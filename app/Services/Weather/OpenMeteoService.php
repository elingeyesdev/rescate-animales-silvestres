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

    /**
     * URL base de la API de SIPI Weather
     */
    protected string $sipiApiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.open_meteo.api_url', 'https://api.open-meteo.com/v1/forecast');
        $this->sipiApiUrl = config('services.sipi_weather.api_url');
    }

    /**
     * Obtener datos meteorológicos para una ubicación específica
     * Intenta primero con SIPI Weather, si falla usa OpenMeteo como fallback
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    public function getWeather(float $latitude, float $longitude): ?array
    {
        // Intentar primero con SIPI Weather
        $sipiData = $this->getWeatherFromSipi($latitude, $longitude);
        if ($sipiData !== null) {
            Log::info('OpenMeteoService: Datos obtenidos de SIPI Weather', [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
            return $sipiData;
        }

        // Si SIPI falla, usar OpenMeteo como fallback
        Log::info('OpenMeteoService: SIPI Weather no disponible, usando OpenMeteo como fallback', [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
        return $this->getWeatherFromOpenMeteo($latitude, $longitude);
    }

    /**
     * Obtener datos meteorológicos desde SIPI Weather API
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    protected function getWeatherFromSipi(float $latitude, float $longitude): ?array
    {
        try {
            $response = Http::timeout(10)->get($this->sipiApiUrl, [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            if (!$response->successful()) {
                Log::warning('OpenMeteoService: Error al obtener datos de SIPI Weather', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            // Normalizar la respuesta de SIPI al formato esperado
            // Asumiendo que SIPI devuelve datos similares, ajustar según la estructura real
            return $this->normalizeSipiResponse($data);

        } catch (\Exception $e) {
            Log::warning('OpenMeteoService: Excepción al obtener datos de SIPI Weather', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Normalizar la respuesta de SIPI al formato estándar
     *
     * @param array $data
     * @return array|null
     */
    protected function normalizeSipiResponse(array $data): ?array
    {
        // Log de la estructura de respuesta para debugging
        Log::debug('OpenMeteoService: Estructura de respuesta de SIPI Weather', [
            'response_keys' => array_keys($data),
            'response_sample' => $data,
        ]);

        // Intentar mapear los campos de SIPI al formato esperado
        // Ajustar según la estructura real de la respuesta de SIPI
        // Intentamos múltiples variaciones de nombres de campos comunes
        $normalized = [
            'temperature' => round((float) ($data['temperature'] ?? $data['temp'] ?? $data['temp_c'] ?? $data['tempC'] ?? $data['temp_celsius'] ?? 0), 1),
            'humidity' => (int) ($data['humidity'] ?? $data['humidity_percent'] ?? $data['humidityPercent'] ?? $data['rh'] ?? 0),
            'windSpeed' => round((float) ($data['windSpeed'] ?? $data['wind_speed'] ?? $data['windSpeed'] ?? $data['wind_kmh'] ?? $data['windKmh'] ?? $data['ws'] ?? 0), 1),
            'windDirection' => (int) ($data['windDirection'] ?? $data['wind_direction'] ?? $data['windDirection'] ?? $data['wind_deg'] ?? $data['windDeg'] ?? $data['wd'] ?? 0),
            'weatherCode' => (int) ($data['weatherCode'] ?? $data['weather_code'] ?? $data['code'] ?? $data['condition'] ?? $data['weather'] ?? 0),
            'precipitation' => round((float) ($data['precipitation'] ?? $data['precip'] ?? $data['precip_mm'] ?? $data['precipMm'] ?? $data['rain'] ?? 0), 1),
        ];

        // Validar que al menos tengamos temperatura (campo mínimo requerido)
        // Verificamos si realmente hay datos de temperatura o si todos los valores son 0
        $hasTemperature = isset($data['temperature']) || isset($data['temp']) || isset($data['temp_c']) || isset($data['tempC']) || isset($data['temp_celsius']);
        
        if (!$hasTemperature && $normalized['temperature'] == 0) {
            Log::warning('OpenMeteoService: Respuesta de SIPI sin datos de temperatura válidos', [
                'response' => $data,
                'normalized' => $normalized,
            ]);
            return null;
        }

        Log::debug('OpenMeteoService: Datos normalizados de SIPI Weather', [
            'normalized' => $normalized,
        ]);

        return $normalized;
    }

    /**
     * Obtener datos meteorológicos desde OpenMeteo API (fallback)
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    protected function getWeatherFromOpenMeteo(float $latitude, float $longitude): ?array
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
                Log::warning('OpenMeteoService: Error al obtener datos meteorológicos de OpenMeteo', [
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
                Log::warning('OpenMeteoService: Respuesta de OpenMeteo sin current_weather', [
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
            $precipitation = null;
            
            // Log de la estructura completa para debugging
            Log::debug('OpenMeteoService: Estructura de respuesta de OpenMeteo', [
                'has_hourly' => isset($data['hourly']),
                'hourly_keys' => isset($data['hourly']) ? array_keys($data['hourly']) : [],
            ]);
            
            if (isset($data['hourly']) && is_array($data['hourly'])) {
                // OpenMeteo devuelve los datos hourly como arrays indexados
                // El primer elemento (índice 0) corresponde a la hora actual
                $currentIndex = 0;
                
                // Verificar si los datos existen en la respuesta para humedad
                if (isset($data['hourly']['relativehumidity_2m']) && 
                    is_array($data['hourly']['relativehumidity_2m']) &&
                    count($data['hourly']['relativehumidity_2m']) > $currentIndex &&
                    array_key_exists($currentIndex, $data['hourly']['relativehumidity_2m'])) {
                    $humidityValue = $data['hourly']['relativehumidity_2m'][$currentIndex];
                    if ($humidityValue !== null) {
                        $humidity = (int) $humidityValue;
                    }
                }
                
                // Verificar si los datos existen en la respuesta para precipitación
                // La precipitación puede ser 0 (sin lluvia) o un valor positivo, ambos son válidos
                if (isset($data['hourly']['precipitation']) && 
                    is_array($data['hourly']['precipitation']) &&
                    count($data['hourly']['precipitation']) > $currentIndex &&
                    array_key_exists($currentIndex, $data['hourly']['precipitation'])) {
                    $precipitationValue = $data['hourly']['precipitation'][$currentIndex];
                    // Aceptar 0 como valor válido (sin precipitación)
                    if ($precipitationValue !== null) {
                        $precipitation = (float) $precipitationValue;
                    }
                }
                
                // Log detallado para debugging
                Log::debug('OpenMeteoService: Datos de precipitación de OpenMeteo', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'has_precipitation_key' => isset($data['hourly']['precipitation']),
                    'precipitation_is_array' => isset($data['hourly']['precipitation']) && is_array($data['hourly']['precipitation']),
                    'precipitation_count' => isset($data['hourly']['precipitation']) && is_array($data['hourly']['precipitation']) ? count($data['hourly']['precipitation']) : 0,
                    'precipitation_index_0' => isset($data['hourly']['precipitation'][0]) ? $data['hourly']['precipitation'][0] : 'not_set',
                    'precipitation_value' => $precipitation,
                    'precipitation_type' => isset($data['hourly']['precipitation']) ? gettype($data['hourly']['precipitation']) : 'not_set',
                ]);
                
                // Log para debugging si no se encuentran los datos
                if ($humidity === null && $precipitation === null) {
                    Log::warning('OpenMeteoService: No se encontraron datos de humedad/precipitación en OpenMeteo', [
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
                Log::warning('OpenMeteoService: No se encontró la clave hourly en la respuesta de OpenMeteo', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'response_keys' => array_keys($data),
                ]);
            }

            // Formatear respuesta según el formato requerido
            // La precipitación puede ser 0 (sin lluvia) o un valor positivo, ambos son válidos
            return [
                'temperature' => round((float) ($currentWeather['temperature'] ?? 0), 1),
                'humidity' => $humidity ?? 0,
                'windSpeed' => round((float) ($currentWeather['windspeed'] ?? 0), 1),
                'windDirection' => (int) ($currentWeather['winddirection'] ?? 0),
                'weatherCode' => (int) ($currentWeather['weathercode'] ?? 0),
                'precipitation' => $precipitation !== null ? round($precipitation, 1) : 0,
            ];

        } catch (\Exception $e) {
            Log::error('OpenMeteoService: Excepción al obtener datos meteorológicos de OpenMeteo', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }
}

