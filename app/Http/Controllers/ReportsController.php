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
}

