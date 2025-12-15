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
     * Si falla, retorna datos simulados como fallback
     * 
     * @return Collection
     */
    public function getExternalFireReports(): Collection
    {
        try {
            $apiUrl = config('services.external_fire_reports.api_url');
            
            if (empty($apiUrl)) {
                \Log::info('ExternalFireReportsService: No hay URL configurada, usando datos simulados');
                return $this->getSimulatedFireReports();
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
                    \Log::warning('Error obteniendo reportes externos de incendios (intento ' . $attempt . '): ' . $e->getMessage());
                }
                
                // Esperar un poco antes del siguiente intento (excepto en el último)
                if ($attempt < $maxAttempts) {
                    usleep(500000); // 0.5 segundos
                }
            }
            
            // Si llegamos aquí, todos los intentos fallaron, usar datos simulados
            \Log::info('ExternalFireReportsService: No se pudo conectar al endpoint, usando datos simulados como fallback');
            return $this->getSimulatedFireReports();
        } catch (\Exception $e) {
            \Log::error('Error en ExternalFireReportsService: ' . $e->getMessage() . ' - Usando datos simulados como fallback');
            return $this->getSimulatedFireReports();
        }
    }

    /**
     * Obtener reportes de incendios simulados como fallback
     * Estos datos se usan cuando el endpoint de brigadas no está disponible
     * 
     * @return Collection
     */
    private function getSimulatedFireReports(): Collection
    {
        $simulatedReports = [
            [
                "id" => "550e8400-e29b-41d4-a716-446655440004",
                "nombre_reportante" => "Ana Gutiérrez",
                "telefono_contacto" => "+591 74567890",
                "fecha_hora" => "2025-12-15T01:33:00-04:00",
                "nombre_lugar" => "Zona Industrial Norte - Planta Textil",
                "latitud" => -17.75,
                "longitud" => -63.2,
                "comentario_adicional" => "Incendio en almacén de materiales inflamables. Riesgo de explosión.",
                "nivel_gravedad" => "Activo",
                "creado" => "2025-12-15T06:03:00-04:00",
                "simulado" => true
            ],
            [
                "id" => "550e8400-e29b-41d4-a716-446655440003",
                "nombre_reportante" => "Carlos Mendoza",
                "telefono_contacto" => "+591 73456789",
                "fecha_hora" => "2025-12-14T23:03:00-04:00",
                "nombre_lugar" => "Carretera Santa Cruz - Cochabamba Km 45",
                "latitud" => -17.65,
                "longitud" => -63.3,
                "comentario_adicional" => "Camión cisterna en llamas en carretera principal. Tráfico desviado.",
                "nivel_gravedad" => "Contenido",
                "creado" => "2025-12-15T06:03:00-04:00",
                "simulado" => true
            ],
            [
                "id" => "550e8400-e29b-41d4-a716-446655440008",
                "nombre_reportante" => "Sofía Martínez",
                "telefono_contacto" => "+591 78901234",
                "fecha_hora" => "2025-12-14T20:03:00-04:00",
                "nombre_lugar" => "Taller Mecánico El Pino",
                "latitud" => -17.77,
                "longitud" => -63.19,
                "comentario_adicional" => "Pequeño incendio en taller por derrame de combustible. Controlado rápidamente.",
                "nivel_gravedad" => "Controlado",
                "creado" => "2025-12-15T06:03:00-04:00",
                "simulado" => true
            ],
            [
                "id" => "550e8400-e29b-41d4-a716-446655440006",
                "nombre_reportante" => "Patricia Rojas",
                "telefono_contacto" => "+591 76789012",
                "fecha_hora" => "2025-12-14T14:03:00-04:00",
                "nombre_lugar" => "Barrio El Carmen - Casa Habitación",
                "latitud" => -17.8,
                "longitud" => -63.17,
                "comentario_adicional" => "Incendio en cocina por cortocircuito. Familia evacuada sin heridos.",
                "nivel_gravedad" => "Contenido",
                "creado" => "2025-12-15T06:03:00-04:00",
                "simulado" => true
            ],
            [
                "id" => "550e8400-e29b-41d4-a716-446655440005",
                "nombre_reportante" => "Roberto Sánchez",
                "telefono_contacto" => "+591 75678901",
                "fecha_hora" => "2025-12-14T02:03:00-04:00",
                "nombre_lugar" => "Comunidad Chiquitana San Rafael",
                "latitud" => -16.25,
                "longitud" => -61.4167,
                "comentario_adicional" => "Quema controlada que se salió de control en área agrícola pequeña.",
                "nivel_gravedad" => "Controlado",
                "creado" => "2025-12-15T06:03:00-04:00",
                "simulado" => true
            ],
            [
                "id" => "550e8400-e29b-41d4-a716-446655440001",
                "nombre_reportante" => "Juan Pérez Rodríguez",
                "telefono_contacto" => "+591 71234567",
                "fecha_hora" => "2025-12-13T02:03:00-04:00",
                "nombre_lugar" => "Parque Nacional Noel Kempff",
                "latitud" => -14.5667,
                "longitud" => -60.9667,
                "comentario_adicional" => "Incendio forestal de gran magnitud en zona protegida. Se requiere intervención urgente.",
                "nivel_gravedad" => "Activo",
                "creado" => "2025-12-15T06:03:00-04:00",
                "simulado" => true
            ],
            [
                "id" => "550e8400-e29b-41d4-a716-446655440002",
                "nombre_reportante" => "María López Silva",
                "telefono_contacto" => "+591 72345678",
                "fecha_hora" => "2025-12-10T02:03:00-04:00",
                "nombre_lugar" => "Centro Comercial Las Américas",
                "latitud" => -17.7833,
                "longitud" => -63.1821,
                "comentario_adicional" => "Incendio en piso 3 del centro comercial. Evacuación en curso. Posibles personas atrapadas.",
                "nivel_gravedad" => "Fuera de control",
                "creado" => "2025-12-15T06:03:00-04:00",
                "simulado" => true
            ],
            [
                "id" => "550e8400-e29b-41d4-a716-446655440007",
                "nombre_reportante" => "Luis Fernández",
                "telefono_contacto" => "+591 77890123",
                "fecha_hora" => "2025-12-08T02:03:00-04:00",
                "nombre_lugar" => "Reserva Municipal Lomas de Arena",
                "latitud" => -17.9167,
                "longitud" => -63.0333,
                "comentario_adicional" => "Incendio forestal masivo afectando área protegida. Fauna en riesgo.",
                "nivel_gravedad" => "Fuera de control",
                "creado" => "2025-12-15T06:03:00-04:00",
                "simulado" => true
            ]
        ];

        return collect($simulatedReports);
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
                
                $isSimulated = isset($report['simulado']) && $report['simulado'] === true;
                
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
                    'simulado' => $isSimulated,
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
