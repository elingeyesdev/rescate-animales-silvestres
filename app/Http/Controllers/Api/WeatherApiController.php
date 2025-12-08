<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Weather\OpenMeteoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WeatherApiController extends Controller
{
    public function __construct(
        private readonly OpenMeteoService $openMeteoService
    ) {
        // Permitir acceso sin autenticación para endpoints externos
        // $this->middleware('auth:sanctum');
    }

    /**
     * Obtener datos meteorológicos para una ubicación específica
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Validar parámetros de entrada
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Parámetros inválidos',
                'message' => 'Se requieren latitude y longitude válidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $latitude = (float) $request->input('latitude');
        $longitude = (float) $request->input('longitude');

        $weatherData = $this->openMeteoService->getWeather($latitude, $longitude);

        if ($weatherData === null) {
            return response()->json([
                'error' => 'No se pudieron obtener los datos meteorológicos',
                'message' => 'Error al consultar la API de OpenMeteo',
            ], 500);
        }

        return response()->json($weatherData);
    }
}

