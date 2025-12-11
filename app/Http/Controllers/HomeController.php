<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use App\Services\Fire\FocosCalorService;
use App\Models\Species;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class HomeController extends Controller
{
    protected $dashboardService;
    protected $focosCalorService;

    public function __construct(DashboardService $dashboardService, FocosCalorService $focosCalorService)
    {
        $this->middleware('auth');
        $this->dashboardService = $dashboardService;
        $this->focosCalorService = $focosCalorService;
    }

    public function index()
    {
        $data = $this->dashboardService->getDashboardData();
        
        // Si es admin o encargado, incluir datos del mapa de campo
        if (auth()->user()->hasAnyRole(['admin', 'encargado'])) {
            $data = array_merge($data, $this->getMapaCampoData());
        }
        
        return view('home', $data);
    }

    /**
     * Obtiene los datos necesarios para el mapa de campo completo
     * Replica la lógica de ReportController::mapaCampo()
     */
    private function getMapaCampoData(): array
    {
        // Obtener reportes/hallazgos aprobados
        $reports = \App\Models\Report::with(['person', 'condicionInicial', 'incidentType'])
            ->where('aprobado', 1)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderByDesc('id')
            ->get()
            ->map(function ($report) {
                $hasAnimalFiles = \App\Models\AnimalFile::whereHas('animal', function ($q) use ($report) {
                    $q->where('reporte_id', $report->id);
                })->exists();
                
                return [
                    'id' => $report->id,
                    'latitud' => $report->latitud,
                    'longitud' => $report->longitud,
                    'urgencia' => $report->urgencia,
                    'incendio_id' => $report->incendio_id,
                    'direccion' => $report->direccion,
                    'tiene_hoja_vida' => $hasAnimalFiles,
                    'condicion_inicial' => $report->condicionInicial ? [
                        'nombre' => $report->condicionInicial->nombre,
                    ] : null,
                    'incident_type' => $report->incidentType ? [
                        'nombre' => $report->incidentType->nombre,
                    ] : null,
                ];
            });

        // Agregar reporte simulado de incendio (igual que en ReportController)
        $reports->push([
            'id' => 'simulado',
            'latitud' => '-17.718397',
            'longitud' => '-60.774994',
            'urgencia' => 5,
            'incendio_id' => 1,
            'direccion' => 'San Jose de Chiquitos, Santa Cruz, Bolivia',
            'tiene_hoja_vida' => false,
            'condicion_inicial' => [
                'nombre' => 'Hallazgo',
            ],
            'incident_type' => [
                'nombre' => 'Incendio forestal',
            ],
        ]);

        // Obtener focos de calor (intenta primero desde API de integración, luego FIRMS)
        $focosCalor = $this->focosCalorService->getRecentHotspotsWithFallback(2);
        $focosCalorFormatted = $this->focosCalorService->formatForMap($focosCalor);

        // Obtener liberaciones
        $releases = \App\Models\Release::with(['animalFile.species', 'animalFile.animal', 'animalFile.animalStatus'])
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($release) {
                $animalFile = $release->animalFile;
                return [
                    'id' => $release->id,
                    'latitud' => $release->latitud,
                    'longitud' => $release->longitud,
                    'direccion' => $release->direccion,
                    'detalle' => $release->detalle,
                    'fecha' => $release->created_at ? $release->created_at->format('d/m/Y') : null,
                    'especie_id' => $animalFile->especie_id ?? null,
                    'especie' => $animalFile->species ? [
                        'id' => $animalFile->species->id,
                        'nombre' => $animalFile->species->nombre,
                    ] : null,
                    'animal' => $animalFile->animal ? [
                        'id' => $animalFile->animal->id,
                        'nombre' => $animalFile->animal->nombre,
                    ] : null,
                    'imagen_url' => $release->imagen_url,
                ];
            });

        // Obtener especies para el filtro
        $speciesIds = $releases->pluck('especie_id')->filter()->unique();
        $species = Species::whereIn('id', $speciesIds)->orderBy('nombre')->get(['id', 'nombre']);

        return [
            'reports' => $reports,
            'focosCalorFormatted' => $focosCalorFormatted,
            'releases' => $releases,
            'species' => $species,
        ];
    }

    /**
     * Exporta el dashboard completo a PDF
     */
    public function exportPdf(): Response
    {
        $data = $this->dashboardService->getDashboardData();
        
        // Si es admin o encargado, incluir datos del mapa de campo
        if (auth()->user()->hasAnyRole(['admin', 'encargado'])) {
            $data = array_merge($data, $this->getMapaCampoData());
        }
        
        // Añadir fecha de generación
        $data['fechaGeneracion'] = Carbon::now()->format('d/m/Y H:i:s');
        $data['usuario'] = auth()->user();
        
        // Generar PDF
        $pdf = Pdf::loadView('dashboard.pdf', $data);
        
        // Nombre del archivo
        $fileName = 'dashboard_' . date('d_m_Y_H_i_s') . '.pdf';
        
        return $pdf->download($fileName);
    }

    /**
     * Exporta el dashboard completo a CSV
     */
    public function exportCsv(): StreamedResponse
    {
        $data = $this->dashboardService->getDashboardData();
        
        // Si es admin o encargado, incluir datos del mapa de campo
        if (auth()->user()->hasAnyRole(['admin', 'encargado'])) {
            $data = array_merge($data, $this->getMapaCampoData());
        }
        
        $fechaGeneracion = Carbon::now()->format('d/m/Y H:i:s');
        $usuario = auth()->user();
        $fileName = 'dashboard_' . date('d_m_Y_H_i_s') . '.csv';
        
        return new StreamedResponse(function() use ($data, $fechaGeneracion, $usuario) {
            $handle = fopen('php://output', 'w');
            
            // BOM para UTF-8 (para que Excel abra correctamente caracteres especiales)
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezado
            fputcsv($handle, ['PANEL DE CONTROL - SISTEMA DE RESCATE DE ANIMALES']);
            fputcsv($handle, ['Reporte Administrativo Completo']);
            fputcsv($handle, ['Generado el: ' . $fechaGeneracion]);
            fputcsv($handle, ['Usuario: ' . ($usuario->name ?? 'N/A')]);
            fputcsv($handle, []);
            
            if ($usuario->hasAnyRole(['admin', 'encargado'])) {
                // SECCIÓN 1: RESUMEN EJECUTIVO
                fputcsv($handle, ['=== RESUMEN EJECUTIVO ===']);
                fputcsv($handle, []);
                
                // KPIs Principales
                fputcsv($handle, ['KPIs PRINCIPALES']);
                fputcsv($handle, ['Métrica', 'Valor', 'Detalle']);
                fputcsv($handle, [
                    'Hallazgos Pendientes',
                    $data['pendingReportsCount'] ?? 0,
                    ($data['totalReports'] ?? 0) > 0 ? round((($data['pendingReportsCount'] ?? 0) / ($data['totalReports'] ?? 1)) * 100, 2) . '% del total reportado' : '0% del total reportado'
                ]);
                fputcsv($handle, [
                    'Solicitudes Pendientes',
                    ($data['pendingRescuersCount'] ?? 0) + ($data['pendingVeterinariansCount'] ?? 0) + ($data['pendingCaregiversCount'] ?? 0),
                    'Revisión requerida'
                ]);
                fputcsv($handle, [
                    'Animales en Sistema',
                    $data['totalAnimals'] ?? 0,
                    'Total registrados'
                ]);
                fputcsv($handle, [
                    'Mensajes Nuevos',
                    $data['unreadMessagesCount'] ?? 0,
                    'Bandeja de entrada'
                ]);
                fputcsv($handle, []);
                
                // KPIs de Actividad
                fputcsv($handle, ['ACTIVIDAD']);
                fputcsv($handle, ['Tipo', 'Cantidad']);
                fputcsv($handle, ['Rescatados', $data['animalsBeingRescued'] ?? 0]);
                fputcsv($handle, ['Trasladados', $data['animalsBeingTransferred'] ?? 0]);
                fputcsv($handle, ['Tratados', $data['animalsBeingTreated'] ?? 0]);
                fputcsv($handle, []);
                
                // KPIs de Eficacia
                fputcsv($handle, ['EFICACIA']);
                fputcsv($handle, ['Métrica', 'Porcentaje', 'Detalle']);
                $effAttended = $data['efficiencyAttendedRescued'] ?? ['attended' => 0, 'rescued' => 0, 'percentage' => 0];
                fputcsv($handle, [
                    'Atendidos/Rescatados',
                    $effAttended['percentage'] . '%',
                    $effAttended['attended'] . ' / ' . $effAttended['rescued']
                ]);
                $effReady = $data['efficiencyReadyAttended'] ?? ['ready' => 0, 'attended' => 0, 'percentage' => 0];
                fputcsv($handle, [
                    'Listos/Atendidos',
                    $effReady['percentage'] . '%',
                    $effReady['ready'] . ' / ' . $effReady['attended']
                ]);
                fputcsv($handle, []);
                
                // KPI de Efectividad
                fputcsv($handle, ['EFECTIVIDAD']);
                fputcsv($handle, ['Métrica', 'Porcentaje', 'Detalle']);
                $effReleased = $data['effectivenessReleasedRescued'] ?? ['released' => 0, 'rescued' => 0, 'percentage' => 0];
                fputcsv($handle, [
                    'Liberados/Rescatados',
                    $effReleased['percentage'] . '%',
                    $effReleased['released'] . ' / ' . $effReleased['rescued']
                ]);
                fputcsv($handle, []);
                
                // SECCIÓN 2: ANÁLISIS Y ESTADÍSTICAS
                fputcsv($handle, ['=== ANÁLISIS Y ESTADÍSTICAS ===']);
                fputcsv($handle, []);
                
                // Estadísticas por Mes
                fputcsv($handle, ['ESTADÍSTICAS MENSUALES (Últimos 6 meses)']);
                fputcsv($handle, ['Tipo', 'Total', 'Promedio Mensual']);
                $reportsByMonth = $data['reportsByMonth'] ?? [];
                $reportsTotal = array_sum($reportsByMonth);
                $reportsAvg = count($reportsByMonth) > 0 ? round($reportsTotal / count($reportsByMonth)) : 0;
                fputcsv($handle, ['Reportes', $reportsTotal, $reportsAvg]);
                
                $transfersByMonth = $data['transfersByMonth'] ?? [];
                $transfersTotal = array_sum($transfersByMonth);
                $transfersAvg = count($transfersByMonth) > 0 ? round($transfersTotal / count($transfersByMonth)) : 0;
                fputcsv($handle, ['Traslados', $transfersTotal, $transfersAvg]);
                
                $releasesByMonth = $data['releasesByMonth'] ?? [];
                $releasesTotal = array_sum($releasesByMonth);
                $releasesAvg = count($releasesByMonth) > 0 ? round($releasesTotal / count($releasesByMonth)) : 0;
                fputcsv($handle, ['Liberaciones', $releasesTotal, $releasesAvg]);
                
                $animalFilesByMonth = $data['animalFilesByMonth'] ?? [];
                $animalFilesTotal = array_sum($animalFilesByMonth);
                $animalFilesAvg = count($animalFilesByMonth) > 0 ? round($animalFilesTotal / count($animalFilesByMonth)) : 0;
                fputcsv($handle, ['Hojas de Animal', $animalFilesTotal, $animalFilesAvg]);
                fputcsv($handle, []);
                
                // Animales por Estado
                if (isset($data['animalsByStatus']) && count($data['animalsByStatus']) > 0) {
                    fputcsv($handle, ['ANIMALES POR ESTADO']);
                    fputcsv($handle, ['Estado', 'Cantidad']);
                    foreach ($data['animalsByStatus'] as $status => $count) {
                        fputcsv($handle, [$status, $count]);
                    }
                    fputcsv($handle, []);
                }
                
                // Top 5 Voluntarios
                if (isset($data['topVolunteers']) && count($data['topVolunteers']) > 0) {
                    fputcsv($handle, ['TOP 5 VOLUNTARIOS MÁS ACTIVOS']);
                    fputcsv($handle, ['#', 'Nombre', 'Email', 'Total', 'Hallazgos', 'Traslados', 'Evaluaciones']);
                    foreach ($data['topVolunteers'] as $index => $volunteer) {
                        $position = $index === 0 ? '🥇' : ($index === 1 ? '🥈' : ($index === 2 ? '🥉' : ($index + 1)));
                        fputcsv($handle, [
                            $position,
                            $volunteer['nombre'] ?? 'Sin nombre',
                            $volunteer['email'] ?? '',
                            $volunteer['total'] ?? 0,
                            $volunteer['reports'] ?? 0,
                            $volunteer['transfers'] ?? 0,
                            $volunteer['evaluations'] ?? 0
                        ]);
                    }
                    fputcsv($handle, []);
                }
                
                // Solicitudes de Voluntariado
                if (isset($data['applicationsByType'])) {
                    fputcsv($handle, ['SOLICITUDES DE VOLUNTARIADO']);
                    fputcsv($handle, ['Tipo', 'Cantidad']);
                    foreach ($data['applicationsByType'] as $type => $count) {
                        fputcsv($handle, [$type, $count]);
                    }
                    fputcsv($handle, []);
                }
                
                // SECCIÓN 3: MAPA DE CAMPO
                fputcsv($handle, ['=== MAPA DE CAMPO ===']);
                fputcsv($handle, []);
                
                $reportsCount = isset($data['reports']) ? (is_countable($data['reports']) ? count($data['reports']) : 0) : 0;
                $releasesCount = isset($data['releases']) ? (is_countable($data['releases']) ? count($data['releases']) : 0) : 0;
                $focosCount = isset($data['focosCalorFormatted']) ? (is_countable($data['focosCalorFormatted']) ? count($data['focosCalorFormatted']) : 0) : 0;
                $speciesCount = isset($data['species']) ? (is_countable($data['species']) ? count($data['species']) : 0) : 0;
                
                fputcsv($handle, ['RESUMEN GEOGRÁFICO']);
                fputcsv($handle, ['Métrica', 'Cantidad']);
                fputcsv($handle, ['Hallazgos Aprobados', $reportsCount]);
                fputcsv($handle, ['Animales Liberados', $releasesCount]);
                fputcsv($handle, ['Focos de Calor Recientes', $focosCount]);
                fputcsv($handle, ['Especies Registradas', $speciesCount]);
                fputcsv($handle, []);
            } else {
                // Vista para otros roles
                fputcsv($handle, ['=== RESUMEN ===']);
                fputcsv($handle, []);
                fputcsv($handle, ['Métrica', 'Valor']);
                fputcsv($handle, ['Animales Rescatados', $data['totalAnimals'] ?? 0]);
                $released = $data['releasedAnimals'] ?? 0;
                $total = $data['totalAnimals'] ?? 0;
                $rpct = $total > 0 ? round(($released / $total) * 100, 2) : 0;
                fputcsv($handle, ['Devueltos al Hábitat', $released . ' (' . $rpct . '% tasa de éxito)']);
                fputcsv($handle, ['Hallazgos Recibidos', $data['totalReports'] ?? 0]);
                fputcsv($handle, []);
            }
            
            // Footer
            fputcsv($handle, []);
            fputcsv($handle, ['Generado el ' . $fechaGeneracion . ' | Sistema de Rescate de Animales']);
            fputcsv($handle, ['Este es un documento generado automáticamente. Para más información, consulte el sistema web.']);
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}