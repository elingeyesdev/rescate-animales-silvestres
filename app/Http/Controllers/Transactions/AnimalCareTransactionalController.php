<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\CareProcessRequest;
use App\Models\AnimalFile;
use App\Models\AnimalHistory;
use App\Models\CareType;
use App\Services\Animal\AnimalCareTransactionalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class AnimalCareTransactionalController extends Controller
{
	public function __construct(
		private readonly AnimalCareTransactionalService $service
	) {
		$this->middleware('auth');
	}

	public function create(): View
	{
		$animalFiles = AnimalFile::with('animal')
            ->leftJoin('releases', 'releases.animal_file_id', '=', 'animal_files.id')
            ->whereNull('releases.animal_file_id')
            ->orderByDesc('animal_files.id')
            ->get(['animal_files.id','animal_files.animal_id']);
		$careTypes = CareType::orderBy('nombre')->get(['id','nombre']);

        // Obtener resumen de la última actualización por hoja de animal
        foreach ($animalFiles as $af) {
            $last = AnimalHistory::where('animal_file_id', $af->id)
                ->orderByDesc('changed_at')
                ->orderByDesc('id')
                ->first(['changed_at','observaciones','valores_nuevos']);

            $summary = null;
            if ($last) {
                $new = $last->valores_nuevos;
                if (!is_array($new)) {
                    $decoded = json_decode((string)$new, true);
                    $new = is_array($decoded) ? $decoded : [];
                }

                // Prioridad: evaluación médica -> cuidado -> cambio de estado -> traslado -> fallback observaciones
                if (!empty($new['evaluacion_medica'])) {
                    $em = $new['evaluacion_medica'];
                    $pieces = [];
                    if (!empty($em['diagnostico'])) {
                        $pieces[] = 'Dx: '.$em['diagnostico'];
                    }
                    if (!empty($em['tratamiento_texto'])) {
                        $pieces[] = $em['tratamiento_texto'];
                    }
                    if (!empty($em['recomendacion'])) {
                        $pieces[] = 'Recomendación: '.$em['recomendacion'];
                    }
                    if (!empty($em['apto_traslado'])) {
                        $map = [
                            'si' => 'Apto para traslado',
                            'no' => 'No apto para traslado',
                            'con_restricciones' => 'Traslado con restricciones',
                        ];
                        $pieces[] = $map[$em['apto_traslado']] ?? ('Apto traslado: '.$em['apto_traslado']);
                    }
                    $summary = implode(' | ', array_filter($pieces));
                } elseif (!empty($new['care'])) {
                    $care = $new['care'];
                    $summary = !empty($care['descripcion'])
                        ? 'Cuidado: '.$care['descripcion']
                        : null;
                } elseif (!empty($new['estado'])) {
                    $estado = $new['estado'];
                    $summary = !empty($estado['nombre'])
                        ? 'Estado: '.$estado['nombre']
                        : null;
                } elseif (!empty($new['transfer'])) {
                    $tr = $new['transfer'];
                    if (!empty($tr['centro_id'])) {
                        $summary = 'Traslado a centro ID '.$tr['centro_id'];
                    }
                }

                // Fallback: observaciones si no se pudo armar un resumen
                if (!$summary) {
                    $obs = $last->observaciones;
                    if (is_array($obs)) {
                        $obs = $obs['texto'] ?? json_encode($obs, JSON_UNESCAPED_UNICODE);
                    }
                    $summary = $obs ? (string)$obs : null;
                }
            }

            $af->last_summary = $summary;
        }

		return view('transactions.animal.care.create', compact('animalFiles','careTypes'));
	}

	public function store(CareProcessRequest $request): RedirectResponse
	{
		$data = $request->validated();
		$image = $request->file('imagen');

		$this->service->registerCare($data, $image);

		return Redirect::route('cares.index')
			->with('success', 'Cuidado registrado correctamente.');
	}
}






