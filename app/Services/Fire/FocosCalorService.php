<?php

namespace App\Services\Fire;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para consultar focos de calor desde servicios externos
 * NO usa base de datos - solo consume APIs externas
 * 
 * Prioridad:
 * 1. API de integración (v1/hotspots/live)
 * 2. NASA FIRMS directo (sin guardar en BD)
 */
class FocosCalorService
{
    /**
     * Obtener focos de calor recientes (últimos N días)
     * 
     * DEPRECADO: Usar getRecentHotspotsWithFallback() en su lugar
     * 
     * @param int $days Número de días hacia atrás
     * @return Collection
     * @deprecated
     */
    public function getRecentHotspots(int $days = 2): Collection
    {
        // Redirigir al método con fallback
        return $this->getRecentHotspotsWithFallback($days);
    }

    /**
     * Obtener focos de calor intentando primero desde la API de integración,
     * si falla, usar datos de FIRMS directamente (sin guardar en BD)
     * 
     * @param int $days Número de días hacia atrás
     * @return Collection
     */
    public function getRecentHotspotsWithFallback(int $days = 2): Collection
    {
        try {
            // Intentar primero obtener desde la API de integración
            $integrationData = $this->fetchFromIntegrationApi();
            
            if ($integrationData !== null && !empty($integrationData) && is_array($integrationData)) {
                // Convertir los datos de la API a formato Collection
                $collection = $this->convertIntegrationDataToCollection($integrationData);
                if ($collection->isNotEmpty()) {
                    Log::info('FocosCalorService: Datos obtenidos de API de integración', [
                        'count' => $collection->count()
                    ]);
                    return $collection;
                }
            }
        } catch (\Exception $e) {
            Log::warning('FocosCalorService: Error al obtener datos de API de integración', [
                'error' => $e->getMessage()
            ]);
        }
        
        // Si falla, usar datos de FIRMS directamente (sin guardar en BD)
        Log::info('FocosCalorService: Usando NASA FIRMS como fallback');
        return $this->fetchFromNasaFirmsDirect($days);
    }

    /**
     * Intentar obtener datos del endpoint de integración (2 intentos)
     */
    private function fetchFromIntegrationApi(): ?array
    {
        $apiUrl = config('services.hotspots_integration.api_url');
        
        if (empty($apiUrl)) {
            return null;
        }

        $maxAttempts = 2;
        
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::timeout(10)->get($apiUrl);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['success']) && $data['success'] === true && isset($data['data']) && is_array($data['data'])) {
                        return $data['data'];
                    }
                }
            } catch (\Exception $e) {
                // Silenciar errores, solo intentar de nuevo
            }
            
            // Esperar un poco antes del siguiente intento (excepto en el último)
            if ($attempt < $maxAttempts) {
                usleep(500000); // 0.5 segundos
            }
        }
        
        return null;
    }

    /**
     * Obtener datos directamente de NASA FIRMS sin guardar en BD
     * 
     * @param int $days Número de días hacia atrás
     * @return Collection
     */
    private function fetchFromNasaFirmsDirect(int $days = 2): Collection
    {
        $apiKey = config('services.nasa_firms.api_key');
        $apiBase = config('services.nasa_firms.api_base', 'https://firms.modaps.eosdis.nasa.gov/api/area/csv');
        
        if (empty($apiKey)) {
            Log::warning('FocosCalorService: NASA_FIRMS_API_KEY no configurada');
            return collect();
        }
        
        // Bolivia bounding box
        $bounds = [
            'min_lat' => -22.9,
            'max_lat' => -9.7,
            'min_lng' => -69.6,
            'max_lng' => -57.5,
        ];
        
        $collection = collect();
        $satellites = ['VIIRS_NOAA21_NRT', 'VIIRS_SNPP_NRT', 'MODIS_NRT'];
        
        foreach ($satellites as $satellite) {
            try {
                $url = sprintf(
                    '%s/%s/%s/%s,%s,%s,%s/%s',
                    $apiBase,
                    $apiKey,
                    $satellite,
                    $bounds['min_lng'],
                    $bounds['min_lat'],
                    $bounds['max_lng'],
                    $bounds['max_lat'],
                    $days
                );
                
                $response = Http::timeout(30)->get($url);
                
                if ($response->successful()) {
                    $csvData = $response->body();
                    $lines = explode("\n", trim($csvData));
                    
                    if (count($lines) > 1) {
                        $headers = null;
                        foreach ($lines as $line) {
                            if (strpos($line, 'latitude') !== false) {
                                $headers = str_getcsv($line);
                            } else if (!empty(trim($line)) && $headers) {
                                $values = str_getcsv($line);
                                if (count($values) >= count($headers)) {
                                    $fire = array_combine($headers, $values);
                                    
                                    // Filtrar por Bolivia
                                    $lat = (float) ($fire['latitude'] ?? 0);
                                    $lng = (float) ($fire['longitude'] ?? 0);
                                    
                                    if ($lat >= $bounds['min_lat'] && $lat <= $bounds['max_lat'] &&
                                        $lng >= $bounds['min_lng'] && $lng <= $bounds['max_lng']) {
                                        
                                        $hotspot = $this->createHotspotObject($fire);
                                        $collection->push($hotspot);
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("FocosCalorService: Error obteniendo datos de {$satellite}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $collection;
    }

    /**
     * Convertir datos de la API de integración a Collection
     */
    private function convertIntegrationDataToCollection(array $hotspots): Collection
    {
        $collection = collect();
        
        foreach ($hotspots as $hotspot) {
            if (!isset($hotspot['latitude']) || !isset($hotspot['longitude'])) {
                continue;
            }

            // Convertir fecha de DD/MM/YYYY a formato Carbon
            $dateStr = $hotspot['date'] ?? null;
            if ($dateStr) {
                $dateParts = explode('/', $dateStr);
                if (count($dateParts) === 3) {
                    $acqDate = Carbon::createFromDate($dateParts[2], $dateParts[1], $dateParts[0]);
                } else {
                    $acqDate = Carbon::parse($dateStr);
                }
            } else {
                $acqDate = Carbon::today();
            }

            // Convertir tiempo de "412" a "04:12:00"
            $timeStr = $hotspot['time'] ?? '0000';
            $timeStr = str_pad($timeStr, 4, '0', STR_PAD_LEFT);
            $formattedTime = substr($timeStr, 0, 2) . ':' . substr($timeStr, 2, 2) . ':00';

            // Handle confidence
            $confidence = $hotspot['confidence'] ?? null;
            if (!is_numeric($confidence)) {
                $confidenceMap = ['l' => 0, 'n' => 50, 'h' => 100];
                $confidence = $confidenceMap[strtolower($confidence)] ?? 50;
            } else {
                $confidence = (int) $confidence;
            }

            // Mapear brightness
            $brightness = isset($hotspot['brightness']) ? (float) $hotspot['brightness'] : null;
            $satellite = $hotspot['satellite'] ?? '';
            
            $brightTi4 = null;
            $brightTi5 = null;
            if (stripos($satellite, 'VIIRS') !== false || stripos($satellite, 'N21') !== false || stripos($satellite, 'SNPP') !== false) {
                $brightTi4 = $brightness;
            } else {
                $brightTi5 = $brightness;
            }

            // Crear objeto simple (no modelo de BD)
            $hotspotObj = $this->createHotspotObject([
                'latitude' => (float) $hotspot['latitude'],
                'longitude' => (float) $hotspot['longitude'],
                'confidence' => $confidence,
                'acq_date' => $acqDate->format('Y-m-d'),
                'acq_time' => $formattedTime,
                'bright_ti4' => $brightTi4,
                'bright_ti5' => $brightTi5,
                'frp' => isset($hotspot['frp']) ? (float) $hotspot['frp'] : null,
            ], 'integration_' . md5($hotspot['latitude'] . $hotspot['longitude'] . $dateStr . $timeStr));
            
            $collection->push($hotspotObj);
        }
        
        return $collection;
    }

    /**
     * Crear un objeto simple para representar un hotspot (sin BD)
     */
    private function createHotspotObject(array $data, ?string $id = null): object
    {
        $id = $id ?? 'firms_' . md5(($data['latitude'] ?? 0) . ($data['longitude'] ?? 0) . ($data['acq_date'] ?? '') . ($data['acq_time'] ?? ''));
        
        $acqDate = isset($data['acq_date']) ? Carbon::parse($data['acq_date']) : Carbon::today();
        
        return (object) [
            'id' => $id,
            'latitude' => (float) ($data['latitude'] ?? 0),
            'longitude' => (float) ($data['longitude'] ?? 0),
            'confidence' => isset($data['confidence']) ? (int) $data['confidence'] : null,
            'acq_date' => $acqDate,
            'acq_time' => $data['acq_time'] ?? '00:00:00',
            'bright_ti4' => isset($data['bright_ti4']) ? (float) $data['bright_ti4'] : null,
            'bright_ti5' => isset($data['bright_ti5']) ? (float) $data['bright_ti5'] : null,
            'frp' => isset($data['frp']) ? (float) $data['frp'] : null,
        ];
    }

    /**
     * Obtener focos de calor dentro de un área geográfica
     * Filtra desde los datos obtenidos de las APIs externas
     *
     * @param float $minLat
     * @param float $maxLat
     * @param float $minLng
     * @param float $maxLng
     * @param int|null $days Número de días hacia atrás (null = sin límite)
     * @return Collection
     */
    public function getHotspotsInArea(
        float $minLat,
        float $maxLat,
        float $minLng,
        float $maxLng,
        ?int $days = null
    ): Collection {
        $allHotspots = $this->getRecentHotspotsWithFallback($days ?? 2);
        
        return $allHotspots->filter(function ($hotspot) use ($minLat, $maxLat, $minLng, $maxLng, $days) {
            $lat = $hotspot->latitude;
            $lng = $hotspot->longitude;
            
            // Filtrar por área
            if ($lat < $minLat || $lat > $maxLat || $lng < $minLng || $lng > $maxLng) {
                return false;
            }
            
            // Filtrar por días si se especifica
            if ($days !== null) {
                $since = Carbon::now()->subDays($days);
                $acqDate = $hotspot->acq_date instanceof Carbon ? $hotspot->acq_date : Carbon::parse($hotspot->acq_date);
                if ($acqDate->lt($since)) {
                    return false;
                }
            }
            
            return true;
        })->sortByDesc(function ($hotspot) {
            $acqDate = $hotspot->acq_date instanceof Carbon ? $hotspot->acq_date : Carbon::parse($hotspot->acq_date);
            return $acqDate->timestamp;
        })->values();
    }

    /**
     * Obtener focos de calor de alta confianza
     *
     * @param int $minConfidence Confianza mínima (0-100)
     * @param int|null $days
     * @return Collection
     */
    public function getHighConfidenceHotspots(int $minConfidence = 70, ?int $days = null): Collection
    {
        $allHotspots = $this->getRecentHotspotsWithFallback($days ?? 2);
        
        return $allHotspots->filter(function ($hotspot) use ($minConfidence, $days) {
            if (($hotspot->confidence ?? 0) < $minConfidence) {
                return false;
            }
            
            if ($days !== null) {
                $since = Carbon::now()->subDays($days);
                $acqDate = $hotspot->acq_date instanceof Carbon ? $hotspot->acq_date : Carbon::parse($hotspot->acq_date);
                if ($acqDate->lt($since)) {
                    return false;
                }
            }
            
            return true;
        })->sortByDesc('confidence')->values();
    }

    /**
     * Obtener estadísticas de focos de calor
     *
     * @param int|null $days
     * @return array
     */
    public function getStatistics(?int $days = null): array
    {
        $allHotspots = $this->getRecentHotspotsWithFallback($days ?? 2);
        
        if ($days !== null) {
            $since = Carbon::now()->subDays($days);
            $allHotspots = $allHotspots->filter(function ($hotspot) use ($since) {
                $acqDate = $hotspot->acq_date instanceof Carbon ? $hotspot->acq_date : Carbon::parse($hotspot->acq_date);
                return $acqDate->gte($since);
            });
        }
        
        $total = $allHotspots->count();
        $highConfidence = $allHotspots->filter(fn($h) => ($h->confidence ?? 0) >= 70)->count();
        $today = $allHotspots->filter(function ($hotspot) {
            $acqDate = $hotspot->acq_date instanceof Carbon ? $hotspot->acq_date : Carbon::parse($hotspot->acq_date);
            return $acqDate->isToday();
        })->count();
        
        $avgConfidence = $allHotspots->avg('confidence') ?? 0;
        
        return [
            'total' => $total,
            'high_confidence' => $highConfidence,
            'today' => $today,
            'avg_confidence' => round($avgConfidence, 2),
        ];
    }

    /**
     * Formatear focos de calor para mostrar en mapas
     *
     * @param Collection $hotspots
     * @return array
     */
    public function formatForMap(Collection $hotspots): array
    {
        // Si la colección está vacía, retornar array vacío directamente
        if ($hotspots->isEmpty()) {
            return [];
        }

        // Formatear directamente
        return $hotspots->map(function ($hotspot) {
            try {
                // Manejar fechas
                $acqDate = $hotspot->acq_date;
                if ($acqDate instanceof Carbon) {
                    $dateFormatted = $acqDate->format('d/m/Y');
                } elseif (is_string($acqDate)) {
                    $dateFormatted = Carbon::parse($acqDate)->format('d/m/Y');
                } else {
                    $dateFormatted = Carbon::now()->format('d/m/Y');
                }
                
                return [
                    'id' => $hotspot->id ?? null,
                    'lat' => (float) ($hotspot->latitude ?? 0),
                    'lng' => (float) ($hotspot->longitude ?? 0),
                    'confidence' => $hotspot->confidence ?? 0,
                    'date' => $dateFormatted,
                    'time' => $hotspot->acq_time ?? '00:00:00',
                    'frp' => $hotspot->frp ?? null,
                    'brightness' => $hotspot->bright_ti4 ?? $hotspot->bright_ti5 ?? null,
                ];
            } catch (\Exception $e) {
                // Si hay error formateando un hotspot, omitirlo
                Log::warning('Error formateando hotspot: ' . $e->getMessage());
                return null;
            }
        })->filter()->toArray(); // Filtrar nulls
    }

    /**
     * Limpiar todo el caché de focos de calor
     * Ya no se usa caché, pero se mantiene para compatibilidad
     */
    public function clearCache(): void
    {
        // Ya no se usa caché, pero se mantiene el método para compatibilidad
    }

    /**
     * Encontrar el foco de calor más cercano a unas coordenadas
     *
     * @param float $lat
     * @param float $lng
     * @param float|null $maxDistanceKm Distancia máxima en kilómetros (null = sin límite)
     * @param int|null $days Número de días hacia atrás
     * @return object|null
     */
    public function findNearestHotspot(float $lat, float $lng, ?float $maxDistanceKm = 10, ?int $days = 2): ?object
    {
        $allHotspots = $this->getRecentHotspotsWithFallback($days);
        
        if ($allHotspots->isEmpty()) {
            return null;
        }

        // Calcular distancia exacta y encontrar el más cercano
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($allHotspots as $hotspot) {
            $distance = $this->calculateDistance(
                $lat,
                $lng,
                $hotspot->latitude,
                $hotspot->longitude
            );
            
            if ($distance < $minDistance && ($maxDistanceKm === null || $distance <= $maxDistanceKm)) {
                $minDistance = $distance;
                $nearest = $hotspot;
            }
        }

        return $nearest;
    }

    /**
     * Calcular distancia entre dos coordenadas en kilómetros
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Radio de la Tierra en kilómetros

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * DEPRECADO: No usar asociación por ID
     * 
     * @deprecated
     */
    public function associateNearestHotspotToReport(\App\Models\Report $report, float $maxDistanceKm = 10): bool
    {
        // No asociar por ID - los focos de calor son independientes
        return false;
    }

    /**
     * Obtener focos de calor cercanos a unas coordenadas (para mostrar en mapa)
     *
     * @param float $lat
     * @param float $lng
     * @param float $radiusKm Radio en kilómetros
     * @param int|null $days
     * @return Collection
     */
    public function getNearbyHotspots(float $lat, float $lng, float $radiusKm = 20, ?int $days = 2): Collection
    {
        $allHotspots = $this->getRecentHotspotsWithFallback($days);
        
        return $allHotspots->filter(function ($hotspot) use ($lat, $lng, $radiusKm) {
            $distance = $this->calculateDistance($lat, $lng, $hotspot->latitude, $hotspot->longitude);
            return $distance <= $radiusKm;
        })->sortByDesc(function ($hotspot) {
            $acqDate = $hotspot->acq_date instanceof Carbon ? $hotspot->acq_date : Carbon::parse($hotspot->acq_date);
            return $acqDate->timestamp;
        })->sortByDesc('confidence')->values();
    }
}
