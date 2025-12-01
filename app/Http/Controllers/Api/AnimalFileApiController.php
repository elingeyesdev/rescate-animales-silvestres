<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\AnimalWithFileRequest;
use App\Models\AnimalFile;
use App\Models\AnimalStatus;
use App\Models\Report;
use App\Services\Animal\AnimalTransactionalService;
use Illuminate\Http\JsonResponse;

class AnimalFileApiController extends Controller
{
    public function __construct(
        private readonly AnimalTransactionalService $service
    ) {
        // Protegido con Sanctum igual que el resto del API "público" para la app móvil
        $this->middleware('auth:sanctum');
    }

    /**
     * Listado simple de Hojas de Vida.
     */
    public function index(): JsonResponse
    {
        $files = AnimalFile::with(['animal', 'species', 'animalStatus', 'center'])
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json($files);
    }

    /**
     * Crear Animal + Hoja de Vida en una sola operación transaccional.
     *
     * Reutiliza la misma lógica de historial de AnimalTransactionalService.
     */
    public function store(AnimalWithFileRequest $request): JsonResponse
    {
        try {
            $animalData = $request->only([
                'nombre',
                'sexo',
                'descripcion',
                'reporte_id',
                'llegaron_cantidad',
                'estado_inicial_id',
            ]);
            $animalFileData = $request->only([
                'especie_id',
                'estado_id',
            ]);
            $image = $request->file('imagen');

            // Misma regla que en el controlador web: mapear estado desde la condición inicial del reporte
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

                // Copiar observaciones a descripción si no vienen desde la app
                if (empty($animalData['descripcion']) && isset($rep) && !empty($rep->observaciones)) {
                    $animalData['descripcion'] = $rep->observaciones;
                }
            }

            $result = $this->service->createWithFile($animalData, $animalFileData, $image);

            return response()->json([
                'message'    => 'Animal y Hoja de Vida creados correctamente.',
                'animal'     => $result['animal']->fresh(),
                'animalFile' => $result['animalFile']->load(['animal', 'species', 'animalStatus', 'center']),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'No se pudo registrar la Hoja de Vida.',
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Detalle de una Hoja de Vida.
     */
    public function show(AnimalFile $animalFile): JsonResponse
    {
        return response()->json(
            $animalFile->load(['animal', 'species', 'animalStatus', 'center'])
        );
    }
}


