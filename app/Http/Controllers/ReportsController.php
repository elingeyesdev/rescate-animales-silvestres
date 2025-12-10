<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Transfer;
use App\Models\AnimalFile;
use App\Models\Center;
use App\Models\Release;
use App\Models\MedicalEvaluation;
use App\Models\AnimalStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                return $this->healthAnimalReport($request);
            }
            return $this->activityReports();
        } elseif ($tab === 'management') {
            $managementSubtab = $request->get('management_subtab', 'rescue');
            // Temporalmente deshabilitado: initial_review
            // if ($managementSubtab === 'initial_review') {
            //     return $this->initialReviewEfficiencyReport($request);
            // } elseif 
            if ($managementSubtab === 'treatment') {
                return $this->treatmentEfficiencyReport($request);
            } elseif ($managementSubtab === 'release') {
                return $this->releaseEfficiencyReport($request);
            }
            return $this->managementReports($request);
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
    private function healthAnimalReport(Request $request): View
    {
        // Obtener la primera fecha registrada en el sistema (fecha más antigua de creación de hoja de vida)
        $primeraFecha = AnimalFile::whereDoesntHave('release')
            ->min('created_at');
        
        // Obtener parámetros de fecha del request
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        // Establecer valores por defecto si no hay filtros
        if (!$fechaDesde && $primeraFecha) {
            $fechaDesde = Carbon::parse($primeraFecha)->format('Y-m-d');
        }
        if (!$fechaHasta) {
            $fechaHasta = Carbon::now()->format('Y-m-d');
        }
        
        // Construir query base
        $query = AnimalFile::whereDoesntHave('release')
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
            ]);
        
        // Aplicar filtro de fechas (fecha de inicio de tratamiento = created_at de AnimalFile)
        if ($fechaDesde) {
            $query->whereDate('created_at', '>=', Carbon::parse($fechaDesde)->startOfDay());
        }
        if ($fechaHasta) {
            $query->whereDate('created_at', '<=', Carbon::parse($fechaHasta)->endOfDay());
        }
        
        $animalFiles = $query->get();

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
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'enPeligro' => [],
            'rescatados' => [],
            'tratados' => [],
            'liberados' => [],
            'totals' => ['en_peligro' => 0, 'rescatados' => 0, 'tratados' => 0, 'liberados' => 0],
        ]);
    }

    /**
     * Reportes de Gestión
     */
    private function managementReports(Request $request): View
    {
        // Calcular eficacia mensual (últimos 30 días)
        $fechaInicio30Dias = Carbon::now()->subDays(30)->startOfDay();
        $fechaFin30Dias = Carbon::now()->endOfDay();
        
        $traslados30Dias = Transfer::where('primer_traslado', true)
            ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        $hallazgos30Dias = Report::where('aprobado', true)
            ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        $eficaciaMensual = $hallazgos30Dias > 0 ? round(($traslados30Dias / $hallazgos30Dias) * 100, 2) : 0;
        
        // Obtener parámetros del filtro
        $filtro = $request->get('filtro', 'mes'); // semana, mes, rango
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        // Determinar rango de fechas según el filtro
        $fechaInicio = null;
        $fechaFin = Carbon::now()->endOfDay();
        
        if ($filtro === 'semana') {
            $fechaInicio = Carbon::now()->subDays(7)->startOfDay();
        } elseif ($filtro === 'mes') {
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        } elseif ($filtro === 'rango' && $fechaDesde && $fechaHasta) {
            $fechaInicio = Carbon::parse($fechaDesde)->startOfDay();
            $fechaFin = Carbon::parse($fechaHasta)->endOfDay();
        } else {
            // Por defecto último mes
            $filtro = 'mes';
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        }
        
        // Generar datos diarios
        $datosDiarios = [];
        $fechaActual = Carbon::parse($fechaInicio);
        
        while ($fechaActual->lte($fechaFin)) {
            $fechaInicioDia = $fechaActual->copy()->startOfDay();
            $fechaFinDia = $fechaActual->copy()->endOfDay();
            
            // Contar hallazgos aprobados del día
            $hallazgosDia = Report::where('aprobado', true)
                ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            // Contar traslados del día
            $trasladosDia = Transfer::where('primer_traslado', true)
                ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            // Solo agregar días que tengan al menos un hallazgo o un traslado
            if ($hallazgosDia > 0 || $trasladosDia > 0) {
                // Calcular eficacia diaria
                $eficaciaDia = $hallazgosDia > 0 ? round(($trasladosDia / $hallazgosDia) * 100, 2) : 0;
                
                // Determinar color según eficacia
                $color = 'rojo'; // <= 50
                if ($eficaciaDia > 100) {
                    $color = 'azul';
                } elseif ($eficaciaDia == 100) {
                    $color = 'verde';
                } elseif ($eficaciaDia > 50) {
                    $color = 'amarillo';
                }
                
                $datosDiarios[] = [
                    'fecha' => $fechaActual->copy(),
                    'hallazgos' => $hallazgosDia,
                    'traslados' => $trasladosDia,
                    'eficacia' => $eficaciaDia,
                    'color' => $color,
                ];
            }
            
            $fechaActual->addDay();
        }
        
        return view('reports.index', [
            'tab' => 'management',
            'management_subtab' => 'rescue',
            'eficaciaMensual' => $eficaciaMensual,
            'traslados30Dias' => $traslados30Dias,
            'hallazgos30Dias' => $hallazgos30Dias,
            'datosDiarios' => $datosDiarios,
            'filtro' => $filtro,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'enPeligro' => [],
            'rescatados' => [],
            'tratados' => [],
            'liberados' => [],
            'totals' => ['en_peligro' => 0, 'rescatados' => 0, 'tratados' => 0, 'liberados' => 0],
        ]);
    }

    /**
     * Eficacia de Revisión Inicial
     */
    private function initialReviewEfficiencyReport(Request $request): View
    {
        // Calcular eficacia mensual (últimos 30 días)
        $fechaInicio30Dias = Carbon::now()->subDays(30)->startOfDay();
        $fechaFin30Dias = Carbon::now()->endOfDay();
        
        $traslados30Dias = Transfer::where('primer_traslado', true)
            ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        // Contar evaluaciones médicas iniciales (primera evaluación de cada animal_file)
        // Obtener todos los animal_files creados en el período y contar sus primeras evaluaciones
        $animalFiles30Dias = AnimalFile::whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])->pluck('id');
        $evaluacionesIniciales30Dias = MedicalEvaluation::whereIn('animal_file_id', $animalFiles30Dias)
            ->get()
            ->groupBy('animal_file_id')
            ->map(function($evaluations) {
                return $evaluations->sortBy('created_at')->first();
            })
            ->count();
        
        $eficaciaMensual = $traslados30Dias > 0 ? round(($evaluacionesIniciales30Dias / $traslados30Dias) * 100, 2) : 0;
        
        // Obtener parámetros del filtro
        $filtro = $request->get('filtro', 'mes');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        // Determinar rango de fechas según el filtro
        $fechaInicio = null;
        $fechaFin = Carbon::now()->endOfDay();
        
        if ($filtro === 'semana') {
            $fechaInicio = Carbon::now()->subDays(7)->startOfDay();
        } elseif ($filtro === 'mes') {
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        } elseif ($filtro === 'rango' && $fechaDesde && $fechaHasta) {
            $fechaInicio = Carbon::parse($fechaDesde)->startOfDay();
            $fechaFin = Carbon::parse($fechaHasta)->endOfDay();
        } else {
            $filtro = 'mes';
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        }
        
        // Generar datos diarios
        $datosDiarios = [];
        $fechaActual = Carbon::parse($fechaInicio);
        
        while ($fechaActual->lte($fechaFin)) {
            $fechaInicioDia = $fechaActual->copy()->startOfDay();
            $fechaFinDia = $fechaActual->copy()->endOfDay();
            
            // Contar traslados del día
            $trasladosDia = Transfer::where('primer_traslado', true)
                ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            // Contar evaluaciones médicas iniciales del día (primera evaluación de cada animal_file creado ese día)
            $animalFilesDia = AnimalFile::whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])->pluck('id');
            $evaluacionesInicialesDia = 0;
            if ($animalFilesDia->isNotEmpty()) {
                $evaluacionesInicialesDia = MedicalEvaluation::whereIn('animal_file_id', $animalFilesDia)
                    ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                    ->get()
                    ->groupBy('animal_file_id')
                    ->map(function($evaluations) {
                        return $evaluations->sortBy('created_at')->first();
                    })
                    ->filter(function($eval) use ($fechaInicioDia, $fechaFinDia) {
                        return $eval->created_at->between($fechaInicioDia, $fechaFinDia);
                    })
                    ->count();
            }
            
            // Solo agregar días que tengan al menos un traslado o una evaluación
            if ($trasladosDia > 0 || $evaluacionesInicialesDia > 0) {
                // Calcular eficacia diaria
                $eficaciaDia = $trasladosDia > 0 ? round(($evaluacionesInicialesDia / $trasladosDia) * 100, 2) : 0;
                
                // Determinar color según eficacia
                $color = 'rojo';
                if ($eficaciaDia > 100) {
                    $color = 'azul';
                } elseif ($eficaciaDia == 100) {
                    $color = 'verde';
                } elseif ($eficaciaDia > 50) {
                    $color = 'amarillo';
                }
                
                $datosDiarios[] = [
                    'fecha' => $fechaActual->copy(),
                    'traslados' => $trasladosDia,
                    'evaluaciones' => $evaluacionesInicialesDia,
                    'eficacia' => $eficaciaDia,
                    'color' => $color,
                ];
            }
            
            $fechaActual->addDay();
        }
        
        return view('reports.index', [
            'tab' => 'management',
            'management_subtab' => 'initial_review',
            'eficaciaMensual' => $eficaciaMensual,
            'traslados30Dias' => $traslados30Dias,
            'evaluaciones30Dias' => $evaluacionesIniciales30Dias,
            'datosDiarios' => $datosDiarios,
            'filtro' => $filtro,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'enPeligro' => [],
            'rescatados' => [],
            'tratados' => [],
            'liberados' => [],
            'totals' => ['en_peligro' => 0, 'rescatados' => 0, 'tratados' => 0, 'liberados' => 0],
        ]);
    }

    /**
     * Eficacia de los Tratamientos
     */
    private function treatmentEfficiencyReport(Request $request): View
    {
        // Calcular eficacia mensual (últimos 30 días)
        $fechaInicio30Dias = Carbon::now()->subDays(30)->startOfDay();
        $fechaFin30Dias = Carbon::now()->endOfDay();
        
        // Animales en tratamiento (sin release)
        $animalesEnTratamiento30Dias = AnimalFile::whereDoesntHave('release')
            ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        // Animales estables (estado "Estable" - deben ser liberados)
        $estableStatusId = AnimalStatus::whereRaw('LOWER(nombre) = ?', ['estable'])->value('id');
        $animalesEstables30Dias = 0;
        if ($estableStatusId) {
            $animalesEstables30Dias = AnimalFile::where('estado_id', $estableStatusId)
                ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
                ->count();
        }
        
        $eficaciaMensual = $animalesEnTratamiento30Dias > 0 ? round(($animalesEstables30Dias / $animalesEnTratamiento30Dias) * 100, 2) : 0;
        
        // Obtener parámetros del filtro
        $filtro = $request->get('filtro', 'mes');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        // Determinar rango de fechas según el filtro
        $fechaInicio = null;
        $fechaFin = Carbon::now()->endOfDay();
        
        if ($filtro === 'semana') {
            $fechaInicio = Carbon::now()->subDays(7)->startOfDay();
        } elseif ($filtro === 'mes') {
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        } elseif ($filtro === 'rango' && $fechaDesde && $fechaHasta) {
            $fechaInicio = Carbon::parse($fechaDesde)->startOfDay();
            $fechaFin = Carbon::parse($fechaHasta)->endOfDay();
        } else {
            $filtro = 'mes';
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        }
        
        // Generar datos diarios
        $datosDiarios = [];
        $fechaActual = Carbon::parse($fechaInicio);
        
        while ($fechaActual->lte($fechaFin)) {
            $fechaInicioDia = $fechaActual->copy()->startOfDay();
            $fechaFinDia = $fechaActual->copy()->endOfDay();
            
            // Animales en tratamiento del día (creados ese día, sin release)
            $animalesEnTratamientoDia = AnimalFile::whereDoesntHave('release')
                ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            // Animales estables del día (deben ser liberados)
            $animalesEstablesDia = 0;
            if ($estableStatusId) {
                $animalesEstablesDia = AnimalFile::where('estado_id', $estableStatusId)
                    ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                    ->count();
            }
            
            // Solo agregar días que tengan al menos un animal en tratamiento
            if ($animalesEnTratamientoDia > 0 || $animalesEstablesDia > 0) {
                // Calcular eficacia diaria
                $eficaciaDia = $animalesEnTratamientoDia > 0 ? round(($animalesEstablesDia / $animalesEnTratamientoDia) * 100, 2) : 0;
                
                // Determinar color según eficacia
                $color = 'rojo';
                if ($eficaciaDia > 100) {
                    $color = 'azul';
                } elseif ($eficaciaDia == 100) {
                    $color = 'verde';
                } elseif ($eficaciaDia > 50) {
                    $color = 'amarillo';
                }
                
                $datosDiarios[] = [
                    'fecha' => $fechaActual->copy(),
                    'en_tratamiento' => $animalesEnTratamientoDia,
                    'estables' => $animalesEstablesDia,
                    'eficacia' => $eficaciaDia,
                    'color' => $color,
                ];
            }
            
            $fechaActual->addDay();
        }
        
        return view('reports.index', [
            'tab' => 'management',
            'management_subtab' => 'treatment',
            'eficaciaMensual' => $eficaciaMensual,
            'animalesEnTratamiento30Dias' => $animalesEnTratamiento30Dias,
            'animalesEstables30Dias' => $animalesEstables30Dias,
            'datosDiarios' => $datosDiarios,
            'filtro' => $filtro,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'enPeligro' => [],
            'rescatados' => [],
            'tratados' => [],
            'liberados' => [],
            'totals' => ['en_peligro' => 0, 'rescatados' => 0, 'tratados' => 0, 'liberados' => 0],
        ]);
    }

    /**
     * Eficacia de la Liberación
     */
    private function releaseEfficiencyReport(Request $request): View
    {
        // Calcular eficacia mensual (últimos 30 días)
        $fechaInicio30Dias = Carbon::now()->subDays(30)->startOfDay();
        $fechaFin30Dias = Carbon::now()->endOfDay();
        
        // Animales estables (estado "Estable")
        $estableStatusId = AnimalStatus::whereRaw('LOWER(nombre) = ?', ['estable'])->value('id');
        $animalesEstables30Dias = 0;
        if ($estableStatusId) {
            $animalesEstables30Dias = AnimalFile::where('estado_id', $estableStatusId)
                ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
                ->count();
        }
        
        // Animales liberados en los últimos 30 días
        $animalesLiberados30Dias = Release::whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        $eficaciaMensual = $animalesEstables30Dias > 0 ? round(($animalesLiberados30Dias / $animalesEstables30Dias) * 100, 2) : 0;
        
        // Obtener parámetros del filtro
        $filtro = $request->get('filtro', 'mes');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        // Determinar rango de fechas según el filtro
        $fechaInicio = null;
        $fechaFin = Carbon::now()->endOfDay();
        
        if ($filtro === 'semana') {
            $fechaInicio = Carbon::now()->subDays(7)->startOfDay();
        } elseif ($filtro === 'mes') {
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        } elseif ($filtro === 'rango' && $fechaDesde && $fechaHasta) {
            $fechaInicio = Carbon::parse($fechaDesde)->startOfDay();
            $fechaFin = Carbon::parse($fechaHasta)->endOfDay();
        } else {
            $filtro = 'mes';
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        }
        
        // Generar datos diarios
        $datosDiarios = [];
        $fechaActual = Carbon::parse($fechaInicio);
        
        while ($fechaActual->lte($fechaFin)) {
            $fechaInicioDia = $fechaActual->copy()->startOfDay();
            $fechaFinDia = $fechaActual->copy()->endOfDay();
            
            // Animales estables del día
            $animalesEstablesDia = 0;
            if ($estableStatusId) {
                $animalesEstablesDia = AnimalFile::where('estado_id', $estableStatusId)
                    ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                    ->count();
            }
            
            // Animales liberados del día
            $animalesLiberadosDia = Release::whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            // Solo agregar días que tengan al menos un animal estable o liberado
            if ($animalesEstablesDia > 0 || $animalesLiberadosDia > 0) {
                // Calcular eficacia diaria
                $eficaciaDia = $animalesEstablesDia > 0 ? round(($animalesLiberadosDia / $animalesEstablesDia) * 100, 2) : 0;
                
                // Determinar color según eficacia
                $color = 'rojo';
                if ($eficaciaDia > 100) {
                    $color = 'azul';
                } elseif ($eficaciaDia == 100) {
                    $color = 'verde';
                } elseif ($eficaciaDia > 50) {
                    $color = 'amarillo';
                }
                
                $datosDiarios[] = [
                    'fecha' => $fechaActual->copy(),
                    'estables' => $animalesEstablesDia,
                    'liberados' => $animalesLiberadosDia,
                    'eficacia' => $eficaciaDia,
                    'color' => $color,
                ];
            }
            
            $fechaActual->addDay();
        }
        
        return view('reports.index', [
            'tab' => 'management',
            'management_subtab' => 'release',
            'eficaciaMensual' => $eficaciaMensual,
            'animalesEstables30Dias' => $animalesEstables30Dias,
            'animalesLiberados30Dias' => $animalesLiberados30Dias,
            'datosDiarios' => $datosDiarios,
            'filtro' => $filtro,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
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

    /**
     * Exporta el reporte actual a PDF
     */
    public function exportPdf(Request $request): Response
    {
        $tab = $request->get('tab', 'activity');
        $subtab = $request->get('subtab', 'states');
        $managementSubtab = $request->get('management_subtab', 'rescue');
        
        // Determinar qué reporte exportar según el tab activo
        if ($tab === 'activity') {
            if ($subtab === 'health') {
                return $this->exportHealthAnimalPdf($request);
            } else {
                return $this->exportActivityStatesPdf($request);
            }
        } elseif ($tab === 'management') {
            if ($managementSubtab === 'treatment') {
                return $this->exportTreatmentEfficiencyPdf($request);
            } elseif ($managementSubtab === 'release') {
                return $this->exportReleaseEfficiencyPdf($request);
            } else {
                return $this->exportRescueEfficiencyPdf($request);
            }
        }
        
        // Por defecto, exportar reporte de actividad por estados
        return $this->exportActivityStatesPdf($request);
    }

    /**
     * Exporta el reporte de Actividad por Estados a PDF
     */
    private function exportActivityStatesPdf(Request $request): Response
    {
        // Obtener los mismos datos que en activityReports()
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

        $enPeligro = [];
        $rescatados = [];
        $tratados = [];
        $liberados = [];
        
        foreach ($reports as $report) {
            $province = $this->extractProvince($report->direccion);
            if (!$province) {
                $province = 'Sin Provincia';
            }
            
            $hasFirstTransfer = $report->transfers->isNotEmpty();
            $animals = $report->animals;
            $animalFiles = $animals->flatMap->animalFiles;
            $hasAnimalFile = $animalFiles->isNotEmpty();
            
            // Obtener nombre del animal (tomar el primero si hay varios)
            $animalNombre = 'Sin nombre';
            if ($animals->isNotEmpty()) {
                $firstAnimal = $animals->first();
                $animalNombre = $firstAnimal->nombre ?? 'Sin nombre';
            }
            
            $release = null;
            $animalFileWithRelease = $animalFiles->first(function($animalFile) {
                return $animalFile->release !== null;
            });
            if ($animalFileWithRelease) {
                $release = $animalFileWithRelease->release;
            }
            
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
            
            $treatmentCenter = null;
            $animalFileCreatedAt = null;
            if ($hasAnimalFile) {
                $firstAnimalFile = $animalFiles->first();
                if ($firstAnimalFile && $firstAnimalFile->centro_id) {
                    $treatmentCenter = $firstAnimalFile->center;
                }
                if (!$treatmentCenter && $center) {
                    $treatmentCenter = $center;
                }
                $animalFileCreatedAt = $firstAnimalFile->created_at;
            }
            
            $tiempoTranscurrido = $this->calculateTimeElapsed($report->created_at);
            $tiempoDesdeTratamiento = null;
            if ($hasAnimalFile && $animalFileCreatedAt) {
                $tiempoDesdeTratamiento = $this->calculateTimeElapsed($animalFileCreatedAt);
            }
            $tiempoHallazgoTraslado = null;
            if ($fechaTraslado) {
                $tiempoHallazgoTraslado = $this->calculateTimeElapsed($report->created_at, $fechaTraslado);
            }
            
            $reportData = [
                'id' => $report->id,
                'province' => $province,
                'nombre' => $animalNombre,
                'fecha_hallazgo' => $report->created_at,
                'tiempo_transcurrido' => $tiempoTranscurrido,
            ];
            
            if ($release) {
                $reportData['estado'] = 'Liberado';
                $reportData['fecha_liberacion'] = $release->created_at;
                $liberados[] = $reportData;
            } elseif ($hasAnimalFile) {
                $reportData['estado'] = 'Tratado';
                $reportData['centro'] = $treatmentCenter;
                $reportData['fecha_tratamiento'] = $animalFileCreatedAt;
                $reportData['tiempo_desde_tratamiento'] = $tiempoDesdeTratamiento;
                $tratados[] = $reportData;
            } elseif ($hasFirstTransfer) {
                $reportData['estado'] = 'En Traslado';
                $reportData['centro'] = $center;
                $reportData['fecha_traslado'] = $fechaTraslado;
                $reportData['tiempo_hallazgo_traslado'] = $tiempoHallazgoTraslado;
                $rescatados[] = $reportData;
            } else {
                $reportData['estado'] = 'En Peligro';
                $enPeligro[] = $reportData;
            }
        }
        
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
        
        $totals = [
            'en_peligro' => count($enPeligro),
            'rescatados' => count($rescatados),
            'tratados' => count($tratados),
            'liberados' => count($liberados),
        ];
        
        $pdf = Pdf::loadView('reports.pdf.activity-states', [
            'enPeligro' => $enPeligro,
            'rescatados' => $rescatados,
            'tratados' => $tratados,
            'liberados' => $liberados,
            'totals' => $totals,
            'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i:s'),
        ]);
        
        $fileName = 'reporte_actividad_estados_' . date('d_m_Y') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Exporta el reporte de Salud Animal Actual a PDF
     */
    private function exportHealthAnimalPdf(Request $request): Response
    {
        // Obtener los mismos datos que en healthAnimalReport()
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        $primeraFecha = AnimalFile::whereDoesntHave('release')
            ->min('created_at');
        
        if (!$fechaDesde && $primeraFecha) {
            $fechaDesde = Carbon::parse($primeraFecha)->format('Y-m-d');
        }
        if (!$fechaHasta) {
            $fechaHasta = Carbon::now()->format('Y-m-d');
        }
        
        $query = AnimalFile::whereDoesntHave('release')
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
            ]);
        
        if ($fechaDesde) {
            $query->whereDate('created_at', '>=', Carbon::parse($fechaDesde)->startOfDay());
        }
        if ($fechaHasta) {
            $query->whereDate('created_at', '<=', Carbon::parse($fechaHasta)->endOfDay());
        }
        
        $animalFiles = $query->get();

        $healthData = [];
        
        foreach ($animalFiles as $animalFile) {
            $animal = $animalFile->animal;
            
            $nombreAnimal = $animal?->nombre ?? 'Sin nombre';
            $nombreCentro = $animalFile->center?->nombre ?? 'Sin centro asignado';
            $diagnosticoInicial = $animal?->descripcion ?? 'Sin diagnóstico inicial';
            $fechaCreacionHoja = $animalFile->created_at;
            
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
        
        usort($healthData, function($a, $b) {
            if ($a['centro'] !== $b['centro']) {
                return strcmp($a['centro'], $b['centro']);
            }
            return strcmp($a['nombre_animal'], $b['nombre_animal']);
        });
        
        $pdf = Pdf::loadView('reports.pdf.health-animal', [
            'healthData' => $healthData,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i:s'),
        ]);
        
        $fileName = 'reporte_salud_animal_' . date('d_m_Y') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Exporta el reporte de Eficacia de Rescate a PDF
     */
    private function exportRescueEfficiencyPdf(Request $request): Response
    {
        // Obtener los mismos datos que en managementReports()
        $fechaInicio30Dias = Carbon::now()->subDays(30)->startOfDay();
        $fechaFin30Dias = Carbon::now()->endOfDay();
        
        $traslados30Dias = Transfer::where('primer_traslado', true)
            ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        $hallazgos30Dias = Report::where('aprobado', true)
            ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        $eficaciaMensual = $hallazgos30Dias > 0 ? round(($traslados30Dias / $hallazgos30Dias) * 100, 2) : 0;
        
        $filtro = $request->get('filtro', 'mes');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        $fechaInicio = null;
        $fechaFin = Carbon::now()->endOfDay();
        
        if ($filtro === 'semana') {
            $fechaInicio = Carbon::now()->subDays(7)->startOfDay();
        } elseif ($filtro === 'mes') {
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        } elseif ($filtro === 'rango' && $fechaDesde && $fechaHasta) {
            $fechaInicio = Carbon::parse($fechaDesde)->startOfDay();
            $fechaFin = Carbon::parse($fechaHasta)->endOfDay();
        } else {
            $filtro = 'mes';
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        }
        
        $datosDiarios = [];
        $fechaActual = Carbon::parse($fechaInicio);
        
        while ($fechaActual->lte($fechaFin)) {
            $fechaInicioDia = $fechaActual->copy()->startOfDay();
            $fechaFinDia = $fechaActual->copy()->endOfDay();
            
            $hallazgosDia = Report::where('aprobado', true)
                ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            $trasladosDia = Transfer::where('primer_traslado', true)
                ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            if ($hallazgosDia > 0 || $trasladosDia > 0) {
                $eficaciaDia = $hallazgosDia > 0 ? round(($trasladosDia / $hallazgosDia) * 100, 2) : 0;
                
                $color = 'rojo';
                if ($eficaciaDia > 100) {
                    $color = 'azul';
                } elseif ($eficaciaDia == 100) {
                    $color = 'verde';
                } elseif ($eficaciaDia > 50) {
                    $color = 'amarillo';
                }
                
                $datosDiarios[] = [
                    'fecha' => $fechaActual->copy(),
                    'hallazgos' => $hallazgosDia,
                    'traslados' => $trasladosDia,
                    'eficacia' => $eficaciaDia,
                    'color' => $color,
                ];
            }
            
            $fechaActual->addDay();
        }
        
        $pdf = Pdf::loadView('reports.pdf.efficiency-rescue', [
            'eficaciaMensual' => $eficaciaMensual,
            'traslados30Dias' => $traslados30Dias,
            'hallazgos30Dias' => $hallazgos30Dias,
            'datosDiarios' => $datosDiarios,
            'filtro' => $filtro,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i:s'),
        ]);
        
        $fileName = 'reporte_eficacia_rescate_' . date('d_m_Y') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Exporta el reporte de Eficacia de Tratamiento a PDF
     */
    private function exportTreatmentEfficiencyPdf(Request $request): Response
    {
        $fechaInicio30Dias = Carbon::now()->subDays(30)->startOfDay();
        $fechaFin30Dias = Carbon::now()->endOfDay();
        
        $animalesEnTratamiento30Dias = AnimalFile::whereDoesntHave('release')
            ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        $estableStatusId = AnimalStatus::whereRaw('LOWER(nombre) = ?', ['estable'])->value('id');
        $animalesEstables30Dias = 0;
        if ($estableStatusId) {
            $animalesEstables30Dias = AnimalFile::where('estado_id', $estableStatusId)
                ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
                ->count();
        }
        
        $eficaciaMensual = $animalesEnTratamiento30Dias > 0 ? round(($animalesEstables30Dias / $animalesEnTratamiento30Dias) * 100, 2) : 0;
        
        $filtro = $request->get('filtro', 'mes');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        $fechaInicio = null;
        $fechaFin = Carbon::now()->endOfDay();
        
        if ($filtro === 'semana') {
            $fechaInicio = Carbon::now()->subDays(7)->startOfDay();
        } elseif ($filtro === 'mes') {
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        } elseif ($filtro === 'rango' && $fechaDesde && $fechaHasta) {
            $fechaInicio = Carbon::parse($fechaDesde)->startOfDay();
            $fechaFin = Carbon::parse($fechaHasta)->endOfDay();
        } else {
            $filtro = 'mes';
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        }
        
        $datosDiarios = [];
        $fechaActual = Carbon::parse($fechaInicio);
        $estableStatusId = AnimalStatus::whereRaw('LOWER(nombre) = ?', ['estable'])->value('id');
        
        while ($fechaActual->lte($fechaFin)) {
            $fechaInicioDia = $fechaActual->copy()->startOfDay();
            $fechaFinDia = $fechaActual->copy()->endOfDay();
            
            $animalesEnTratamientoDia = AnimalFile::whereDoesntHave('release')
                ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            $animalesEstablesDia = 0;
            if ($estableStatusId) {
                $animalesEstablesDia = AnimalFile::where('estado_id', $estableStatusId)
                    ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                    ->count();
            }
            
            if ($animalesEnTratamientoDia > 0 || $animalesEstablesDia > 0) {
                $eficaciaDia = $animalesEnTratamientoDia > 0 ? round(($animalesEstablesDia / $animalesEnTratamientoDia) * 100, 2) : 0;
                
                $color = 'rojo';
                if ($eficaciaDia > 100) {
                    $color = 'azul';
                } elseif ($eficaciaDia == 100) {
                    $color = 'verde';
                } elseif ($eficaciaDia > 50) {
                    $color = 'amarillo';
                }
                
                $datosDiarios[] = [
                    'fecha' => $fechaActual->copy(),
                    'en_tratamiento' => $animalesEnTratamientoDia,
                    'estables' => $animalesEstablesDia,
                    'eficacia' => $eficaciaDia,
                    'color' => $color,
                ];
            }
            
            $fechaActual->addDay();
        }
        
        $pdf = Pdf::loadView('reports.pdf.efficiency-treatment', [
            'eficaciaMensual' => $eficaciaMensual,
            'animalesEnTratamiento30Dias' => $animalesEnTratamiento30Dias,
            'animalesEstables30Dias' => $animalesEstables30Dias,
            'datosDiarios' => $datosDiarios,
            'filtro' => $filtro,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i:s'),
        ]);
        
        $fileName = 'reporte_eficacia_tratamiento_' . date('d_m_Y') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Exporta el reporte de Eficacia de Liberación a PDF
     */
    private function exportReleaseEfficiencyPdf(Request $request): Response
    {
        $fechaInicio30Dias = Carbon::now()->subDays(30)->startOfDay();
        $fechaFin30Dias = Carbon::now()->endOfDay();
        
        $estableStatusId = AnimalStatus::whereRaw('LOWER(nombre) = ?', ['estable'])->value('id');
        $animalesEstables30Dias = 0;
        if ($estableStatusId) {
            $animalesEstables30Dias = AnimalFile::where('estado_id', $estableStatusId)
                ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
                ->count();
        }
        
        $animalesLiberados30Dias = Release::whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        $eficaciaMensual = $animalesEstables30Dias > 0 ? round(($animalesLiberados30Dias / $animalesEstables30Dias) * 100, 2) : 0;
        
        $filtro = $request->get('filtro', 'mes');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        $fechaInicio = null;
        $fechaFin = Carbon::now()->endOfDay();
        
        if ($filtro === 'semana') {
            $fechaInicio = Carbon::now()->subDays(7)->startOfDay();
        } elseif ($filtro === 'mes') {
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        } elseif ($filtro === 'rango' && $fechaDesde && $fechaHasta) {
            $fechaInicio = Carbon::parse($fechaDesde)->startOfDay();
            $fechaFin = Carbon::parse($fechaHasta)->endOfDay();
        } else {
            $filtro = 'mes';
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        }
        
        $datosDiarios = [];
        $fechaActual = Carbon::parse($fechaInicio);
        
        while ($fechaActual->lte($fechaFin)) {
            $fechaInicioDia = $fechaActual->copy()->startOfDay();
            $fechaFinDia = $fechaActual->copy()->endOfDay();
            
            $animalesEstablesDia = 0;
            if ($estableStatusId) {
                $animalesEstablesDia = AnimalFile::where('estado_id', $estableStatusId)
                    ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                    ->count();
            }
            
            $animalesLiberadosDia = Release::whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            if ($animalesEstablesDia > 0 || $animalesLiberadosDia > 0) {
                $eficaciaDia = $animalesEstablesDia > 0 ? round(($animalesLiberadosDia / $animalesEstablesDia) * 100, 2) : 0;
                
                $color = 'rojo';
                if ($eficaciaDia > 100) {
                    $color = 'azul';
                } elseif ($eficaciaDia == 100) {
                    $color = 'verde';
                } elseif ($eficaciaDia > 50) {
                    $color = 'amarillo';
                }
                
                $datosDiarios[] = [
                    'fecha' => $fechaActual->copy(),
                    'estables' => $animalesEstablesDia,
                    'liberados' => $animalesLiberadosDia,
                    'eficacia' => $eficaciaDia,
                    'color' => $color,
                ];
            }
            
            $fechaActual->addDay();
        }
        
        $pdf = Pdf::loadView('reports.pdf.efficiency-release', [
            'eficaciaMensual' => $eficaciaMensual,
            'animalesEstables30Dias' => $animalesEstables30Dias,
            'animalesLiberados30Dias' => $animalesLiberados30Dias,
            'datosDiarios' => $datosDiarios,
            'filtro' => $filtro,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i:s'),
        ]);
        
        $fileName = 'reporte_eficacia_liberacion_' . date('d_m_Y') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Exporta el reporte actual a CSV
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $tab = $request->get('tab', 'activity');
        $subtab = $request->get('subtab', 'states');
        $managementSubtab = $request->get('management_subtab', 'rescue');
        
        // Determinar qué reporte exportar según el tab activo
        if ($tab === 'activity') {
            if ($subtab === 'health') {
                return $this->exportHealthAnimalCsv($request);
            } else {
                return $this->exportActivityStatesCsv($request);
            }
        } elseif ($tab === 'management') {
            if ($managementSubtab === 'treatment') {
                return $this->exportTreatmentEfficiencyCsv($request);
            } elseif ($managementSubtab === 'release') {
                return $this->exportReleaseEfficiencyCsv($request);
            } else {
                return $this->exportRescueEfficiencyCsv($request);
            }
        }
        
        // Por defecto, exportar reporte de actividad por estados
        return $this->exportActivityStatesCsv($request);
    }

    /**
     * Exporta el reporte de Actividad por Estados a CSV
     */
    private function exportActivityStatesCsv(Request $request): StreamedResponse
    {
        // Obtener los mismos datos que en activityReports()
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

        $enPeligro = [];
        $rescatados = [];
        $tratados = [];
        $liberados = [];
        
        foreach ($reports as $report) {
            $province = $this->extractProvince($report->direccion);
            if (!$province) {
                $province = 'Sin Provincia';
            }
            
            $hasFirstTransfer = $report->transfers->isNotEmpty();
            $animals = $report->animals;
            $animalFiles = $animals->flatMap->animalFiles;
            $hasAnimalFile = $animalFiles->isNotEmpty();
            
            // Obtener nombre del animal (tomar el primero si hay varios)
            $animalNombre = 'Sin nombre';
            if ($animals->isNotEmpty()) {
                $firstAnimal = $animals->first();
                $animalNombre = $firstAnimal->nombre ?? 'Sin nombre';
            }
            
            $release = null;
            $animalFileWithRelease = $animalFiles->first(function($animalFile) {
                return $animalFile->release !== null;
            });
            if ($animalFileWithRelease) {
                $release = $animalFileWithRelease->release;
            }
            
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
            
            $treatmentCenter = null;
            $animalFileCreatedAt = null;
            if ($hasAnimalFile) {
                $firstAnimalFile = $animalFiles->first();
                if ($firstAnimalFile && $firstAnimalFile->centro_id) {
                    $treatmentCenter = $firstAnimalFile->center;
                }
                if (!$treatmentCenter && $center) {
                    $treatmentCenter = $center;
                }
                $animalFileCreatedAt = $firstAnimalFile->created_at;
            }
            
            $tiempoTranscurrido = $this->calculateTimeElapsed($report->created_at);
            $tiempoDesdeTratamiento = null;
            if ($hasAnimalFile && $animalFileCreatedAt) {
                $tiempoDesdeTratamiento = $this->calculateTimeElapsed($animalFileCreatedAt);
            }
            $tiempoHallazgoTraslado = null;
            if ($fechaTraslado) {
                $tiempoHallazgoTraslado = $this->calculateTimeElapsed($report->created_at, $fechaTraslado);
            }
            
            $reportData = [
                'id' => $report->id,
                'province' => $province,
                'nombre' => $animalNombre,
                'fecha_hallazgo' => $report->created_at,
                'tiempo_transcurrido' => $tiempoTranscurrido,
            ];
            
            if ($release) {
                $reportData['estado'] = 'Liberado';
                $reportData['fecha_liberacion'] = $release->created_at;
                $liberados[] = $reportData;
            } elseif ($hasAnimalFile) {
                $reportData['estado'] = 'Tratado';
                $reportData['centro'] = $treatmentCenter;
                $reportData['fecha_tratamiento'] = $animalFileCreatedAt;
                $reportData['tiempo_desde_tratamiento'] = $tiempoDesdeTratamiento;
                $tratados[] = $reportData;
            } elseif ($hasFirstTransfer) {
                $reportData['estado'] = 'En Traslado';
                $reportData['centro'] = $center;
                $reportData['fecha_traslado'] = $fechaTraslado;
                $reportData['tiempo_hallazgo_traslado'] = $tiempoHallazgoTraslado;
                $rescatados[] = $reportData;
            } else {
                $reportData['estado'] = 'En Peligro';
                $enPeligro[] = $reportData;
            }
        }
        
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
        
        $totals = [
            'en_peligro' => count($enPeligro),
            'rescatados' => count($rescatados),
            'tratados' => count($tratados),
            'liberados' => count($liberados),
        ];
        
        $fileName = 'reporte_actividad_estados_' . date('d_m_Y') . '.csv';
        
        return new StreamedResponse(function() use ($totals, $enPeligro, $rescatados, $tratados, $liberados) {
            $handle = fopen('php://output', 'w');
            
            // BOM para UTF-8 (para que Excel abra correctamente caracteres especiales)
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Título
            fputcsv($handle, ['Reporte de Actividad por Estados']);
            fputcsv($handle, ['Generado el: ' . Carbon::now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, []);
            
            // Totales
            fputcsv($handle, ['Totales']);
            fputcsv($handle, ['En Peligro', $totals['en_peligro']]);
            fputcsv($handle, ['En Traslado', $totals['rescatados']]);
            fputcsv($handle, ['Tratados', $totals['tratados']]);
            fputcsv($handle, ['Liberados', $totals['liberados']]);
            fputcsv($handle, ['Total General', $totals['en_peligro'] + $totals['rescatados'] + $totals['tratados'] + $totals['liberados']]);
            fputcsv($handle, []);
            
            // Tabla En Peligro
            if (!empty($enPeligro)) {
                fputcsv($handle, ['Reporte de Animales en Peligro']);
                fputcsv($handle, ['Provincia', 'Estado', 'Fecha Hallazgo', 'Tiempo Transcurrido']);
                foreach ($enPeligro as $report) {
                    fputcsv($handle, [
                        $report['province'],
                        'En Peligro',
                        $report['fecha_hallazgo']->format('d/m/Y H:i'),
                        $report['tiempo_transcurrido']
                    ]);
                }
                fputcsv($handle, []);
            }
            
            // Tabla En Traslado
            if (!empty($rescatados)) {
                fputcsv($handle, ['Reporte de Animales en Traslado']);
                fputcsv($handle, ['Nombre', 'Estado', 'Centro de Destino', 'Fecha Traslado', 'Tiempo H-T']);
                foreach ($rescatados as $report) {
                    fputcsv($handle, [
                        $report['nombre'] ?? 'Sin nombre',
                        'En Traslado',
                        $report['centro'] ? $report['centro']->nombre : '-',
                        $report['fecha_traslado'] ? $report['fecha_traslado']->format('d/m/Y H:i') : '-',
                        $report['tiempo_hallazgo_traslado'] ?? '-'
                    ]);
                }
                fputcsv($handle, []);
            }
            
            // Tabla Tratados
            if (!empty($tratados)) {
                fputcsv($handle, ['Reporte de Animales en Tratamiento']);
                fputcsv($handle, ['Nombre', 'Estado', 'Fecha Hallazgo', 'Fecha Inicio Tratamiento', 'Tiempo desde Tratamiento']);
                foreach ($tratados as $report) {
                    fputcsv($handle, [
                        $report['nombre'] ?? 'Sin nombre',
                        'Tratado',
                        $report['fecha_hallazgo']->format('d/m/Y H:i'),
                        $report['fecha_tratamiento'] ? $report['fecha_tratamiento']->format('d/m/Y H:i') : '-',
                        $report['tiempo_desde_tratamiento'] ?? '-'
                    ]);
                }
                fputcsv($handle, []);
            }
            
            // Tabla Liberados
            if (!empty($liberados)) {
                fputcsv($handle, ['Reporte de Animales Liberados']);
                fputcsv($handle, ['Nombre', 'Estado', 'Fecha Hallazgo', 'Fecha Liberación']);
                foreach ($liberados as $report) {
                    fputcsv($handle, [
                        $report['nombre'] ?? 'Sin nombre',
                        'Liberado',
                        $report['fecha_hallazgo']->format('d/m/Y H:i'),
                        $report['fecha_liberacion']->format('d/m/Y H:i')
                    ]);
                }
            }
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Exporta el reporte de Salud Animal Actual a CSV
     */
    private function exportHealthAnimalCsv(Request $request): StreamedResponse
    {
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        $primeraFecha = AnimalFile::whereDoesntHave('release')
            ->min('created_at');
        
        if (!$fechaDesde && $primeraFecha) {
            $fechaDesde = Carbon::parse($primeraFecha)->format('Y-m-d');
        }
        if (!$fechaHasta) {
            $fechaHasta = Carbon::now()->format('Y-m-d');
        }
        
        $query = AnimalFile::whereDoesntHave('release')
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
            ]);
        
        if ($fechaDesde) {
            $query->whereDate('created_at', '>=', Carbon::parse($fechaDesde)->startOfDay());
        }
        if ($fechaHasta) {
            $query->whereDate('created_at', '<=', Carbon::parse($fechaHasta)->endOfDay());
        }
        
        $animalFiles = $query->get();

        $healthData = [];
        
        foreach ($animalFiles as $animalFile) {
            $animal = $animalFile->animal;
            
            $nombreAnimal = $animal?->nombre ?? 'Sin nombre';
            $nombreCentro = $animalFile->center?->nombre ?? 'Sin centro asignado';
            $diagnosticoInicial = $animal?->descripcion ?? 'Sin diagnóstico inicial';
            $fechaCreacionHoja = $animalFile->created_at;
            
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
        
        usort($healthData, function($a, $b) {
            if ($a['centro'] !== $b['centro']) {
                return strcmp($a['centro'], $b['centro']);
            }
            return strcmp($a['nombre_animal'], $b['nombre_animal']);
        });
        
        $fileName = 'reporte_salud_animal_' . date('d_m_Y') . '.csv';
        
        return new StreamedResponse(function() use ($healthData, $fechaDesde, $fechaHasta) {
            $handle = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Título
            fputcsv($handle, ['Reporte de Salud Animal Actual']);
            fputcsv($handle, ['Generado el: ' . Carbon::now()->format('d/m/Y H:i:s')]);
            
            if ($fechaDesde || $fechaHasta) {
                $filtroTexto = 'Filtro aplicado: ';
                if ($fechaDesde && $fechaHasta) {
                    $filtroTexto .= 'Desde: ' . Carbon::parse($fechaDesde)->format('d/m/Y') . ' - Hasta: ' . Carbon::parse($fechaHasta)->format('d/m/Y');
                } elseif ($fechaDesde) {
                    $filtroTexto .= 'Desde: ' . Carbon::parse($fechaDesde)->format('d/m/Y');
                } elseif ($fechaHasta) {
                    $filtroTexto .= 'Hasta: ' . Carbon::parse($fechaHasta)->format('d/m/Y');
                }
                fputcsv($handle, [$filtroTexto]);
            }
            fputcsv($handle, []);
            
            // Encabezados
            fputcsv($handle, ['Centro', 'Nombre del Animal', 'Diagnóstico Inicial', 'Fecha Inicial', 'Fecha Última Evaluación', 'Última Intervención Médica', 'Estado Actual']);
            
            // Datos
            foreach ($healthData as $data) {
                $intervencionTexto = '';
                if ($data['ultima_intervencion']) {
                    $intervencionTexto = 'Tipo: ' . $data['ultima_intervencion']['tipo'];
                    if ($data['ultima_intervencion']['diagnostico']) {
                        $intervencionTexto .= ' | Diagnóstico: ' . $data['ultima_intervencion']['diagnostico'];
                    }
                    if ($data['ultima_intervencion']['descripcion']) {
                        $intervencionTexto .= ' | ' . mb_substr($data['ultima_intervencion']['descripcion'], 0, 100);
                    }
                } else {
                    $intervencionTexto = 'Sin intervenciones registradas';
                }
                
                fputcsv($handle, [
                    $data['centro'],
                    $data['nombre_animal'],
                    $data['diagnostico_inicial'],
                    $data['fecha_creacion_hoja'] ? $data['fecha_creacion_hoja']->format('d/m/Y') : '-',
                    $data['fecha_ultima_evaluacion'] ? $data['fecha_ultima_evaluacion']->format('d/m/Y') : '-',
                    $intervencionTexto,
                    $data['estado_actual']
                ]);
            }
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Exporta el reporte de Eficacia de Rescate a CSV
     */
    private function exportRescueEfficiencyCsv(Request $request): StreamedResponse
    {
        $fechaInicio30Dias = Carbon::now()->subDays(30)->startOfDay();
        $fechaFin30Dias = Carbon::now()->endOfDay();
        
        $traslados30Dias = Transfer::where('primer_traslado', true)
            ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        $hallazgos30Dias = Report::where('aprobado', true)
            ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        $eficaciaMensual = $hallazgos30Dias > 0 ? round(($traslados30Dias / $hallazgos30Dias) * 100, 2) : 0;
        
        $filtro = $request->get('filtro', 'mes');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        $fechaInicio = null;
        $fechaFin = Carbon::now()->endOfDay();
        
        if ($filtro === 'semana') {
            $fechaInicio = Carbon::now()->subDays(7)->startOfDay();
        } elseif ($filtro === 'mes') {
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        } elseif ($filtro === 'rango' && $fechaDesde && $fechaHasta) {
            $fechaInicio = Carbon::parse($fechaDesde)->startOfDay();
            $fechaFin = Carbon::parse($fechaHasta)->endOfDay();
        } else {
            $filtro = 'mes';
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        }
        
        $datosDiarios = [];
        $fechaActual = Carbon::parse($fechaInicio);
        
        while ($fechaActual->lte($fechaFin)) {
            $fechaInicioDia = $fechaActual->copy()->startOfDay();
            $fechaFinDia = $fechaActual->copy()->endOfDay();
            
            $hallazgosDia = Report::where('aprobado', true)
                ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            $trasladosDia = Transfer::where('primer_traslado', true)
                ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            if ($hallazgosDia > 0 || $trasladosDia > 0) {
                $eficaciaDia = $hallazgosDia > 0 ? round(($trasladosDia / $hallazgosDia) * 100, 2) : 0;
                
                $color = 'rojo';
                if ($eficaciaDia > 100) {
                    $color = 'azul';
                } elseif ($eficaciaDia == 100) {
                    $color = 'verde';
                } elseif ($eficaciaDia > 50) {
                    $color = 'amarillo';
                }
                
                $datosDiarios[] = [
                    'fecha' => $fechaActual->copy(),
                    'hallazgos' => $hallazgosDia,
                    'traslados' => $trasladosDia,
                    'eficacia' => $eficaciaDia,
                    'color' => $color,
                ];
            }
            
            $fechaActual->addDay();
        }
        
        return $this->exportEfficiencyCsv(
            'Eficacia Mensual del Rescate de Animales',
            $eficaciaMensual,
            $traslados30Dias . ' traslados / ' . $hallazgos30Dias . ' hallazgos',
            $filtro,
            $fechaDesde,
            $fechaHasta,
            ['Fecha', 'Cantidad de Hallazgos', 'Cantidad de Traslados', 'Eficacia Diaria (%)', 'Estado'],
            $datosDiarios,
            'reporte_eficacia_rescate_' . date('d_m_Y') . '.csv',
            function($dato) {
                return [
                    $dato['fecha']->format('d/m/Y'),
                    $dato['hallazgos'],
                    $dato['traslados'],
                    number_format($dato['eficacia'], 2) . '%',
                    $this->getEstadoTexto($dato['color']),
                ];
            }
        );
    }

    /**
     * Exporta el reporte de Eficacia de Tratamiento a CSV
     */
    private function exportTreatmentEfficiencyCsv(Request $request): StreamedResponse
    {
        $fechaInicio30Dias = Carbon::now()->subDays(30)->startOfDay();
        $fechaFin30Dias = Carbon::now()->endOfDay();
        
        $animalesEnTratamiento30Dias = AnimalFile::whereDoesntHave('release')
            ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
            ->count();
        
        $estableStatusId = AnimalStatus::whereRaw('LOWER(nombre) = ?', ['estable'])->value('id');
        $animalesEstables30Dias = 0;
        if ($estableStatusId) {
            $animalesEstables30Dias = AnimalFile::where('estado_id', $estableStatusId)
                ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
                ->count();
        }
        
        $eficaciaMensual = $animalesEnTratamiento30Dias > 0 ? round(($animalesEstables30Dias / $animalesEnTratamiento30Dias) * 100, 2) : 0;
        
        $filtro = $request->get('filtro', 'mes');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        $fechaInicio = null;
        $fechaFin = Carbon::now()->endOfDay();
        
        if ($filtro === 'semana') {
            $fechaInicio = Carbon::now()->subDays(7)->startOfDay();
        } elseif ($filtro === 'mes') {
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        } elseif ($filtro === 'rango' && $fechaDesde && $fechaHasta) {
            $fechaInicio = Carbon::parse($fechaDesde)->startOfDay();
            $fechaFin = Carbon::parse($fechaHasta)->endOfDay();
        } else {
            $filtro = 'mes';
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        }
        
        $datosDiarios = [];
        $fechaActual = Carbon::parse($fechaInicio);
        
        while ($fechaActual->lte($fechaFin)) {
            $fechaInicioDia = $fechaActual->copy()->startOfDay();
            $fechaFinDia = $fechaActual->copy()->endOfDay();
            
            $animalesEnTratamientoDia = AnimalFile::whereDoesntHave('release')
                ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                ->count();
            
            $animalesEstablesDia = 0;
            if ($estableStatusId) {
                $animalesEstablesDia = AnimalFile::where('estado_id', $estableStatusId)
                    ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                    ->count();
            }
            
            if ($animalesEnTratamientoDia > 0 || $animalesEstablesDia > 0) {
                $eficaciaDia = $animalesEnTratamientoDia > 0 ? round(($animalesEstablesDia / $animalesEnTratamientoDia) * 100, 2) : 0;
                
                $color = 'rojo';
                if ($eficaciaDia > 100) {
                    $color = 'azul';
                } elseif ($eficaciaDia == 100) {
                    $color = 'verde';
                } elseif ($eficaciaDia > 50) {
                    $color = 'amarillo';
                }
                
                $datosDiarios[] = [
                    'fecha' => $fechaActual->copy(),
                    'en_tratamiento' => $animalesEnTratamientoDia,
                    'estables' => $animalesEstablesDia,
                    'eficacia' => $eficaciaDia,
                    'color' => $color,
                ];
            }
            
            $fechaActual->addDay();
        }
        
        return $this->exportEfficiencyCsv(
            'Eficacia de los Tratamientos',
            $eficaciaMensual,
            $animalesEstables30Dias . ' animales estables / ' . $animalesEnTratamiento30Dias . ' animales en tratamiento',
            $filtro,
            $fechaDesde,
            $fechaHasta,
            ['Fecha', 'Animales en Tratamiento', 'Animales Estables', 'Eficacia Diaria (%)', 'Estado'],
            $datosDiarios,
            'reporte_eficacia_tratamiento_' . date('d_m_Y') . '.csv',
            function($dato) {
                return [
                    $dato['fecha']->format('d/m/Y'),
                    $dato['en_tratamiento'],
                    $dato['estables'],
                    number_format($dato['eficacia'], 2) . '%',
                    $this->getEstadoTexto($dato['color']),
                ];
            }
        );
    }

    /**
     * Exporta el reporte de Eficacia de Liberación a CSV
     */
    private function exportReleaseEfficiencyCsv(Request $request): StreamedResponse
    {
        $fechaInicio30Dias = Carbon::now()->subDays(30)->startOfDay();
        $fechaFin30Dias = Carbon::now()->endOfDay();
        
        $estableStatusId = AnimalStatus::whereRaw('LOWER(nombre) = ?', ['estable'])->value('id');
        $animalesEstables30Dias = 0;
        if ($estableStatusId) {
            $animalesEstables30Dias = AnimalFile::where('estado_id', $estableStatusId)
                ->whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])
                ->count();
        }
        
        $animalesLiberados30Dias = Release::whereBetween('created_at', [$fechaInicio30Dias, $fechaFin30Dias])->count();
        
        $eficaciaMensual = $animalesEstables30Dias > 0 ? round(($animalesLiberados30Dias / $animalesEstables30Dias) * 100, 2) : 0;
        
        $filtro = $request->get('filtro', 'mes');
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        $fechaInicio = null;
        $fechaFin = Carbon::now()->endOfDay();
        
        if ($filtro === 'semana') {
            $fechaInicio = Carbon::now()->subDays(7)->startOfDay();
        } elseif ($filtro === 'mes') {
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        } elseif ($filtro === 'rango' && $fechaDesde && $fechaHasta) {
            $fechaInicio = Carbon::parse($fechaDesde)->startOfDay();
            $fechaFin = Carbon::parse($fechaHasta)->endOfDay();
        } else {
            $filtro = 'mes';
            $fechaInicio = Carbon::now()->subDays(30)->startOfDay();
        }
        
        $datosDiarios = [];
        $fechaActual = Carbon::parse($fechaInicio);
        
        while ($fechaActual->lte($fechaFin)) {
            $fechaInicioDia = $fechaActual->copy()->startOfDay();
            $fechaFinDia = $fechaActual->copy()->endOfDay();
            
            $animalesEstablesDia = 0;
            if ($estableStatusId) {
                $animalesEstablesDia = AnimalFile::where('estado_id', $estableStatusId)
                    ->whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])
                    ->count();
            }
            
            $animalesLiberadosDia = Release::whereBetween('created_at', [$fechaInicioDia, $fechaFinDia])->count();
            
            if ($animalesEstablesDia > 0 || $animalesLiberadosDia > 0) {
                $eficaciaDia = $animalesEstablesDia > 0 ? round(($animalesLiberadosDia / $animalesEstablesDia) * 100, 2) : 0;
                
                $color = 'rojo';
                if ($eficaciaDia > 100) {
                    $color = 'azul';
                } elseif ($eficaciaDia == 100) {
                    $color = 'verde';
                } elseif ($eficaciaDia > 50) {
                    $color = 'amarillo';
                }
                
                $datosDiarios[] = [
                    'fecha' => $fechaActual->copy(),
                    'estables' => $animalesEstablesDia,
                    'liberados' => $animalesLiberadosDia,
                    'eficacia' => $eficaciaDia,
                    'color' => $color,
                ];
            }
            
            $fechaActual->addDay();
        }
        
        return $this->exportEfficiencyCsv(
            'Eficacia de la Liberación',
            $eficaciaMensual,
            $animalesLiberados30Dias . ' animales liberados / ' . $animalesEstables30Dias . ' animales estables',
            $filtro,
            $fechaDesde,
            $fechaHasta,
            ['Fecha', 'Animales Estables', 'Animales Liberados', 'Eficacia Diaria (%)', 'Estado'],
            $datosDiarios,
            'reporte_eficacia_liberacion_' . date('d_m_Y') . '.csv',
            function($dato) {
                return [
                    $dato['fecha']->format('d/m/Y'),
                    $dato['estables'],
                    $dato['liberados'],
                    number_format($dato['eficacia'], 2) . '%',
                    $this->getEstadoTexto($dato['color']),
                ];
            }
        );
    }

    /**
     * Método helper para exportar reportes de eficacia a CSV
     */
    private function exportEfficiencyCsv($titulo, $eficaciaMensual, $detalles, $filtro, $fechaDesde, $fechaHasta, $headers, $datosDiarios, $fileName, $callback): StreamedResponse
    {
        return new StreamedResponse(function() use ($titulo, $eficaciaMensual, $detalles, $filtro, $fechaDesde, $fechaHasta, $headers, $datosDiarios, $callback) {
            $handle = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Título
            fputcsv($handle, [$titulo]);
            fputcsv($handle, ['Generado el: ' . Carbon::now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, []);
            
            // Resumen de eficacia
            fputcsv($handle, ['Eficacia de los últimos 30 días:', $eficaciaMensual . '%']);
            fputcsv($handle, [$detalles]);
            fputcsv($handle, []);
            
            // Filtro
            if ($filtro) {
                $filtroTexto = 'Filtro aplicado: ';
                if ($filtro === 'semana') {
                    $filtroTexto .= 'Última Semana';
                } elseif ($filtro === 'mes') {
                    $filtroTexto .= 'Último Mes';
                } elseif ($filtro === 'rango' && $fechaDesde && $fechaHasta) {
                    $filtroTexto .= 'Rango: ' . Carbon::parse($fechaDesde)->format('d/m/Y') . ' - ' . Carbon::parse($fechaHasta)->format('d/m/Y');
                }
                fputcsv($handle, [$filtroTexto]);
                fputcsv($handle, []);
            }
            
            // Encabezados
            fputcsv($handle, $headers);
            
            // Datos
            foreach ($datosDiarios as $dato) {
                $datos = $callback($dato);
                fputcsv($handle, $datos);
            }
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Obtiene el texto del estado según el color
     */
    private function getEstadoTexto($color): string
    {
        switch ($color) {
            case 'verde':
                return '100%';
            case 'amarillo':
                return '> 50%';
            case 'azul':
                return '> 100%';
            case 'rojo':
            default:
                return '≤ 50%';
        }
    }
}

