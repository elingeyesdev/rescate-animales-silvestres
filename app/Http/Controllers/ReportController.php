<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Person;
use App\Models\Center;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\Animal\AnimalTransferTransactionalService;
use App\Services\Report\ReportUrgencyService;
use App\Models\AnimalCondition;
use App\Models\IncidentType;
use App\Models\AnimalHistory;

class ReportController extends Controller
{
    public function __construct(
        private readonly AnimalTransferTransactionalService $transferService,
        private readonly ReportUrgencyService $urgencyService
    ) {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Report::with(['person', 'condicionInicial', 'incidentType', 'firstTransfer.center'])
            ->orderByDesc('id');

        // Filters
        if ($request->filled('urgencia_nivel')) {
            $nivel = $request->string('urgencia_nivel')->toString();
            if ($nivel === 'alta') {
                // 4-5
                $query->where('urgencia', '>=', 4);
            } elseif ($nivel === 'media') {
                // 3
                $query->where('urgencia', 3);
            } elseif ($nivel === 'baja') {
                // 1-2
                $query->where('urgencia', '<=', 2);
            }
        }
        if ($request->filled('persona_id')) {
            $query->where('persona_id', $request->input('persona_id'));
        }
        if ($request->filled('tipo_incidente_id')) {
            $query->where('tipo_incidente_id', $request->input('tipo_incidente_id'));
        }
        if ($request->filled('aprobado')) {
            // aprobado can be '1' or '0'
            $query->where('aprobado', (int) $request->input('aprobado'));
        }

        $reports = $query->paginate(12)->withQueryString();

        // Filter options
        $reporters = Person::whereIn(
                'id',
                Report::select('persona_id')->whereNotNull('persona_id')->distinct()->pluck('persona_id')
            )
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
        $incidentTypes = IncidentType::where('activo', true)->orderBy('nombre')->get(['id','nombre']);

        return view('report.index', compact('reports', 'reporters', 'incidentTypes'))
            ->with('i', ($request->input('page', 1) - 1) * $reports->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $report = new Report();

        $centers = Center::orderBy('nombre')->get(['id','nombre','latitud','longitud']);
        $conditions = AnimalCondition::where('activo', true)->orderBy('nombre')->get(['id','nombre']);
        $incidentTypes = IncidentType::where('activo', true)->orderBy('nombre')->get(['id','nombre']);

        return view('report.create', compact('report','centers','conditions','incidentTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Persona del usuario autenticado (obligatoria para guardar)
            $personId = Person::where('usuario_id', Auth::id())->value('id');
            if (empty($personId)) {
                return Redirect::back()
                    ->withInput()
                    ->withErrors(['persona_id' => 'Tu usuario no está vinculado a una persona. Comunícate con el administrador.']);
            }
            $data['persona_id'] = $personId;
            $data['aprobado'] = 0;
            // Default cantidad si no viene
            if (empty($data['cantidad_animales'])) {
                $data['cantidad_animales'] = 1;
            }

            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('reports', 'public');
                $data['imagen_url'] = $path;
            }
            // Calcular urgencia
            $data['urgencia'] = $this->urgencyService->compute($data);

            $report = Report::create($data);

            // Registrar evento de reporte en el historial (sin hoja)
            $hist = new AnimalHistory();
            $hist->animal_file_id = null;
            $hist->valores_antiguos = null;
            $hist->valores_nuevos = [
                'report' => [
                    'id' => $report->id,
                    'persona_id' => $report->persona_id,
                    'direccion' => $report->direccion,
                    'latitud' => $report->latitud,
                    'longitud' => $report->longitud,
                    'condicion_inicial_id' => $report->condicion_inicial_id,
                    'tipo_incidente_id' => $report->tipo_incidente_id,
                    'tamano' => $report->tamano,
                    'puede_moverse' => $report->puede_moverse,
                    'urgencia' => $report->urgencia,
                    'cantidad_animales' => $report->cantidad_animales,
                    'imagen_url' => $report->imagen_url,
                ],
            ];
            $hist->observaciones = ['texto' => $report->observaciones ?? 'Registro de hallazgo'];
            $hist->changed_at = $report->created_at;
            $hist->save();

            // Si se marcó traslado inmediato, registrar primer traslado (sin hoja)
            if ($request->boolean('traslado_inmediato')) {
                $tData = [
                    'persona_id' => $report->persona_id,
                    'centro_id' => $request->input('centro_id'),
                    'observaciones' => $report->observaciones,
                    'primer_traslado' => true,
                    'animal_id' => null,
                    'latitud' => $report->latitud,
                    'longitud' => $report->longitud,
                    'reporte_id' => $report->id,
                ];
                $this->transferService->create($tData);
            }

            return Redirect::route('reports.index')
                ->with('success', 'El hallazgo se registró correctamente.');
        } catch (\Throwable $e) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['general' => 'No se pudo registrar el hallazgo: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $report = Report::with(['firstTransfer.center'])->findOrFail($id);

        return view('report.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $report = Report::find($id);

        $conditions = AnimalCondition::where('activo', true)->orderBy('nombre')->get(['id','nombre']);
        $incidentTypes = IncidentType::where('activo', true)->orderBy('nombre')->get(['id','nombre']);

        return view('report.edit', compact('report','conditions','incidentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReportRequest $request, Report $report): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('reports', 'public');
            $data['imagen_url'] = $path;
        }
        // Recalcular urgencia si cambian parámetros
        $data['urgencia'] = $this->urgencyService->compute(array_merge($report->toArray(), $data));

        $report->update($data);

        return Redirect::route('reports.index')
            ->with('success', 'El hallazgo se actualizó correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        Report::find($id)->delete();

        return Redirect::route('reports.index')
            ->with('success', 'El hallazgo se eliminó correctamente');
    }
}
