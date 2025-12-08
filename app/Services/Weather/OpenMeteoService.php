<?php

namespace App\Services\Weather;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoService
{
    /**
     * URL base de la API de OpenMeteo
     */
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.open_meteo.api_url', 'https://api.open-meteo.com/v1/forecast');
    }

    /**
     * Obtener datos meteorológicos para una ubicación específica
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    public function getWeather(float $latitude, float $longitude): ?array
    {
        try {
            $response = Http::timeout(10)->get($this->apiUrl, [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current_weather' => true,
                'hourly' => 'relativehumidity_2m,precipitation',
                'timezone' => 'auto',
            ]);

            if (!$response->successful()) {
                Log::warning('OpenMeteoService: Error al obtener datos meteorológicos', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();

            // Validar que la respuesta tenga la estructura esperada
            if (!isset($data['current_weather'])) {
                Log::warning('OpenMeteoService: Respuesta sin current_weather', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'response' => $data,
                ]);

                return null;
            }

            $currentWeather = $data['current_weather'];
            
            // Obtener humedad y precipitación del primer elemento de hourly (hora actual)
            $humidity = null;
            $precipitation = 0;
            
            if (isset($data['hourly']) && isset($data['hourly']['time']) && count($data['hourly']['time']) > 0) {
                $currentTime = $currentWeather['time'] ?? null;
                $hourlyTimes = $data['hourly']['time'];
                
                // Buscar el índice correspondiente a la hora actual
                $currentIndex = array_search($currentTime, $hourlyTimes);
                
                if ($currentIndex !== false) {
                    if (isset($data['hourly']['relativehumidity_2m'][$currentIndex])) {
                        $humidity = (int) $data['hourly']['relativehumidity_2m'][$currentIndex];
                    }
                    if (isset($data['hourly']['precipitation'][$currentIndex])) {
                        $precipitation = (float) $data['hourly']['precipitation'][$currentIndex];
                    }
                }
            }

            // Formatear respuesta según el formato requerido
            return [
                'temperature' => round((float) ($currentWeather['temperature'] ?? 0), 1),
                'humidity' => $humidity ?? 0,
                'windSpeed' => round((float) ($currentWeather['windspeed'] ?? 0), 1),
                'windDirection' => (int) ($currentWeather['winddirection'] ?? 0),
                'weatherCode' => (int) ($currentWeather['weathercode'] ?? 0),
                'precipitation' => round($precipitation, 1),
            ];

        } catch (\Exception $e) {
            Log::error('OpenMeteoService: Excepción al obtener datos meteorológicos', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }
}

