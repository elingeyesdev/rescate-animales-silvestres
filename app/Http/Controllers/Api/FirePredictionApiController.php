<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Fire\FirePredictionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FirePredictionApiController extends Controller
{
    public function __construct(
        private readonly FirePredictionService $firePredictionService
    ) {
        // Permitir acceso sin autenticación si es necesario, o agregar middleware según requerimientos
        // $this->middleware('auth:sanctum');
    }

    /**
     * Obtener predicciones de incendios
     * Permite filtrar por foco_incendio_id
     */
    public function index(Request $request): JsonResponse
    {
        $focoIncendioId = $request->input('foco_incendio_id');

        if ($focoIncendioId) {
            $prediction = $this->firePredictionService->getPrediction((int) $focoIncendioId);
            
            if ($prediction) {
                return response()->json([
                    'data' => [$prediction]
                ]);
            }

            return response()->json([
                'data' => []
            ]);
        }

        // Si no se especifica foco_incendio_id, devolver todas las predicciones
        $predictions = $this->firePredictionService->getAllPredictions();

        return response()->json([
            'data' => $predictions
        ]);
    }
}


