<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Models\Transfer;
use App\Models\Animal;
use App\Models\Person;
use App\Models\Report;
use App\Models\Center;
use App\Services\Animal\AnimalTransferTransactionalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferApiController extends Controller
{
    public function __construct(
        private readonly AnimalTransferTransactionalService $transferService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Listado de traslados.
     * Soporta filtros por ?animal_id= y ?report_id= y paginación estándar.
     */
    public function index(Request $request): JsonResponse
    {
        // Endpoint auxiliar para conocer centro actual/destinos de un animal (igual que en TransferController@index)
        if ($request->boolean('current_center') && $request->filled('animal_id')) {
            $animalId = (int) $request->input('animal_id');
            $last = Transfer::where('animal_id', $animalId)->orderByDesc('id')->first();
            $currentCenter = $last?->center;
            $centers = Center::orderBy('nombre')->get(['id','nombre','latitud','longitud']);
            $destinations = $centers->when($currentCenter, function ($c) use ($currentCenter) {
                return $c->where('id', '!=', $currentCenter->id);
            })->values();

            return response()->json([
                'current'      => $currentCenter ? [
                    'id'       => $currentCenter->id,
                    'nombre'   => $currentCenter->nombre,
                    'latitud'  => $currentCenter->latitud,
                    'longitud' => $currentCenter->longitud,
                ] : null,
                'destinations' => $destinations,
            ]);
        }

        $query = Transfer::with(['person', 'center', 'report'])
            ->orderByDesc('id');

        if ($request->filled('animal_id')) {
            $query->where('animal_id', (int) $request->input('animal_id'));
        }
        if ($request->filled('report_id')) {
            $query->where('reporte_id', (int) $request->input('report_id'));
        }

        $transfers = $query->paginate(20);

        return response()->json($transfers);
    }

    /**
     * Crea un traslado (primer traslado o traslado interno) usando el servicio transaccional.
     */
    public function store(TransferRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            // Modo "primer traslado" si viene report_id y NO viene animal_id
            if (!empty($data['report_id']) && empty($data['animal_id'])) {
                $report = Report::findOrFail($data['report_id']);
                if ((int) $report->aprobado !== 1) {
                    return response()->json([
                        'message' => 'El hallazgo no está aprobado.',
                    ], 422);
                }
                // Evitar duplicado de primer traslado
                $exists = Transfer::where('primer_traslado', true)
                    ->where('reporte_id', $report->id)
                    ->exists();
                if ($exists) {
                    return response()->json([
                        'message' => 'Este hallazgo ya tiene un primer traslado.',
                    ], 422);
                }
                $personId = Person::where('usuario_id', Auth::id())->value('id');
                $payload = [
                    'persona_id'      => $personId,
                    'reporte_id'      => $report->id,
                    'centro_id'       => $data['centro_id'],
                    'observaciones'   => $data['observaciones'] ?? $report->observaciones,
                    'primer_traslado' => true,
                    'animal_id'       => null,
                    'latitud'         => $report->latitud,
                    'longitud'        => $report->longitud,
                ];
                $transfer = $this->transferService->create($payload);

                return response()->json([
                    'message'  => 'Primer traslado registrado correctamente.',
                    'transfer' => $transfer->load(['person', 'center', 'report']),
                ], 201);
            }

            // Modo traslado interno (entre centros) usando animal_id
            // Derivar animal_id desde animal_file_id si corresponde
            if (empty($data['animal_id']) && !empty($data['animal_file_id'])) {
                $data['animal_id'] = \App\Models\AnimalFile::where('id', $data['animal_file_id'])->value('animal_id');
            }
            $personId = $data['persona_id'] ?? Person::where('usuario_id', Auth::id())->value('id');
            $payload = array_merge($data, [
                'persona_id'      => $personId,
                'primer_traslado' => false,
            ]);
            $transfer = $this->transferService->create($payload);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'No se pudo registrar el traslado.',
                'error'   => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message'  => 'Traslado creado correctamente.',
            'transfer' => $transfer->load(['person', 'center', 'report']),
        ], 201);
    }

    /**
     * Detalle de un traslado.
     */
    public function show(Transfer $transfer): JsonResponse
    {
        return response()->json(
            $transfer->load(['person', 'center', 'report'])
        );
    }
}


