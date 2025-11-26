<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Rescuer;
use App\Models\Center;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TransferRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Animal;
use App\Services\Animal\AnimalTransferTransactionalService;
use Illuminate\Http\JsonResponse;
use App\Models\Report;
use App\Models\Person;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function __construct(
        private readonly AnimalTransferTransactionalService $transferService
    ) {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Soporte JSON para centro actual via resource index con query params
        if ($request->boolean('current_center') && $request->filled('animal_id')) {
            $animalId = (int) $request->input('animal_id');
            $last = \App\Models\Transfer::where('animal_id', $animalId)->orderByDesc('id')->first();
            $currentCenter = $last?->center;
            $centers = Center::orderBy('nombre')->get(['id','nombre','latitud','longitud']);
            $destinations = $centers->when($currentCenter, function ($c) use ($currentCenter) {
                return $c->where('id', '!=', $currentCenter->id);
            })->values();
            return response()->json([
                'current' => $currentCenter ? [
                    'id' => $currentCenter->id,
                    'nombre' => $currentCenter->nombre,
                    'latitud' => $currentCenter->latitud,
                    'longitud' => $currentCenter->longitud,
                ] : null,
                'destinations' => $destinations,
            ]);
        }

        $tab = $request->string('tab')->toString() ?: 'first';
        $transfers = Transfer::with(['person','center'])->paginate();
        $centers = Center::orderBy('nombre')->get(['id','nombre','latitud','longitud']);
        $people = Person::orderBy('nombre')->get(['id','nombre']);

        // Preparar datos SOLO para la pestaña activa
        $reportsFirst = collect();
        $animalFiles = collect();
        if ($tab === 'first') {
            $firstReportIds = Transfer::where('primer_traslado', true)
                ->whereNotNull('reporte_id')
                ->pluck('reporte_id')
                ->all();
            $reportsWithAnimal = Animal::whereNotNull('reporte_id')->pluck('reporte_id')->all();
            $reportsFirst = Report::with(['person','condicionInicial'])
                ->where('aprobado', true)
                ->when(!empty($firstReportIds), fn($q) => $q->whereNotIn('id', $firstReportIds))
                ->when(!empty($reportsWithAnimal), fn($q) => $q->whereNotIn('id', $reportsWithAnimal))
                ->orderByDesc('id')
                ->take(12)
                ->get(['id','persona_id','condicion_inicial_id','aprobado','created_at','direccion','imagen_url']);
        } elseif ($tab === 'internal') {
            $animalFiles = \App\Models\AnimalFile::with(['animal:id,nombre', 'center:id,nombre'])
                ->leftJoin('releases', 'releases.animal_file_id', '=', 'animal_files.id')
                ->whereNull('releases.animal_file_id')
                ->orderByDesc('animal_files.id')
                ->get(['animal_files.id','animal_files.animal_id','animal_files.imagen_url','animal_files.centro_id']);
        }
        $transfer = new Transfer();

        return view('transfer.index', compact('transfers','reportsFirst','centers','people','animalFiles','transfer','tab'))
            ->with('i', ($request->input('page', 1) - 1) * $transfers->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $transfer = new Transfer();
        $rescuers = Rescuer::with('person')->where('aprobado', true)->orderBy('id')->get();
        $centers = Center::orderBy('nombre')->get(['id','nombre','latitud','longitud']);
        $animals = Animal::orderByDesc('id')->get(['id','nombre']);
        $people = \App\Models\Person::orderBy('nombre')->get(['id','nombre']);
        return view('transfer.create', compact('transfer','rescuers','centers','animals','people'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransferRequest $request): RedirectResponse
    {
        $data = $request->validated();
        try {
            // Modo "primer traslado" si viene report_id y NO viene animal_id
            if (!empty($data['report_id']) && empty($data['animal_id'])) {
                $report = Report::findOrFail($data['report_id']);
                if ((int)$report->aprobado !== 1) {
                    return Redirect::back()->with('error', 'El hallazgo no está aprobado.');
                }
                // Evitar duplicado de primer traslado
                $exists = \App\Models\Transfer::where('primer_traslado', true)
                    ->where('reporte_id', $report->id)
                    ->exists();
                if ($exists) {
                    return Redirect::back()->with('error', 'Este hallazgo ya tiene un primer traslado.');
                }
                $personId = Person::where('usuario_id', Auth::id())->value('id');
                $payload = [
                    'persona_id' => $personId,
                    'reporte_id' => $report->id,
                    'centro_id' => $data['centro_id'],
                    'observaciones' => $data['observaciones'] ?? $report->observaciones,
                    'primer_traslado' => true,
                    'animal_id' => null,
                    'latitud' => $report->latitud,
                    'longitud' => $report->longitud,
                ];
                $this->transferService->create($payload);
                return Redirect::route('reports.index')
                    ->with('success', 'Primer traslado registrado correctamente.');
            }
            // Modo traslado interno (entre centros) usando animal_id
            // Derivar animal_id desde animal_file_id si corresponde
            if (empty($data['animal_id']) && !empty($data['animal_file_id'])) {
                $data['animal_id'] = \App\Models\AnimalFile::where('id', $data['animal_file_id'])->value('animal_id');
            }
            $personId = $data['persona_id'] ?? Person::where('usuario_id', Auth::id())->value('id');
            $payload = array_merge($data, [
                'persona_id' => $personId,
                'primer_traslado' => false,
            ]);
            $this->transferService->create($payload);
        } catch (\Throwable $e) {
            return Redirect::back()->withInput()->with('error', 'No se pudo registrar el traslado: '.$e->getMessage());
        }

        return Redirect::route('transfers.index')
            ->with('success', 'Transferencia creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $transfer = Transfer::find($id);

        return view('transfer.show', compact('transfer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $transfer = Transfer::find($id);
        $rescuers = Rescuer::with('person')->where('aprobado', true)->orderBy('id')->get();
        $centers = Center::orderBy('nombre')->get(['id','nombre']);
        $animals = Animal::orderByDesc('id')->get(['id','nombre']);
        $people = \App\Models\Person::orderBy('nombre')->get(['id','nombre']);
        return view('transfer.edit', compact('transfer','rescuers','centers','animals','people'));
    }

    // Método específico eliminado; el primer traslado se maneja en store() del resource

    // Eliminado: currentCenterByAnimal. Usar index() con params.
    /**
     * Update the specified resource in storage.
     */
    public function update(TransferRequest $request, Transfer $transfer): RedirectResponse
    {
        $transfer->update($request->validated());

        return Redirect::route('transfers.index')
            ->with('success', 'Transferencia actualizada correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        Transfer::find($id)->delete();

        return Redirect::route('transfers.index')
            ->with('success', 'Transferencia eliminada correctamente');
    }
}
