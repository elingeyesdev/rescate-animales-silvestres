<?php

namespace App\Services\Fire;

use App\Models\Report;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

/**
 * Servicio para obtener reportes de incendios externos desde una API
 */
class ExternalFireReportsService
{
    /**
     * Obtener reportes de incendios externos desde la API
     * 
     * @return Collection
     */
    public function getExternalFireReports(): Collection
    {
        try {
            $apiUrl = config('services.external_fire_reports.api_url');
            
            if (empty($apiUrl)) {
                return collect();
            }

            $maxAttempts = 2;
            
            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                try {
                    $response = Http::timeout(10)->get($apiUrl);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        
                        // Si la respuesta es un array directo, usarlo
                        if (is_array($data) && !isset($data['success'])) {
                            return collect($data);
                        }
                        
                        // Si tiene estructura con success y data
                        if (isset($data['success']) && $data['success'] === true && isset($data['data']) && is_array($data['data'])) {
                            return collect($data['data']);
                        }
                        
                        // Si tiene estructura con data directa
                        if (isset($data['data']) && is_array($data['data'])) {
                            return collect($data['data']);
                        }
                    }
                } catch (\Exception $e) {
                    // Silenciar errores, solo intentar de nuevo
                    \Log::warning('Error obteniendo reportes externos de incendios: ' . $e->getMessage());
                }
                
                // Esperar un poco antes del siguiente intento (excepto en el último)
                if ($attempt < $maxAttempts) {
                    usleep(500000); // 0.5 segundos
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error en ExternalFireReportsService: ' . $e->getMessage());
        }
        
        return collect();
    }

    /**
     * Formatear reportes externos para mostrar en mapas
     * Hace double checking con hallazgos locales que tengan incendio_id
     * 
     * @param Collection $reports
     * @return array
     */
    public function formatForMap(Collection $reports): array
    {
        if ($reports->isEmpty()) {
            return [];
        }

        // Obtener todos los hallazgos locales que tengan incendio_id
        $localReportsWithIncendioId = Report::whereNotNull('incendio_id')
            ->where('aprobado', 1)
            ->get()
            ->keyBy('incendio_id');

        return $reports->map(function ($report) use ($localReportsWithIncendioId) {
            try {
                // Validar que tenga coordenadas
                if (!isset($report['latitud']) || !isset($report['longitud'])) {
                    return null;
                }

                // Obtener color según nivel de gravedad
                $color = $this->getColorBySeverity($report['nivel_gravedad'] ?? 'Controlado');
                
                // Double checking: verificar si hay hallazgos locales con este incendio_id
                $externalReportId = $report['id'] ?? null;
                $hasLocalReports = false;
                $localReportsCount = 0;
                
                if ($externalReportId) {
                    // Buscar coincidencias por ID del reporte externo
                    // El incendio_id en los reportes locales debe coincidir con el id del reporte externo
                    $matchingLocalReports = $localReportsWithIncendioId->filter(function ($localReport) use ($externalReportId) {
                        // Comparar el incendio_id del reporte local con el id del reporte externo
                        return $localReport->incendio_id == $externalReportId;
                    });
                    
                    $hasLocalReports = $matchingLocalReports->isNotEmpty();
                    $localReportsCount = $matchingLocalReports->count();
                }
                
                return [
                    'id' => $externalReportId,
                    'lat' => (float) $report['latitud'],
                    'lng' => (float) $report['longitud'],
                    'nombre_reportante' => $report['nombre_reportante'] ?? 'Sin nombre',
                    'telefono_contacto' => $report['telefono_contacto'] ?? 'N/A',
                    'fecha_hora' => $report['fecha_hora'] ?? null,
                    'nombre_lugar' => $report['nombre_lugar'] ?? 'Ubicación desconocida',
                    'comentario_adicional' => $report['comentario_adicional'] ?? '',
                    'nivel_gravedad' => $report['nivel_gravedad'] ?? 'Controlado',
                    'creado' => $report['creado'] ?? null,
                    'color' => $color,
                    'has_local_reports' => $hasLocalReports,
                    'local_reports_count' => $localReportsCount,
                ];
            } catch (\Exception $e) {
                \Log::warning('Error formateando reporte externo: ' . $e->getMessage());
                return null;
            }
        })->filter()->toArray();
    }

    /**
     * Obtener color según el nivel de gravedad
     * 
     * @param string $severity
     * @return string Color en formato hexadecimal
     */
    private function getColorBySeverity(string $severity): string
    {
        $severity = strtolower(trim($severity));
        
        return match($severity) {
            'fuera de control' => '#dc3545', // Rojo
            'activo' => '#ff8800',            // Naranja
            'contenido' => '#ffc107',         // Amarillo
            'controlado' => '#28a745',        // Verde
            default => '#6c757d',             // Gris por defecto
        };
    }
}
