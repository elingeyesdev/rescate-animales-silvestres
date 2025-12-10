<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Transfer;
use App\Models\AnimalFile;
use App\Models\Center;
use App\Models\Release;
use App\Models\MedicalEvaluation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra la página principal de reportes con pestañas
     */
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'activity');
        $subtab = $request->get('subtab', 'states');
        
        if ($tab === 'activity') {
            if ($subtab === 'health') {
                return $this->healthAnimalReport();
            }
            return $this->activityReports();
        } elseif ($tab === 'management') {
            return $this->managementReports();
        }
        
        return $this->activityReports();
    }

    /**
     * Reportes de Actividad
     */
    private function activityReports(): View
    {
        // Obtener todos los reportes aprobados
        $reports = Report::where('aprobado', true)
            ->with([
                'animals' => function($query) {
                    $query->with(['animalFiles' => function($q) {
                        $q->with(['release', 'center']);
                    }]);
                },
                'transfers' => function($query) {
                    $query->where('primer_traslado', true)->with('center');
                }
            ])
            ->get();

        // Separar por estados
        $enPeligro = [];
        $rescatados = [];
        $tratados = [];
        $liberados = [];
        
        foreach ($reports as $report) {
            // Extraer provincia de la dirección
            $province = $this->extractProvince($report->direccion);
            if (!$province) {
                $province = 'Sin Provincia';
            }
            
            // Determinar el estado del reporte
            $hasFirstTransfer = $report->transfers->isNotEmpty();
            $animals = $report->animals;
            $animalFiles = $animals->flatMap->animalFiles;
            $hasAnimalFile = $animalFiles->isNotEmpty();
            
            // Obtener nombre del animal (tomar el primero si hay varios)
            $animalNombre = null;
            if ($animals->isNotEmpty()) {
                $firstAnimal = $animals->first();
                $animalNombre = $firstAnimal->nombre ?? 'Sin nombre';
            }
            
            // Buscar si hay release
            $release = null;
            $animalFileWithRelease = $animalFiles->first(function($animalFile) {
                return $animalFile->release !== null;
            });
            if ($animalFileWithRelease) {
                $release = $animalFileWithRelease->release;
            }
            
            // Obtener información del centro y fecha de traslado
            $center = null;
            $fechaTraslado = null;
            if ($hasFirstTransfer) {
                $firstTransfer = $report->transfers->first();
                if ($firstTransfer) {
                    if ($firstTransfer->centro_id) {
                        $center = $firstTransfer->center;
                    }
                    $fechaTraslado = $firstTransfer->created_at;
                }
            }
            
            // Para tratados, obtener el centro desde la hoja de vida
            $treatmentCenter = null;
            $animalFileCreatedAt = null;
            if ($hasAnimalFile) {
                $firstAnimalFile = $animalFiles->first();
                if ($firstAnimalFile && $firstAnimalFile->centro_id) {
                    $treatmentCenter = $firstAnimalFile->center;
                }
                // Si no tiene centro en la hoja, usar el del traslado
                if (!$treatmentCenter && $center) {
                    $treatmentCenter = $center;
                }
                $animalFileCreatedAt = $firstAnimalFile->created_at;
            }
            
            // Calcular tiempo transcurrido desde el hallazgo
            $tiempoTranscurrido = $this->calculateTimeElapsed($report->created_at);
            
            // Calcular tiempo transcurrido desde el primer tratamiento (para tratados)
            $tiempoDesdeTratamiento = null;
            if ($hasAnimalFile && $animalFileCreatedAt) {
                $tiempoDesdeTratamiento = $this->calculateTimeElapsed($animalFileCreatedAt);
            }
            
            $reportData = [
                'id' => $report->id,
                'province' => $province,
                'nombre' => $animalNombre,
                'fecha_hallazgo' => $report->created_at,
                'tiempo_transcurrido' => $tiempoTranscurrido,
            ];
            
            // Clasificar según estado
            if ($release) {
                // Liberado
                $reportData['fecha_liberacion'] = $release->created_at;
                $liberados[] = $reportData;
            } elseif ($hasAnimalFile) {
                // Tratado
                $reportData['centro'] = $treatmentCenter;
                $reportData['fecha_tratamiento'] = $animalFileCreatedAt;
                $reportData['tiempo_desde_tratamiento'] = $tiempoDesdeTratamiento;
                $tratados[] = $reportData;
            } elseif ($hasFirstTransfer) {
                // Rescatado (En Traslado)
                $reportData['centro'] = $center;
                $reportData['fecha_traslado'] = $fechaTraslado;
                $reportData['tiempo_hallazgo_traslado'] = $fechaTraslado ? $this->calculateTimeElapsed($report->created_at, $fechaTraslado) : null;
                $rescatados[] = $reportData;
            } else {
                // En Peligro
                $enPeligro[] = $reportData;
            }
        }
        
        // Ordenar cada lista por provincia y luego por ID
        $sortFunction = function($a, $b) {
            if ($a['province'] !== $b['province']) {
                return strcmp($a['province'], $b['province']);
            }
            return $a['id'] - $b['id'];
        };
        
        usort($enPeligro, $sortFunction);
        usort($rescatados, $sortFunction);
        usort($tratados, $sortFunction);
        usort($liberados, $sortFunction);
        
        // Calcular totales
        $totals = [
            'en_peligro' => count($enPeligro),
            'rescatados' => count($rescatados),
            'tratados' => count($tratados),
            'liberados' => count($liberados),
        ];
        
        return view('reports.index', [
            'tab' => 'activity',
            'subtab' => 'states',
            'enPeligro' => $enPeligro,
            'rescatados' => $rescatados,
            'tratados' => $tratados,
            'liberados' => $liberados,
            'totals' => $totals,
        ]);
    }
    
    /**
     * Calcula el tiempo transcurrido desde una fecha hasta otra (o hasta ahora)
     */
    private function calculateTimeElapsed($dateFrom, $dateTo = null): string
    {
        if (!$dateFrom) {
            return '-';
        }
        
        $dateFrom = Carbon::parse($dateFrom);
        $dateTo = $dateTo ? Carbon::parse($dateTo) : Carbon::now();
        $diff = $dateFrom->diff($dateTo);
        
        if ($diff->days > 0) {
            return $diff->days . ' día' . ($diff->days > 1 ? 's' : '');
        } elseif ($diff->h > 0) {
            return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
        } elseif ($diff->i > 0) {
            return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
        } else {
            return 'Menos de un minuto';
        }
    }

    /**
     * Reporte de Salud Animal Actual
     */
    private function healthAnimalReport(): View
    {
        // Obtener todos los AnimalFiles que no tienen release (animales en tratamiento)
        $animalFiles = AnimalFile::whereDoesntHave('release')
            ->with([
                'center',
                'animalStatus',
                'animal' => function($query) {
                    $query->with(['report' => function($q) {
                        $q->with('condicionInicial');
                    }]);
                },
                'medicalEvaluations' => function($query) {
                    $query->with('treatmentType')
                          ->orderBy('fecha', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->limit(1);
                }
            ])
            ->get();

        $healthData = [];
        
        foreach ($animalFiles as $animalFile) {
            $animal = $animalFile->animal;
            $report = $animal?->report;
            
            // Nombre del animal
            $nombreAnimal = $animal?->nombre ?? 'Sin nombre';
            
            // Nombre del centro
            $nombreCentro = $animalFile->center?->nombre ?? 'Sin centro asignado';
            
            // Diagnóstico inicial (descripción con la que se creó la hoja de vida del animal)
            $diagnosticoInicial = $animal?->descripcion ?? 'Sin diagnóstico inicial';
            
            // Fecha de creación de la hoja de vida
            $fechaCreacionHoja = $animalFile->created_at;
            
            // Última intervención médica
            $ultimaIntervencion = null;
            $fechaUltimaEvaluacion = null;
            $ultimaEvaluacion = $animalFile->medicalEvaluations->first();
            if ($ultimaEvaluacion) {
                $fechaIntervencion = $ultimaEvaluacion->fecha ?? $ultimaEvaluacion->created_at;
                if ($fechaIntervencion) {
                    $fechaIntervencion = Carbon::parse($fechaIntervencion);
                    $fechaUltimaEvaluacion = $fechaIntervencion;
                }
                $tipoTratamiento = $ultimaEvaluacion->treatmentType?->nombre ?? 'Sin tipo';
                $descripcion = $ultimaEvaluacion->descripcion ?? '';
                $diagnostico = $ultimaEvaluacion->diagnostico ?? '';
                
                $ultimaIntervencion = [
                    'fecha' => $fechaIntervencion,
                    'tipo' => $tipoTratamiento,
                    'descripcion' => $descripcion,
                    'diagnostico' => $diagnostico,
                ];
            }
            
            // Estado actual
            $estadoActual = $animalFile->animalStatus?->nombre ?? 'Sin estado';
            
            $healthData[] = [
                'centro' => $nombreCentro,
                'nombre_animal' => $nombreAnimal,
                'diagnostico_inicial' => $diagnosticoInicial,
                'fecha_creacion_hoja' => $fechaCreacionHoja,
                'fecha_ultima_evaluacion' => $fechaUltimaEvaluacion,
                'ultima_intervencion' => $ultimaIntervencion,
                'estado_actual' => $estadoActual,
            ];
        }
        
        // Ordenar por centro y luego por nombre del animal
        usort($healthData, function($a, $b) {
            if ($a['centro'] !== $b['centro']) {
                return strcmp($a['centro'], $b['centro']);
            }
            return strcmp($a['nombre_animal'], $b['nombre_animal']);
        });
        
        return view('reports.index', [
            'tab' => 'activity',
            'subtab' => 'health',
            'healthData' => $healthData,
            'enPeligro' => [],
            'rescatados' => [],
            'tratados' => [],
            'liberados' => [],
            'totals' => ['en_peligro' => 0, 'rescatados' => 0, 'tratados' => 0, 'liberados' => 0],
        ]);
    }

    /**
     * Reportes de Gestión (placeholder)
     */
    private function managementReports(): View
    {
        return view('reports.index', [
            'tab' => 'management',
            'enPeligro' => [],
            'rescatados' => [],
            'tratados' => [],
            'liberados' => [],
            'totals' => ['en_peligro' => 0, 'rescatados' => 0, 'tratados' => 0, 'liberados' => 0],
        ]);
    }

    /**
     * Extrae la provincia de una dirección
     */
    private function extractProvince(?string $address): ?string
    {
        if (!$address) {
            return null;
        }
        
        // Buscar patrón "Provincia X" en la dirección
        if (preg_match('/Provincia\s+([^,]+)/i', $address, $matches)) {
            $province = trim($matches[1]);
            // Limpiar espacios y caracteres extra
            $province = preg_replace('/\s+/', ' ', $province);
            return $province;
        }
        
        // Buscar "Cruceña" específicamente (puede estar sin "Provincia")
        if (preg_match('/(?:Provincia\s+)?(Cruceña)/i', $address, $matches)) {
            return 'Cruceña';
        }
        
        // Buscar otras provincias comunes
        $commonProvinces = ['Andrés Ibáñez', 'Warnes', 'Obispo Santistevan', 'Ichilo', 'Sara', 'Vallegrande', 'Florida', 'Manuel María Caballero', 'Chiquitos', 'Velasco', 'Guarayos', 'Ñuflo de Chávez', 'Ángel Sandoval', 'Germán Busch'];
        foreach ($commonProvinces as $province) {
            if (stripos($address, $province) !== false) {
                return $province;
            }
        }
        
        return null;
    }
}

