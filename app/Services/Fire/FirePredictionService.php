<?php

namespace App\Services\Fire;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirePredictionService
{
    /**
     * URL base de la API externa de predicciones de incendios
     * Se puede configurar en .env como FIRE_PREDICTION_API_URL
     */
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.fire_prediction.url', env('FIRE_PREDICTION_API_URL', ''));
    }

    /**
     * Obtener la predicción de un incendio por su foco_incendio_id
     *
     * @param int $focoIncendioId
     * @return array|null
     */
    public function getPrediction(int $focoIncendioId): ?array
    {
        // Si no hay URL configurada, usar datos simulados
        if (empty($this->apiUrl)) {
            return $this->getSimulatedPrediction($focoIncendioId);
        }

        try {
            $url = rtrim($this->apiUrl, '/') . '/api/predictions';
            $response = Http::timeout(10)->get($url, [
                'foco_incendio_id' => $focoIncendioId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // Buscar la predicción que coincida con el foco_incendio_id
                if (isset($data['data']) && is_array($data['data'])) {
                    foreach ($data['data'] as $prediction) {
                        if (isset($prediction['foco_incendio_id']) && $prediction['foco_incendio_id'] == $focoIncendioId) {
                            return $prediction;
                        }
                        // También verificar si viene dentro de foco_incendio
                        if (isset($prediction['foco_incendio']['id']) && $prediction['foco_incendio']['id'] == $focoIncendioId) {
                            return $prediction;
                        }
                    }
                    // Si no se encuentra, devolver el primero si existe
                    return $data['data'][0] ?? null;
                }
                return null;
            }

            Log::warning('FirePredictionService: Error al obtener predicción', [
                'foco_incendio_id' => $focoIncendioId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('FirePredictionService: Excepción al obtener predicción', [
                'foco_incendio_id' => $focoIncendioId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Obtener predicción simulada basada en los datos JSON proporcionados
     *
     * @param int $focoIncendioId
     * @return array|null
     */
    protected function getSimulatedPrediction(int $focoIncendioId): ?array
    {
        // Para foco_incendio_id = 1
        if ($focoIncendioId == 1) {
            return [
                'id' => 1,
                'foco_incendio_id' => 1,
                'predicted_at' => now()->toIso8601String(),
                'path' => [
                    ['hour' => 0, 'lat' => -17.700097, 'lng' => -60.774792, 'intensity' => 4.59, 'spread_radius_km' => 1.301, 'affected_area_km2' => 5.313, 'perimeter_km' => 8.17, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 1, 'lat' => -17.676761, 'lng' => -60.776364, 'intensity' => 5.05, 'spread_radius_km' => 2.024, 'affected_area_km2' => 12.874, 'perimeter_km' => 12.72, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 2, 'lat' => -17.658059, 'lng' => -60.778366, 'intensity' => 4.96, 'spread_radius_km' => 2.435, 'affected_area_km2' => 18.62, 'perimeter_km' => 15.3, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 3, 'lat' => -17.63882, 'lng' => -60.775177, 'intensity' => 4.91, 'spread_radius_km' => 2.78, 'affected_area_km2' => 24.271, 'perimeter_km' => 17.46, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 4, 'lat' => -17.616355, 'lng' => -60.771852, 'intensity' => 5.39, 'spread_radius_km' => 3.417, 'affected_area_km2' => 36.673, 'perimeter_km' => 21.47, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 5, 'lat' => -17.593143, 'lng' => -60.772535, 'intensity' => 4.71, 'spread_radius_km' => 3.268, 'affected_area_km2' => 33.558, 'perimeter_km' => 20.54, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 6, 'lat' => -17.564554, 'lng' => -60.775254, 'intensity' => 5.52, 'spread_radius_km' => 4.14, 'affected_area_km2' => 53.839, 'perimeter_km' => 26.01, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 7, 'lat' => -17.540731, 'lng' => -60.773521, 'intensity' => 5.99, 'spread_radius_km' => 4.8, 'affected_area_km2' => 72.38, 'perimeter_km' => 30.16, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 8, 'lat' => -17.514868, 'lng' => -60.772844, 'intensity' => 5.02, 'spread_radius_km' => 4.269, 'affected_area_km2' => 57.245, 'perimeter_km' => 26.82, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 9, 'lat' => -17.488418, 'lng' => -60.778375, 'intensity' => 6.17, 'spread_radius_km' => 5.53, 'affected_area_km2' => 96.078, 'perimeter_km' => 34.75, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 10, 'lat' => -17.456885, 'lng' => -60.772515, 'intensity' => 6.26, 'spread_radius_km' => 5.882, 'affected_area_km2' => 108.697, 'perimeter_km' => 36.96, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 11, 'lat' => -17.419232, 'lng' => -60.771657, 'intensity' => 5.51, 'spread_radius_km' => 5.404, 'affected_area_km2' => 91.755, 'perimeter_km' => 33.96, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 12, 'lat' => -17.387175, 'lng' => -60.777072, 'intensity' => 5.75, 'spread_radius_km' => 5.877, 'affected_area_km2' => 108.504, 'perimeter_km' => 36.93, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 13, 'lat' => -17.349131, 'lng' => -60.77657, 'intensity' => 5.88, 'spread_radius_km' => 6.233, 'affected_area_km2' => 122.063, 'perimeter_km' => 39.16, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 14, 'lat' => -17.306019, 'lng' => -60.771653, 'intensity' => 5.75, 'spread_radius_km' => 6.311, 'affected_area_km2' => 125.119, 'perimeter_km' => 39.65, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 15, 'lat' => -17.267315, 'lng' => -60.769949, 'intensity' => 7.05, 'spread_radius_km' => 7.987, 'affected_area_km2' => 200.389, 'perimeter_km' => 50.18, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 16, 'lat' => -17.218115, 'lng' => -60.762256, 'intensity' => 7.26, 'spread_radius_km' => 8.481, 'affected_area_km2' => 225.941, 'perimeter_km' => 53.28, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 17, 'lat' => -17.16441, 'lng' => -60.76821, 'intensity' => 7.92, 'spread_radius_km' => 9.519, 'affected_area_km2' => 284.666, 'perimeter_km' => 59.81, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 18, 'lat' => -17.109858, 'lng' => -60.775509, 'intensity' => 7.97, 'spread_radius_km' => 9.848, 'affected_area_km2' => 304.702, 'perimeter_km' => 61.88, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 19, 'lat' => -17.059204, 'lng' => -60.766569, 'intensity' => 7.1, 'spread_radius_km' => 8.994, 'affected_area_km2' => 254.153, 'perimeter_km' => 56.51, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 20, 'lat' => -17.014582, 'lng' => -60.763797, 'intensity' => 6.09, 'spread_radius_km' => 7.908, 'affected_area_km2' => 196.453, 'perimeter_km' => 49.69, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 21, 'lat' => -16.967786, 'lng' => -60.767114, 'intensity' => 4.52, 'spread_radius_km' => 6.01, 'affected_area_km2' => 113.463, 'perimeter_km' => 37.76, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 22, 'lat' => -16.924637, 'lng' => -60.763461, 'intensity' => 3.61, 'spread_radius_km' => 4.91, 'affected_area_km2' => 75.728, 'perimeter_km' => 30.85, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 23, 'lat' => -16.884002, 'lng' => -60.763342, 'intensity' => 2.52, 'spread_radius_km' => 3.496, 'affected_area_km2' => 38.407, 'perimeter_km' => 21.97, 'extinguished' => false, 'biomasa' => null],
                    ['hour' => 24, 'lat' => -16.842391, 'lng' => -60.765126, 'intensity' => 1.62, 'spread_radius_km' => 2.298, 'affected_area_km2' => 16.592, 'perimeter_km' => 14.44, 'extinguished' => false, 'biomasa' => null],
                ],
                'meta' => [
                    'input_parameters' => [
                        'temperature' => 25,
                        'humidity' => 50,
                        'wind_speed' => 10,
                        'wind_direction' => 0,
                        'prediction_hours' => 24,
                        'terrain_type' => 'pastizal',
                        'initial_intensity' => 5,
                    ],
                    'trajectory' => [
                        ['hour' => 0, 'lat' => -17.700097, 'lng' => -60.774792, 'intensity' => 4.59, 'spread_radius_km' => 1.301, 'affected_area_km2' => 5.313, 'perimeter_km' => 8.17, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 1, 'lat' => -17.676761, 'lng' => -60.776364, 'intensity' => 5.05, 'spread_radius_km' => 2.024, 'affected_area_km2' => 12.874, 'perimeter_km' => 12.72, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 2, 'lat' => -17.658059, 'lng' => -60.778366, 'intensity' => 4.96, 'spread_radius_km' => 2.435, 'affected_area_km2' => 18.62, 'perimeter_km' => 15.3, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 3, 'lat' => -17.63882, 'lng' => -60.775177, 'intensity' => 4.91, 'spread_radius_km' => 2.78, 'affected_area_km2' => 24.271, 'perimeter_km' => 17.46, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 4, 'lat' => -17.616355, 'lng' => -60.771852, 'intensity' => 5.39, 'spread_radius_km' => 3.417, 'affected_area_km2' => 36.673, 'perimeter_km' => 21.47, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 5, 'lat' => -17.593143, 'lng' => -60.772535, 'intensity' => 4.71, 'spread_radius_km' => 3.268, 'affected_area_km2' => 33.558, 'perimeter_km' => 20.54, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 6, 'lat' => -17.564554, 'lng' => -60.775254, 'intensity' => 5.52, 'spread_radius_km' => 4.14, 'affected_area_km2' => 53.839, 'perimeter_km' => 26.01, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 7, 'lat' => -17.540731, 'lng' => -60.773521, 'intensity' => 5.99, 'spread_radius_km' => 4.8, 'affected_area_km2' => 72.38, 'perimeter_km' => 30.16, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 8, 'lat' => -17.514868, 'lng' => -60.772844, 'intensity' => 5.02, 'spread_radius_km' => 4.269, 'affected_area_km2' => 57.245, 'perimeter_km' => 26.82, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 9, 'lat' => -17.488418, 'lng' => -60.778375, 'intensity' => 6.17, 'spread_radius_km' => 5.53, 'affected_area_km2' => 96.078, 'perimeter_km' => 34.75, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 10, 'lat' => -17.456885, 'lng' => -60.772515, 'intensity' => 6.26, 'spread_radius_km' => 5.882, 'affected_area_km2' => 108.697, 'perimeter_km' => 36.96, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 11, 'lat' => -17.419232, 'lng' => -60.771657, 'intensity' => 5.51, 'spread_radius_km' => 5.404, 'affected_area_km2' => 91.755, 'perimeter_km' => 33.96, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 12, 'lat' => -17.387175, 'lng' => -60.777072, 'intensity' => 5.75, 'spread_radius_km' => 5.877, 'affected_area_km2' => 108.504, 'perimeter_km' => 36.93, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 13, 'lat' => -17.349131, 'lng' => -60.77657, 'intensity' => 5.88, 'spread_radius_km' => 6.233, 'affected_area_km2' => 122.063, 'perimeter_km' => 39.16, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 14, 'lat' => -17.306019, 'lng' => -60.771653, 'intensity' => 5.75, 'spread_radius_km' => 6.311, 'affected_area_km2' => 125.119, 'perimeter_km' => 39.65, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 15, 'lat' => -17.267315, 'lng' => -60.769949, 'intensity' => 7.05, 'spread_radius_km' => 7.987, 'affected_area_km2' => 200.389, 'perimeter_km' => 50.18, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 16, 'lat' => -17.218115, 'lng' => -60.762256, 'intensity' => 7.26, 'spread_radius_km' => 8.481, 'affected_area_km2' => 225.941, 'perimeter_km' => 53.28, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 17, 'lat' => -17.16441, 'lng' => -60.76821, 'intensity' => 7.92, 'spread_radius_km' => 9.519, 'affected_area_km2' => 284.666, 'perimeter_km' => 59.81, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 18, 'lat' => -17.109858, 'lng' => -60.775509, 'intensity' => 7.97, 'spread_radius_km' => 9.848, 'affected_area_km2' => 304.702, 'perimeter_km' => 61.88, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 19, 'lat' => -17.059204, 'lng' => -60.766569, 'intensity' => 7.1, 'spread_radius_km' => 8.994, 'affected_area_km2' => 254.153, 'perimeter_km' => 56.51, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 20, 'lat' => -17.014582, 'lng' => -60.763797, 'intensity' => 6.09, 'spread_radius_km' => 7.908, 'affected_area_km2' => 196.453, 'perimeter_km' => 49.69, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 21, 'lat' => -16.967786, 'lng' => -60.767114, 'intensity' => 4.52, 'spread_radius_km' => 6.01, 'affected_area_km2' => 113.463, 'perimeter_km' => 37.76, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 22, 'lat' => -16.924637, 'lng' => -60.763461, 'intensity' => 3.61, 'spread_radius_km' => 4.91, 'affected_area_km2' => 75.728, 'perimeter_km' => 30.85, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 23, 'lat' => -16.884002, 'lng' => -60.763342, 'intensity' => 2.52, 'spread_radius_km' => 3.496, 'affected_area_km2' => 38.407, 'perimeter_km' => 21.97, 'extinguished' => false, 'biomasa' => null],
                        ['hour' => 24, 'lat' => -16.842391, 'lng' => -60.765126, 'intensity' => 1.62, 'spread_radius_km' => 2.298, 'affected_area_km2' => 16.592, 'perimeter_km' => 14.44, 'extinguished' => false, 'biomasa' => null],
                    ],
                    'biomasas_encountered' => [],
                    'total_biomasas_crossed' => 0,
                    'fire_extinguished' => false,
                    'actual_duration_hours' => 24,
                    'extinguished_early' => false,
                    'fire_risk_index' => 50,
                    'spread_speed_kmh' => 1.42,
                    'terrain_factor' => 1,
                    'final_position' => [
                        'lat' => -16.842391,
                        'lng' => -60.765126,
                        'intensity' => 1.62,
                    ],
                    'total_distance_km' => 97.41,
                    'total_area_affected_km2' => 16.59,
                    'final_perimeter_km' => 14.44,
                    'max_spread_radius_km' => 2.3,
                    'containment_probability' => 45.3,
                    'danger_level' => 'ALTO',
                    'propagation_rate' => 'RÁPIDA',
                    'estimated_resources' => [
                        'firefighters' => 34,
                        'fire_trucks' => 12,
                        'helicopters' => 2,
                        'water_needed_liters' => 165925,
                        'estimated_cost_usd' => 22800,
                    ],
                    'recommendations' => [
                        '⚠️ Riesgo ALTO: Monitoreo constante requerido',
                        '📏 Área extensa: Dividir zona en sectores de control',
                        '💧 Mantener provisiones de agua constantes',
                        '📡 Establecer comunicación permanente entre equipos',
                    ],
                    'perimeter_growth_timeline' => [8.17, 12.72, 15.3, 17.46, 21.47, 20.54, 26.01, 30.16, 26.82, 34.75, 36.96, 33.96, 36.93, 39.16, 39.65, 50.18, 53.28, 59.81, 61.88, 56.51, 49.69, 37.76, 30.85, 21.97, 14.44],
                    'algorithm_version' => '1.0',
                    'prediction_confidence' => 0.85,
                    'generated_at' => now()->toIso8601String(),
                ],
                'created_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
                'foco_incendio' => [
                    'id' => 1,
                    'fecha' => now()->subHours(5)->toIso8601String(),
                    'ubicacion' => 'San Jose de Chiquitos',
                    'coordenadas' => '[-17.718397,-60.774994]',
                    'intensidad' => 5,
                    'created_at' => now()->subHours(5)->toIso8601String(),
                    'updated_at' => now()->subHours(1)->toIso8601String(),
                ],
            ];
        }

        // Para otros IDs, devolver null o datos simulados adicionales
        return null;
    }

    /**
     * Obtener todas las predicciones disponibles
     *
     * @return array
     */
    public function getAllPredictions(): array
    {
        // Si no hay URL configurada, usar datos simulados
        if (empty($this->apiUrl)) {
            $prediction = $this->getSimulatedPrediction(1);
            return $prediction ? [$prediction] : [];
        }

        try {
            $url = rtrim($this->apiUrl, '/') . '/api/predictions';
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }

            Log::warning('FirePredictionService: Error al obtener predicciones', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('FirePredictionService: Excepción al obtener predicciones', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}

