<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use App\Services\Fire\FocosCalorService;
use App\Models\Species;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
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

        // Obtener focos de calor
        $focosCalor = $this->focosCalorService->getRecentHotspots(2);
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
}