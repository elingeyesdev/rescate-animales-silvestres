<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Person;
use App\Models\Center;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use App\Services\Animal\AnimalTransferTransactionalService;
use App\Services\Report\ReportUrgencyService;
use App\Services\Fire\FocosCalorService;
use App\Models\AnimalCondition;
use App\Models\IncidentType;
use App\Models\AnimalHistory;
use App\Mail\NewReportNotification;

class ReportController extends Controller
{
    public function __construct(
        private readonly AnimalTransferTransactionalService $transferService,
        private readonly ReportUrgencyService $urgencyService,
        private readonly FocosCalorService $focosCalorService
    ) {
        // Permitir create y store sin autenticación (para usuarios anónimos desde landing)
        $this->middleware('auth')->except(['create', 'store']);
        // Solo ciertos roles gestionan reportes en el panel interno
        $this->middleware('role:ciudadano|rescatista|veterinario|encargado|admin')->except(['create', 'store']);
        // Ciudadanos solo pueden ver y crear, no editar ni eliminar
        $this->middleware('role:admin|encargado|rescatista|veterinario')->only(['edit', 'update']);
        // Solo administradores pueden eliminar reportes
        $this->middleware('role:admin')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Report::with(['person', 'condicionInicial', 'incidentType', 'firstTransfer.center'])
            ->orderByDesc('id');

        // Si el usuario es solo ciudadano (sin otros roles), mostrar solo sus hallazgos
        if ($user->hasRole('ciudadano') && !$user->hasAnyRole(['admin', 'encargado', 'rescatista', 'veterinario', 'cuidador'])) {
            $personId = Person::where('usuario_id', $user->id)->value('id');
            if ($personId) {
                $query->where('persona_id', $personId);
            } else {
                // Si no tiene persona asociada, no mostrar nada
                $query->whereRaw('1 = 0');
            }
        }

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

        // Filter options (solo para admin/encargado/veterinario, no para ciudadanos)
        $reporters = collect();
        if (!$user->hasRole('ciudadano') || $user->hasAnyRole(['admin', 'encargado', 'veterinario'])) {
            $reporters = Person::whereIn(
                    'id',
                    Report::select('persona_id')->whereNotNull('persona_id')->distinct()->pluck('persona_id')
                )
                ->orderBy('nombre')
                ->get(['id', 'nombre']);
        }
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
            $isAuthenticated = Auth::check();

            // Si el usuario está autenticado, obtener su persona_id
            if ($isAuthenticated) {
                $personId = Person::where('usuario_id', Auth::id())->value('id');
                if (empty($personId)) {
                    return Redirect::back()
                        ->withInput()
                        ->withErrors(['persona_id' => 'Tu usuario no está vinculado a una persona. Comunícate con el administrador.']);
                }
                $data['persona_id'] = $personId;
            } else {
                // Usuario no autenticado: guardar sin persona_id
                $data['persona_id'] = null;
            }
            
            $data['aprobado'] = 0;
           
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('reports', 'public');
                $data['imagen_url'] = $path;
            }
            // Calcular urgencia
            $data['urgencia'] = $this->urgencyService->compute($data);

            $report = Report::create($data);
            
            // NOTA: No asociamos reportes con focos de calor por ID porque:
            // - La API de NASA FIRMS no proporciona IDs de incendios
            // - Los focos de calor son detecciones independientes
            // - La relación se hace por proximidad geográfica cuando se visualiza en el mapa
            
            $report->load(['person', 'condicionInicial', 'incidentType']);

            // Enviar correo a todos los encargados y administradores
            $adminsAndEncargados = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'encargado']);
            })->get();

            foreach ($adminsAndEncargados as $user) {
                try {
                    Mail::to($user->email)->send(new NewReportNotification($report));
                } catch (\Exception $e) {
                    // Log error pero no interrumpir el flujo
                    \Log::error('Error enviando correo de nuevo reporte: ' . $e->getMessage());
                }
            }

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
                    'imagen_url' => $report->imagen_url,
                    'created_at' => $report->created_at ? $report->created_at->toDateTimeString() : null, // Guardar fecha original del reporte
                ],
            ];
            $hist->observaciones = ['texto' => $report->observaciones ?? 'Registro de hallazgo'];
            $hist->changed_at = $report->created_at;
            $hist->save();

            // Si se marcó traslado inmediato, registrar primer traslado (sin hoja)
            // Solo si hay persona_id (usuario autenticado)
            if ($request->boolean('traslado_inmediato') && $report->persona_id) {
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

            // Si el usuario NO está autenticado, guardar el reporte en sesión y preguntar si quiere conservarlo
            if (!$isAuthenticated) {
                session(['pending_report_id' => $report->id]);
                return Redirect::route('reports.claim')
                    ->with('success', 'El hallazgo se registró correctamente. ¿Deseas conservar este reporte como tuyo?');
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
        $report = Report::with(['firstTransfer.center', 'incidentType'])->findOrFail($id);
        
        // Obtener focos de calor cercanos (por proximidad, no por ID)
        $nearbyFocosCalor = $report->getNearbyFocosCalor(20, 7);

        return view('report.show', compact('report', 'nearbyFocosCalor'));
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

    /**
     * Approve or reject a report.
     */
    public function approve(Request $request, Report $report): RedirectResponse
    {
        // Solo admin y encargado pueden aprobar/rechazar
        if (!Auth::user()->hasAnyRole(['admin', 'encargado'])) {
            abort(403, 'No tienes permiso para aprobar o rechazar hallazgos.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $report->aprobado = $validated['action'] === 'approve' ? 1 : 0;
        $report->save();

        // Registrar en historial si existe
        $hist = AnimalHistory::whereNull('animal_file_id')
            ->whereNotNull('valores_nuevos')
            ->whereRaw("(valores_nuevos->'report'->>'id')::text = ?", [(string)$report->id])
            ->first();

        if ($hist) {
            // Actualizar observaciones con la acción de aprobación/rechazo
            $obs = $hist->observaciones ?? [];
            $obsTexto = is_array($obs) ? ($obs['texto'] ?? '') : (string)$obs;
            $accionTexto = $validated['action'] === 'approve' ? 'Aprobado' : 'Rechazado';
            $obs['texto'] = $obsTexto . ' | ' . $accionTexto . ' por: ' . Auth::user()->person->nombre;
            $hist->observaciones = $obs;
            $hist->save();
        }

        $message = $validated['action'] === 'approve' 
            ? 'El hallazgo ha sido aprobado correctamente.' 
            : 'El hallazgo ha sido rechazado correctamente.';

        // Redirigir a la vista desde donde se llamó (index o show)
        $redirectTo = $request->get('redirect_to', 'reports.index');
        if ($redirectTo === 'show') {
            return Redirect::route('reports.show', $report->id)
                ->with('success', $message);
        }
        
        return Redirect::route('reports.index')
            ->with('success', $message);
    }

    public function destroy($id): RedirectResponse
    {
        Report::find($id)->delete();

        return Redirect::route('reports.index')
            ->with('success', 'El hallazgo se eliminó correctamente');
    }

    /**
     * Mostrar el mapa de campo con todos los hallazgos e incendios
     */
    public function mapaCampo(): View
    {
        $user = Auth::user();
        
        // Solo administradores y encargados pueden acceder
        if (!$user->hasAnyRole(['admin', 'encargado'])) {
            abort(403, 'No tienes permiso para acceder al mapa de campo.');
        }
        
        $query = Report::with(['person', 'condicionInicial', 'incidentType'])
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderByDesc('id');

        $reports = $query->get()->map(function ($report) {
            return [
                'id' => $report->id,
                'latitud' => $report->latitud,
                'longitud' => $report->longitud,
                'urgencia' => $report->urgencia,
                'incendio_id' => $report->incendio_id,
                'direccion' => $report->direccion,
                'condicion_inicial' => $report->condicionInicial ? [
                    'nombre' => $report->condicionInicial->nombre,
                ] : null,
                'incident_type' => $report->incidentType ? [
                    'nombre' => $report->incidentType->nombre,
                ] : null,
            ];
        });

        // SIEMPRE agregar reporte simulado de incendio para demostración
        // Este reporte es independiente y siempre se muestra con su predicción
        // Es un módulo separado que coexiste con los datos reales
        $reports->push([
            'id' => 'simulado',
            'latitud' => '-17.718397',
            'longitud' => '-60.774994',
            'urgencia' => 5,
            'incendio_id' => 1, // ID para cargar la predicción desde la API
            'direccion' => 'San Jose de Chiquitos, Santa Cruz, Bolivia',
            'condicion_inicial' => [
                'nombre' => 'Hallazgo en incendio (Simulación)',
            ],
            'incident_type' => [
                'nombre' => 'Incendio forestal',
            ],
        ]);

        // Obtener focos de calor reales desde la BD (en lugar de llamar a la API)
        // Esto es mucho más eficiente y no excede los límites de la API
        $focosCalor = $this->focosCalorService->getRecentHotspots(2);
        $focosCalorFormatted = $this->focosCalorService->formatForMap($focosCalor);

        return view('report.mapa-campo', compact('reports', 'focosCalorFormatted'));
    }

    /**
     * Mostrar página para reclamar el reporte (si el usuario no estaba autenticado)
     */
    public function claim(): View
    {
        $reportId = session('pending_report_id');
        if (!$reportId) {
            return view('reports.claim', ['report' => null]);
        }

        $report = Report::with(['condicionInicial', 'incidentType'])->find($reportId);
        return view('reports.claim', compact('report'));
    }

    /**
     * Procesar la decisión del usuario sobre conservar el reporte
     */
    public function claimStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:yes,no',
        ]);

        $reportId = session('pending_report_id');
        if (!$reportId) {
            return Redirect::route('landing')
                ->with('info', 'No hay reportes pendientes de asociar.');
        }

        if ($validated['action'] === 'no') {
            // El usuario no quiere conservar el reporte, limpiar sesión y redirigir
            session()->forget('pending_report_id');
            return Redirect::route('landing')
                ->with('success', 'El reporte se ha registrado correctamente. Gracias por tu colaboración.');
        }

        // El usuario quiere conservar el reporte, mantener en sesión y redirigir a login
        // El reporte ya está en sesión, solo redirigir
        return Redirect::route('login')
            ->with('info', 'Por favor, inicia sesión o regístrate para asociar este reporte a tu cuenta.');
    }
}
