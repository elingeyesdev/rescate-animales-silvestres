<?php

namespace App\Services\Fire;

use App\Models\FocosCalor;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Servicio para consultar focos de calor desde la base de datos local
 * (en lugar de llamar directamente a la API de NASA FIRMS)
 * 
 * Los datos se guardan en BD (persistente) y se cachean para consultas rápidas.
 * El caché se limpia automáticamente cuando se importan nuevos datos.
 */
class FocosCalorService
{
    /**
     * Tiempo de expiración del caché (en minutos)
     * Los datos se actualizan cuando se ejecuta el comando de importación
     */
    private const CACHE_TTL = 360; // 6 horas

    /**
     * Obtener focos de calor recientes (últimos N días)
     * 
     * Los datos se consultan desde la BD. El caché se usa solo para
     * consultas simples, no para datos grandes que pueden causar problemas.
     *
     * @param int $days Número de días hacia atrás
     * @return Collection
     */
    public function getRecentHotspots(int $days = 2): Collection
    {
        // Usar caché solo para consultas pequeñas (estadísticas, conteos)
        // Para colecciones grandes, consultar directamente desde BD
        $since = Carbon::now()->subDays($days);
        
        return FocosCalor::where('acq_date', '>=', $since)
            ->orderBy('acq_date', 'desc')
            ->orderBy('acq_time', 'desc')
            ->get();
    }

    /**
     * Obtener focos de calor intentando primero desde la API de integración,
     * si falla, usar datos de FIRMS desde la BD
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
                // Convertir los datos de la API a formato Collection de modelos FocosCalor
                $collection = $this->convertIntegrationDataToCollection($integrationData);
                if ($collection->isNotEmpty()) {
                    return $collection;
                }
            }
        } catch (\Exception $e) {
            // Si hay cualquier error, continuar con el fallback
        }
        
        // Si falla, usar datos de FIRMS desde la BD
        return $this->getRecentHotspots($days);
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
     * Convertir datos de la API de integración a Collection de modelos FocosCalor
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

            // Crear modelo temporal (no guardado en BD)
            $focoCalor = new FocosCalor();
            $focoCalor->latitude = (float) $hotspot['latitude'];
            $focoCalor->longitude = (float) $hotspot['longitude'];
            $focoCalor->confidence = $confidence;
            $focoCalor->acq_date = $acqDate;
            $focoCalor->acq_time = $formattedTime;
            $focoCalor->bright_ti4 = $brightTi4;
            $focoCalor->bright_ti5 = $brightTi5;
            $focoCalor->frp = isset($hotspot['frp']) ? (float) $hotspot['frp'] : null;
            
            // Asignar un ID temporal único para evitar conflictos
            $focoCalor->id = 'integration_' . md5($hotspot['latitude'] . $hotspot['longitude'] . $dateStr . $timeStr);
            $focoCalor->exists = false; // Marcar como no persistido
            
            $collection->push($focoCalor);
        }
        
        return $collection;
    }

    /**
     * Obtener focos de calor dentro de un área geográfica
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
        $query = FocosCalor::whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng]);

        if ($days !== null) {
            $since = Carbon::now()->subDays($days);
            $query->where('acq_date', '>=', $since);
        }

        return $query->orderBy('acq_date', 'desc')
            ->orderBy('acq_time', 'desc')
            ->get();
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
        $query = FocosCalor::where('confidence', '>=', $minConfidence);

        if ($days !== null) {
            $since = Carbon::now()->subDays($days);
            $query->where('acq_date', '>=', $since);
        }

        return $query->orderBy('confidence', 'desc')
            ->orderBy('acq_date', 'desc')
            ->get();
    }

    /**
     * Obtener estadísticas de focos de calor
     *
     * @param int|null $days
     * @return array
     */
    public function getStatistics(?int $days = null): array
    {
        $query = FocosCalor::query();

        if ($days !== null) {
            $since = Carbon::now()->subDays($days);
            $query->where('acq_date', '>=', $since);
        }

        $total = $query->count();
        $highConfidence = (clone $query)->where('confidence', '>=', 70)->count();
        $today = (clone $query)->whereDate('acq_date', Carbon::today())->count();

        return [
            'total' => $total,
            'high_confidence' => $highConfidence,
            'today' => $today,
            'avg_confidence' => $query->avg('confidence') ?? 0,
        ];
    }

    /**
     * Formatear focos de calor para mostrar en mapas
     * 
     * NOTA: No se cachea el resultado formateado porque puede ser muy grande
     * y causar problemas con límites de tamaño en la tabla cache.
     * El caché se aplica solo a las consultas de BD, no al formateo.
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

        // Formatear directamente sin caché (el procesamiento es rápido)
        return $hotspots->map(function ($hotspot) {
            try {
                // Manejar tanto modelos de BD como modelos temporales
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
                \Log::warning('Error formateando hotspot: ' . $e->getMessage());
                return null;
            }
        })->filter()->toArray(); // Filtrar nulls
    }

    /**
     * Limpiar todo el caché de focos de calor
     * 
     * Se llama automáticamente cuando se importan nuevos datos
     * para asegurar que se muestren los datos más recientes
     */
    public function clearCache(): void
    {
        // Limpiar solo cachés específicos de focos de calor (si existen)
        // Como ahora no usamos caché para datos grandes, esto es principalmente
        // para estadísticas o conteos que puedan estar cacheados
        try {
            Cache::forget('focos_calor_recent_2');
            Cache::forget('focos_calor_recent_7');
            Cache::forget('focos_calor_stats');
        } catch (\Exception $e) {
            // Si falla, no es crítico - los datos se consultarán directamente desde BD
        }
    }

    /**
     * Encontrar el foco de calor más cercano a unas coordenadas
     *
     * @param float $lat
     * @param float $lng
     * @param float $maxDistanceKm Distancia máxima en kilómetros (null = sin límite)
     * @param int|null $days Número de días hacia atrás
     * @return FocosCalor|null
     */
    public function findNearestHotspot(float $lat, float $lng, ?float $maxDistanceKm = 10, ?int $days = 2): ?FocosCalor
    {
        $query = FocosCalor::query();

        if ($days !== null) {
            $since = Carbon::now()->subDays($days);
            $query->where('acq_date', '>=', $since);
        }

        // Filtrar por área aproximada primero (más eficiente)
        // 1 grado de latitud ≈ 111 km
        if ($maxDistanceKm !== null) {
            $latDelta = $maxDistanceKm / 111;
            $lngDelta = $maxDistanceKm / (111 * cos(deg2rad($lat)));
            
            $query->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
                  ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta]);
        }

        $hotspots = $query->get();

        if ($hotspots->isEmpty()) {
            return null;
        }

        // Calcular distancia exacta y encontrar el más cercano
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($hotspots as $hotspot) {
            $distance = $hotspot->distanceTo($lat, $lng);
            
            if ($distance < $minDistance && ($maxDistanceKm === null || $distance <= $maxDistanceKm)) {
                $minDistance = $distance;
                $nearest = $hotspot;
            }
        }

        return $nearest;
    }

    /**
     * DEPRECADO: No usar asociación por ID
     * 
     * La API de NASA FIRMS no proporciona IDs de incendios.
     * Los focos de calor se relacionan con reportes por proximidad geográfica,
     * no por ID. Usar getNearbyHotspots() en su lugar.
     * 
     * @deprecated Usar getNearbyHotspots() en su lugar
     */
    public function associateNearestHotspotToReport(\App\Models\Report $report, float $maxDistanceKm = 10): bool
    {
        // No asociar por ID - los focos de calor son independientes
        // La relación se hace por proximidad geográfica cuando se visualiza
        return false;
    }

    /**
     * Obtener focos de calor cercanos a unas coordenadas (para mostrar en mapa)
     * Útil para mostrar información contextual sin necesidad de asociar
     *
     * @param float $lat
     * @param float $lng
     * @param float $radiusKm Radio en kilómetros
     * @param int|null $days
     * @return Collection
     */
    public function getNearbyHotspots(float $lat, float $lng, float $radiusKm = 20, ?int $days = 2): Collection
    {
        $query = FocosCalor::query();

        if ($days !== null) {
            $since = Carbon::now()->subDays($days);
            $query->where('acq_date', '>=', $since);
        }

        // Filtrar por área aproximada
        $latDelta = $radiusKm / 111;
        $lngDelta = $radiusKm / (111 * cos(deg2rad($lat)));
        
        $query->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
              ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta]);

        return $query->orderBy('acq_date', 'desc')
            ->orderBy('confidence', 'desc')
            ->get()
            ->filter(function ($hotspot) use ($lat, $lng, $radiusKm) {
                return $hotspot->distanceTo($lat, $lng) <= $radiusKm;
            });
    }
}

