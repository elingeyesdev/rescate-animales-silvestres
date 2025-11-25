<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\AnimalWithFileRequest;
use App\Models\Animal;
use App\Models\AnimalFile;
use App\Models\AnimalStatus;
use App\Models\AnimalType;
use App\Models\Report;
use App\Models\Species;
	use App\Models\AnimalHistory;
use App\Services\Animal\AnimalTransactionalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AnimalTransactionalController extends Controller
{
	public function __construct(
		private readonly AnimalTransactionalService $service
	) {
		$this->middleware('auth');
	}

	/**
	 * Formulario combinado para crear Animal + Hoja de Animal en una sola operación.
	 */
	public function create(): View
	{
		$animal = new Animal();
		$animalFile = new AnimalFile();

		// Defaults
		$wildTypeId = AnimalType::whereRaw('LOWER(nombre) = ?', ['silvestre'])->value('id');
		$defaultStatusId = AnimalStatus::whereRaw('LOWER(nombre) = ?', ['en atención'])->value('id');

		// Datos requeridos por el form de Animal (select oculto y tarjetas)
		$reports = Report::query()
			->where('aprobado', 1)
			->leftJoin('animals', 'animals.reporte_id', '=', 'reports.id')
			->groupBy('reports.id', 'reports.cantidad_animales')
			->havingRaw('COUNT(animals.id) < COALESCE(reports.cantidad_animales, 1)')
			->orderByDesc('reports.id')
			->get(['reports.id']);

        $reportCards = Report::query()
            ->where('reports.aprobado', 1)
            ->leftJoin('animals', 'animals.reporte_id', '=', 'reports.id')
            ->leftJoin('people', 'people.id', '=', 'reports.persona_id')
            ->leftJoin('animal_conditions', 'animal_conditions.id', '=', 'reports.condicion_inicial_id')
            ->select([
                'reports.id',
                'reports.cantidad_animales',
                'reports.imagen_url',
                'reports.observaciones',
                DB::raw('COUNT(animals.id) as asignados'),
                DB::raw("COALESCE(people.nombre, '') as reportante_nombre"),
                'reports.condicion_inicial_id',
                DB::raw("COALESCE(animal_conditions.nombre, '') as condicion_nombre"),
            ])
            ->groupBy('reports.id','reports.cantidad_animales','reports.imagen_url','reports.observaciones','people.nombre','reports.condicion_inicial_id','animal_conditions.nombre')
            ->havingRaw('COUNT(animals.id) < COALESCE(reports.cantidad_animales, 1)')
            ->orderByDesc('reports.id')
            ->get();

		// Datos requeridos por el form de AnimalFile (salvo animales)
		$animalTypes = AnimalType::orderBy('nombre')->get(['id','nombre']);
		$species = Species::orderBy('nombre')->get(['id','nombre']);
		$animalStatuses = AnimalStatus::orderBy('nombre')->get(['id','nombre']);

		// Historiales de primer traslado pendientes (sin hoja asignada)
		$pendingTransfers = AnimalHistory::query()
			->whereNull('animal_file_id')
			->whereNotNull('valores_nuevos')
			->whereRaw("(valores_nuevos->'transfer'->>'primer_traslado')::text = 'true'")
			->orderByDesc('id')
			->get(['id','valores_nuevos']);

		return view('transactions.animal.create', compact(
			'animal',
			'animalFile',
			'reports',
			'animalTypes',
			'species',
			'animalStatuses',
			'reportCards',
            'wildTypeId',
            'defaultStatusId'
		));
	}

	/**
	 * Persiste Animal + Hoja de Animal (transaccional).
	 */
	public function store(AnimalWithFileRequest $request): RedirectResponse
	{
		$animalData = $request->only(['nombre','sexo','descripcion','reporte_id','transfer_history_ids','llegaron_cantidad']);
		$animalFileData = $request->only(['tipo_id','especie_id','raza_id','estado_id']);
		$image = $request->file('imagen');

        // Forzar tipo Silvestre por defecto si no viene del formulario
        if (empty($animalFileData['tipo_id'])) {
            $animalFileData['tipo_id'] = AnimalType::whereRaw('LOWER(nombre) = ?', ['silvestre'])->value('id');
        }

        // Si no viene estado, mapear desde la condición inicial del reporte
        if (empty($animalFileData['estado_id']) && !empty($animalData['reporte_id'])) {
            $rep = Report::with('animalCondition')->find($animalData['reporte_id']);
            if ($rep && $rep->animalCondition && $rep->animalCondition->nombre) {
                $status = AnimalStatus::whereRaw('LOWER(nombre) = ?', [mb_strtolower($rep->animalCondition->nombre)])->value('id');
                if ($status) {
                    $animalFileData['estado_id'] = $status;
                } else {
                    // fallback
                    $animalFileData['estado_id'] = AnimalStatus::whereRaw('LOWER(nombre) = ?', ['en atención'])->value('id');
                }
            }
            // Copiar observaciones en descripción si no se envió
            if (empty($animalData['descripcion']) && $rep && !empty($rep->observaciones)) {
                $animalData['descripcion'] = $rep->observaciones;
            }
        }

        $this->service->createWithFile($animalData, $animalFileData, $image);
        $msg = 'Animal y Hoja creados correctamente en una transacción.';

		return Redirect::route('animal-files.index')
			->with('success', $msg);
	}
}


