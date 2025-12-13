<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use App\Services\Fire\FocosCalorService;
use App\Services\Fire\ExternalFireReportsService;
use App\Models\Species;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HomeController extends Controller
{
    protected $dashboardService;
    protected $focosCalorService;
    protected $externalFireReportsService;

    public function __construct(DashboardService $dashboardService, FocosCalorService $focosCalorService, ExternalFireReportsService $externalFireReportsService)
    {
        $this->middleware('auth');
        $this->dashboardService = $dashboardService;
        $this->focosCalorService = $focosCalorService;
        $this->externalFireReportsService = $externalFireReportsService;
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

        // Obtener reportes externos de incendios
        $externalFireReports = $this->externalFireReportsService->getExternalFireReports();
        $externalFireReportsFormatted = $this->externalFireReportsService->formatForMap($externalFireReports);

        return [
            'reports' => $reports,
            'focosCalorFormatted' => $focosCalorFormatted,
            'releases' => $releases,
            'species' => $species,
            'externalFireReportsFormatted' => $externalFireReportsFormatted,
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
     * Exporta el dashboard completo a Excel
     */
    public function exportExcel(): StreamedResponse
    {
        $data = $this->dashboardService->getDashboardData();
        
        // Si es admin o encargado, incluir datos del mapa de campo
        if (auth()->user()->hasAnyRole(['admin', 'encargado'])) {
            $data = array_merge($data, $this->getMapaCampoData());
        }
        
        $fechaGeneracion = Carbon::now()->format('d/m/Y H:i:s');
        $usuario = auth()->user();
        $fileName = 'dashboard_' . date('d_m_Y_H_i_s') . '.xlsx';
        
        return new StreamedResponse(function() use ($data, $fechaGeneracion, $usuario) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Título Principal
            $sheet->setCellValue('A1', 'PANEL DE CONTROL - SISTEMA DE RESCATE');
            $sheet->mergeCells('A1:G1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '4B5563']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Metadatos
            $sheet->setCellValue('A2', 'Generado el: ' . $fechaGeneracion);
            $sheet->mergeCells('A2:C2');
            $sheet->setCellValue('D2', 'Usuario: ' . ($usuario->name ?? 'N/A'));
            $sheet->mergeCells('D2:G2');
            
            $row = 4;
            
            if ($usuario->hasAnyRole(['admin', 'encargado'])) {
                // ==========================================
                // 1. RESUMEN GENERAL (KPIs Principales)
                // ==========================================
                $sheet->setCellValue('A' . $row, '1. RESUMEN GENERAL');
                $sheet->mergeCells('A' . $row . ':G' . $row);
                $this->applyExcelStyles($sheet, 'header', 'A' . $row . ':G' . $row);
                $row += 2;
                
                // Tabla de Resumen
                $sheet->setCellValue('A' . $row, 'Métrica');
                $sheet->setCellValue('B' . $row, 'Valor');
                $sheet->setCellValue('C' . $row, 'Descripción / Porcentaje');
                $sheet->mergeCells('C' . $row . ':G' . $row);
                $this->applyExcelStyles($sheet, 'subheader', 'A' . $row . ':G' . $row);
                $row++;
                
                // Datos Resumen
                $kpis = [
                    ['Animales Registrados', $data['totalAnimals'] ?? 0, 'Total histórico en sistema'],
                    ['Hallazgos Pendientes', $data['pendingReportsCount'] ?? 0, ($data['totalReports'] ?? 0) > 0 ? round((($data['pendingReportsCount'] ?? 0) / ($data['totalReports'] ?? 1)) * 100, 1) . '% del total reportado' : '0%'],
                    ['Solicitudes Pendientes', ($data['pendingRescuersCount'] ?? 0) + ($data['pendingVeterinariansCount'] ?? 0) + ($data['pendingCaregiversCount'] ?? 0), 'Nuevos voluntarios/personal'],
                    ['Mensajes Nuevos', $data['unreadMessagesCount'] ?? 0, 'Bandeja de entrada no leída']
                ];
                
                foreach ($kpis as $kpi) {
                    $sheet->setCellValue('A' . $row, $kpi[0]);
                    $sheet->setCellValue('B' . $row, $kpi[1]);
                    $sheet->setCellValue('C' . $row, $kpi[2]);
                    $sheet->mergeCells('C' . $row . ':G' . $row);
                    $this->applyExcelStyles($sheet, 'row_border', 'A' . $row . ':G' . $row);
                    $row++;
                }
                $row += 2;

                // ==========================================
                // 2. GESTIÓN OPERATIVA (Actividad y Eficacia)
                // ==========================================
                $sheet->setCellValue('A' . $row, '2. GESTIÓN OPERATIVA');
                $sheet->mergeCells('A' . $row . ':G' . $row);
                $this->applyExcelStyles($sheet, 'header', 'A' . $row . ':G' . $row);
                $row += 2;

                // 2.1 Actividad Actual
                $sheet->setCellValue('A' . $row, 'Estado Actual de Animales');
                $sheet->mergeCells('A' . $row . ':C' . $row);
                $this->applyExcelStyles($sheet, 'subheader', 'A' . $row . ':C' . $row);
                
                // 2.2 Eficacia y Efectividad
                $sheet->setCellValue('E' . $row, 'Indicadores de Desempeño');
                $sheet->mergeCells('E' . $row . ':G' . $row);
                $this->applyExcelStyles($sheet, 'subheader', 'E' . $row . ':G' . $row);
                $row++;

                // Cabeceras columnas
                $sheet->setCellValue('A' . $row, 'Estado');
                $sheet->setCellValue('B' . $row, 'Cant.');
                $sheet->setCellValue('C' . $row, '% del Total');
                
                $sheet->setCellValue('E' . $row, 'Indicador');
                $sheet->setCellValue('F' . $row, '%');
                $sheet->setCellValue('G' . $row, 'Relación');
                
                $this->applyExcelStyles($sheet, 'bold_center', 'A' . $row . ':G' . $row);
                $row++;

                // Datos Actividad
                $totalActive = ($data['animalsBeingRescued'] ?? 0) + ($data['animalsBeingTransferred'] ?? 0) + ($data['animalsBeingTreated'] ?? 0);
                $pRescued = $totalActive > 0 ? round(($data['animalsBeingRescued'] / $totalActive) * 100, 1) : 0;
                $pTransferred = $totalActive > 0 ? round(($data['animalsBeingTransferred'] / $totalActive) * 100, 1) : 0;
                $pTreated = $totalActive > 0 ? round(($data['animalsBeingTreated'] / $totalActive) * 100, 1) : 0;

                // Fila 1: Rescatados y Eficacia Atendidos
                $effAttended = $data['efficiencyAttendedRescued'] ?? ['attended' => 0, 'rescued' => 0, 'percentage' => 0];
                
                $sheet->setCellValue('A' . $row, 'En Proceso de Rescate');
                $sheet->setCellValue('B' . $row, $data['animalsBeingRescued'] ?? 0);
                $sheet->setCellValue('C' . $row, $pRescued . '%');
                
                $sheet->setCellValue('E' . $row, 'Atendidos vs Rescatados');
                $sheet->setCellValue('F' . $row, $effAttended['percentage'] . '%');
                $sheet->setCellValue('G' . $row, $effAttended['attended'] . '/' . $effAttended['rescued']);
                
                $this->applyColorScale($sheet, 'F' . $row, $effAttended['percentage']);
                $row++;

                // Fila 2: Trasladados y Eficacia Listos
                $effReady = $data['efficiencyReadyAttended'] ?? ['ready' => 0, 'attended' => 0, 'percentage' => 0];
                
                $sheet->setCellValue('A' . $row, 'En Traslado');
                $sheet->setCellValue('B' . $row, $data['animalsBeingTransferred'] ?? 0);
                $sheet->setCellValue('C' . $row, $pTransferred . '%');
                
                $sheet->setCellValue('E' . $row, 'Listos vs Atendidos');
                $sheet->setCellValue('F' . $row, $effReady['percentage'] . '%');
                $sheet->setCellValue('G' . $row, $effReady['ready'] . '/' . $effReady['attended']);
                
                $this->applyColorScale($sheet, 'F' . $row, $effReady['percentage']);
                $row++;

                // Fila 3: Tratados y Efectividad Liberación
                $effReleased = $data['effectivenessReleasedRescued'] ?? ['released' => 0, 'rescued' => 0, 'percentage' => 0];
                
                $sheet->setCellValue('A' . $row, 'En Tratamiento');
                $sheet->setCellValue('B' . $row, $data['animalsBeingTreated'] ?? 0);
                $sheet->setCellValue('C' . $row, $pTreated . '%');
                
                $sheet->setCellValue('E' . $row, 'Liberados vs Rescatados');
                $sheet->setCellValue('F' . $row, $effReleased['percentage'] . '%');
                $sheet->setCellValue('G' . $row, $effReleased['released'] . '/' . $effReleased['rescued']);
                
                $this->applyColorScale($sheet, 'F' . $row, $effReleased['percentage'], true); // true para usar azul en 100%
                $row += 2;

                // ==========================================
                // 3. TENDENCIAS MENSUALES
                // ==========================================
                $sheet->setCellValue('A' . $row, '3. TENDENCIAS MENSUALES (Últimos 6 meses)');
                $sheet->mergeCells('A' . $row . ':G' . $row);
                $this->applyExcelStyles($sheet, 'header', 'A' . $row . ':G' . $row);
                $row += 2;

                $sheet->setCellValue('A' . $row, 'Categoría');
                $sheet->setCellValue('B' . $row, 'Total Periodo');
                $sheet->setCellValue('C' . $row, 'Promedio Mensual');
                $sheet->mergeCells('C' . $row . ':G' . $row);
                $this->applyExcelStyles($sheet, 'subheader', 'A' . $row . ':G' . $row);
                $row++;

                // Datos Mensuales
                $monthlyStats = [
                    'Nuevos Reportes' => $data['reportsByMonth'] ?? [],
                    'Traslados Realizados' => $data['transfersByMonth'] ?? [],
                    'Liberaciones Exitosas' => $data['releasesByMonth'] ?? [],
                    'Hojas de Vida Creadas' => $data['animalFilesByMonth'] ?? []
                ];

                foreach ($monthlyStats as $label => $values) {
                    $total = array_sum($values);
                    $avg = count($values) > 0 ? round($total / count($values), 1) : 0;
                    
                    $sheet->setCellValue('A' . $row, $label);
                    $sheet->setCellValue('B' . $row, $total);
                    $sheet->setCellValue('C' . $row, $avg . ' / mes');
                    $sheet->mergeCells('C' . $row . ':G' . $row);
                    $this->applyExcelStyles($sheet, 'row_border', 'A' . $row . ':G' . $row);
                    $row++;
                }
                $row += 2;

                // ==========================================
                // 4. RECURSOS HUMANOS (Voluntarios)
                // ==========================================
                if (isset($data['topVolunteers']) && count($data['topVolunteers']) > 0) {
                    $sheet->setCellValue('A' . $row, '4. RECURSOS HUMANOS DESTACADOS');
                    $sheet->mergeCells('A' . $row . ':G' . $row);
                    $this->applyExcelStyles($sheet, 'header', 'A' . $row . ':G' . $row);
                    $row += 2;

                    $sheet->setCellValue('A' . $row, 'Top 5 Voluntarios Más Activos');
                    $sheet->mergeCells('A' . $row . ':G' . $row);
                    $this->applyExcelStyles($sheet, 'subheader', 'A' . $row . ':G' . $row);
                    $row++;
                    
                    $headers = ['Pos.', 'Voluntario', 'Email', 'Total Acciones', 'Hallazgos', 'Traslados', 'Evaluaciones'];
                    $col = 'A';
                    foreach ($headers as $header) {
                        $sheet->setCellValue($col . $row, $header);
                        $col++;
                    }
                    $this->applyExcelStyles($sheet, 'bold_center', 'A' . $row . ':G' . $row);
                    $row++;
                    
                    foreach ($data['topVolunteers'] as $index => $volunteer) {
                        $position = $index === 0 ? '🥇' : ($index === 1 ? '🥈' : ($index === 2 ? '🥉' : ($index + 1)));
                        $sheet->setCellValue('A' . $row, $position);
                        $sheet->setCellValue('B' . $row, $volunteer['nombre'] ?? 'Sin nombre');
                        $sheet->setCellValue('C' . $row, $volunteer['email'] ?? '');
                        $sheet->setCellValue('D' . $row, $volunteer['total'] ?? 0);
                        $sheet->setCellValue('E' . $row, $volunteer['reports'] ?? 0);
                        $sheet->setCellValue('F' . $row, $volunteer['transfers'] ?? 0);
                        $sheet->setCellValue('G' . $row, $volunteer['evaluations'] ?? 0);
                        
                        // Centrar columnas numéricas
                        $sheet->getStyle('D' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $row++;
                    }
                }
                $row += 2;

                // ==========================================
                // 5. RESUMEN GEOGRÁFICO
                // ==========================================
                $sheet->setCellValue('A' . $row, '5. RESUMEN GEOGRÁFICO');
                $sheet->mergeCells('A' . $row . ':G' . $row);
                $this->applyExcelStyles($sheet, 'header', 'A' . $row . ':G' . $row);
                $row += 2;
                
                $geoMetrics = [
                    ['Hallazgos Aprobados', isset($data['reports']) ? (is_countable($data['reports']) ? count($data['reports']) : 0) : 0],
                    ['Animales Liberados', isset($data['releases']) ? (is_countable($data['releases']) ? count($data['releases']) : 0) : 0],
                    ['Focos de Calor (Recientes)', isset($data['focosCalorFormatted']) ? (is_countable($data['focosCalorFormatted']) ? count($data['focosCalorFormatted']) : 0) : 0],
                    ['Especies Distintas', isset($data['species']) ? (is_countable($data['species']) ? count($data['species']) : 0) : 0]
                ];

                $sheet->setCellValue('A' . $row, 'Elemento Geográfico');
                $sheet->mergeCells('A' . $row . ':C' . $row);
                $sheet->setCellValue('D' . $row, 'Cantidad en Mapa');
                $sheet->mergeCells('D' . $row . ':G' . $row);
                $this->applyExcelStyles($sheet, 'subheader', 'A' . $row . ':G' . $row);
                $row++;

                foreach ($geoMetrics as $metric) {
                    $sheet->setCellValue('A' . $row, $metric[0]);
                    $sheet->mergeCells('A' . $row . ':C' . $row);
                    $sheet->setCellValue('D' . $row, $metric[1]);
                    $sheet->mergeCells('D' . $row . ':G' . $row);
                    $this->applyExcelStyles($sheet, 'row_border', 'A' . $row . ':G' . $row);
                    $row++;
                }

            } else {
                // ==========================================
                // VISTA SIMPLIFICADA (No Admin)
                // ==========================================
                $sheet->setCellValue('A' . $row, 'RESUMEN PERSONAL');
                $sheet->mergeCells('A' . $row . ':E' . $row);
                $this->applyExcelStyles($sheet, 'header', 'A' . $row . ':E' . $row);
                $row += 2;
                
                $sheet->setCellValue('A' . $row, 'Métrica');
                $sheet->setCellValue('B' . $row, 'Valor');
                $this->applyExcelStyles($sheet, 'subheader', 'A' . $row . ':B' . $row);
                $row++;
                
                $sheet->setCellValue('A' . $row, 'Animales Rescatados');
                $sheet->setCellValue('B' . $row, $data['totalAnimals'] ?? 0);
                $row++;
                
                $released = $data['releasedAnimals'] ?? 0;
                $total = $data['totalAnimals'] ?? 0;
                $rpct = $total > 0 ? round(($released / $total) * 100, 2) : 0;
                $sheet->setCellValue('A' . $row, 'Devueltos al Hábitat');
                $sheet->setCellValue('B' . $row, $released . ' (' . $rpct . '% tasa de éxito)');
                $row++;
                
                $sheet->setCellValue('A' . $row, 'Hallazgos Recibidos');
                $sheet->setCellValue('B' . $row, $data['totalReports'] ?? 0);
                $row++;
            }
            
            // Auto-size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
    
    /**
     * Helper para aplicar escala de colores a celdas de porcentaje
     */
    private function applyColorScale($sheet, $cellCoordinate, $percentage, $isRelease = false)
    {
        $colorCode = 'DC3545'; // Red (Bootstrap Danger)
        $fontColor = 'FFFFFF'; // White text
        
        if ($percentage == 100) {
            $colorCode = $isRelease ? '17A2B8' : '28A745'; // Blue (Info) or Green (Success)
        } elseif ($percentage >= 50) {
            $colorCode = 'FFC107'; // Yellow (Warning)
            $fontColor = '000000'; // Black text
        }
        
        $sheet->getStyle($cellCoordinate)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB($colorCode);
            
        $sheet->getStyle($cellCoordinate)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($fontColor));
        $sheet->getStyle($cellCoordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
    
    private function applyExcelStyles($sheet, $type, $range)
    {
        switch ($type) {
            case 'header':
                $sheet->getStyle($range)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '4B5563'] // Azul gris oscuro
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM]
                    ]
                ]);
                $sheet->getRowDimension($sheet->getCell(explode(':', $range)[0])->getRow())->setRowHeight(30);
                break;
            case 'subheader':
                $sheet->getStyle($range)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '6B7280'] // Azul gris medio
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                break;
            case 'bold':
                $sheet->getStyle($range)->getFont()->setBold(true);
                break;
            case 'bold_center':
                $sheet->getStyle($range)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
                break;
            case 'row_border':
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'E5E7EB']]
                    ]
                ]);
                break;
        }
    }
}
