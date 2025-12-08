<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Person;
use App\Models\AnimalHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ReportRequest;
use App\Services\Animal\AnimalTransferTransactionalService;
use App\Services\Report\ReportUrgencyService;

class ReportApiController extends Controller
{
    public function __construct(
        private readonly AnimalTransferTransactionalService $transferService,
        private readonly ReportUrgencyService $urgencyService
    ) {
        // Permitir store sin autenticación para endpoints externos
        $this->middleware('auth:sanctum')->except(['store']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Report::latest()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request)
    {
        $data = $request->validated();

        // persona_id del usuario logueado (si está autenticado)
        // Si no está autenticado (endpoint externo), persona_id será null
        if (Auth::check()) {
            $personId = Person::where('usuario_id', Auth::id())->value('id');
            $data['persona_id'] = $personId;
        } else {
            $data['persona_id'] = null;
        }
        $data['aprobado'] = 0;


        // imagen
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('reports', 'public');
            $data['imagen_url'] = $path;
        }

        // urgencia
        $data['urgencia'] = $this->urgencyService->compute($data);

        // guardar hallazgo
        $report = Report::create($data);

        // historial
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
        $hist->observaciones = ['texto' => $report->observaciones ?? 'Registro de Hallazgo'];
        $hist->changed_at = $report->created_at;
        $hist->save();

        // traslado inmediato (primer traslado)
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

        return response()->json([
            'message' => 'El hallazgo se registró correctamente.',
            'report'  => $report,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        //
    }
}
