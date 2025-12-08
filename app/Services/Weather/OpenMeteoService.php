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
            // OpenMeteo requiere que los parámetros hourly se pasen como cadena separada por comas
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
            // OpenMeteo devuelve los datos hourly empezando desde la hora actual, así que usamos el índice 0
            $humidity = null;
            $precipitation = 0;
            
            // Log de la estructura completa para debugging
            Log::debug('OpenMeteoService: Estructura de respuesta', [
                'has_hourly' => isset($data['hourly']),
                'hourly_keys' => isset($data['hourly']) ? array_keys($data['hourly']) : [],
            ]);
            
            if (isset($data['hourly'])) {
                // OpenMeteo devuelve los datos hourly como arrays indexados
                // El primer elemento (índice 0) corresponde a la hora actual
                $currentIndex = 0;
                
                // Verificar si los datos existen en la respuesta
                if (isset($data['hourly']['relativehumidity_2m']) && 
                    is_array($data['hourly']['relativehumidity_2m']) &&
                    isset($data['hourly']['relativehumidity_2m'][$currentIndex]) &&
                    $data['hourly']['relativehumidity_2m'][$currentIndex] !== null) {
                    $humidity = (int) $data['hourly']['relativehumidity_2m'][$currentIndex];
                }
                
                if (isset($data['hourly']['precipitation']) && 
                    is_array($data['hourly']['precipitation']) &&
                    isset($data['hourly']['precipitation'][$currentIndex]) &&
                    $data['hourly']['precipitation'][$currentIndex] !== null) {
                    $precipitation = (float) $data['hourly']['precipitation'][$currentIndex];
                }
                
                // Log para debugging si no se encuentran los datos
                if ($humidity === null && $precipitation === 0) {
                    Log::warning('OpenMeteoService: No se encontraron datos de humedad/precipitación', [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'hourly_structure' => array_keys($data['hourly']),
                        'has_relativehumidity_2m' => isset($data['hourly']['relativehumidity_2m']),
                        'has_precipitation' => isset($data['hourly']['precipitation']),
                        'relativehumidity_2m_type' => isset($data['hourly']['relativehumidity_2m']) ? gettype($data['hourly']['relativehumidity_2m']) : 'not_set',
                        'precipitation_type' => isset($data['hourly']['precipitation']) ? gettype($data['hourly']['precipitation']) : 'not_set',
                        'relativehumidity_2m_first' => isset($data['hourly']['relativehumidity_2m'][0]) ? $data['hourly']['relativehumidity_2m'][0] : 'not_set',
                        'precipitation_first' => isset($data['hourly']['precipitation'][0]) ? $data['hourly']['precipitation'][0] : 'not_set',
                    ]);
                }
            } else {
                Log::warning('OpenMeteoService: No se encontró la clave hourly en la respuesta', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'response_keys' => array_keys($data),
                ]);
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

