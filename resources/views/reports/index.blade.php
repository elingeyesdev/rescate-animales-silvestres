@extends('adminlte::page')

@section('title', 'Reportes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-dark">Reportes</h1>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Pestañas -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <ul class="nav nav-tabs" id="reportsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tab === 'activity' ? 'active' : '' }}" 
                   id="activity-tab" 
                   href="{{ route('reportes.index', ['tab' => 'activity']) }}"
                   role="tab">
                    <i class="fas fa-chart-line mr-2"></i>Reportes de Actividad
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tab === 'management' ? 'active' : '' }}" 
                   id="management-tab" 
                   href="{{ route('reportes.index', ['tab' => 'management']) }}"
                   role="tab">
                    <i class="fas fa-cog mr-2"></i>Reportes de Gestión
                </a>
            </li>
        </ul>
        <div>
            <a href="{{ route('reportes.export-pdf', request()->all()) }}" 
               class="btn btn-danger btn-sm" 
               target="_blank">
                <i class="fas fa-file-pdf mr-2"></i>Exportar a PDF
            </a>
            <a href="{{ route('reportes.export-csv', request()->all()) }}" 
               class="btn btn-success btn-sm ml-2" 
               target="_blank">
                <i class="fas fa-file-csv mr-2"></i>Exportar a CSV
            </a>
        </div>
    </div>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="reportsTabContent">
        <!-- Pestaña: Reportes de Actividad -->
        <div class="tab-pane fade {{ $tab === 'activity' ? 'show active' : '' }}" 
             id="activity" 
             role="tabpanel" 
             aria-labelledby="activity-tab">
            
            <!-- Subpestañas dentro de Reportes de Actividad -->
            <ul class="nav nav-pills mt-3" id="activitySubTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ (!isset($subtab) || $subtab === 'states') ? 'active' : '' }}" 
                       id="states-subtab" 
                       href="{{ route('reportes.index', ['tab' => 'activity', 'subtab' => 'states']) }}"
                       role="tab">
                        <i class="fas fa-map-marked-alt mr-2"></i>Actividad por Estados
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ (isset($subtab) && $subtab === 'health') ? 'active' : '' }}" 
                       id="health-subtab" 
                       href="{{ route('reportes.index', ['tab' => 'activity', 'subtab' => 'health']) }}"
                       role="tab">
                        <i class="fas fa-heartbeat mr-2"></i>Salud Animal Actual
                    </a>
                </li>
            </ul>

            <!-- Contenido de las subpestañas -->
            <div class="tab-content mt-3" id="activitySubTabContent">
                <!-- Subpestaña: Actividad por Estados -->
                <div class="tab-pane fade {{ (!isset($subtab) || $subtab === 'states') ? 'show active' : '' }}" 
                     id="states-report" 
                     role="tabpanel" 
                     aria-labelledby="states-subtab">
            
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-map-marked-alt mr-2"></i>Reporte de Actividad por Estados
                    </h3>
                </div>
                <div class="card-body py-2">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-2 bg-danger text-white rounded">
                                <h4 class="mb-0" style="font-size: 1.5rem;">{{ $totals['en_peligro'] }}</h4>
                                <small style="font-size: 0.75rem;">En Peligro</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 bg-warning text-white rounded">
                                <h4 class="mb-0" style="font-size: 1.5rem;">{{ $totals['rescatados'] }}</h4>
                                <small style="font-size: 0.75rem;">En Traslado</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 bg-success text-white rounded">
                                <h4 class="mb-0" style="font-size: 1.5rem;">{{ $totals['tratados'] }}</h4>
                                <small style="font-size: 0.75rem;">En Tratamiento</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 bg-info text-white rounded">
                                <h4 class="mb-0" style="font-size: 1.5rem;">{{ $totals['liberados'] }}</h4>
                                <small style="font-size: 0.75rem;">Liberados</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 text-center">
                            <h5 class="mb-0" style="font-size: 1rem;">
                                <strong>Total: {{ $totals['en_peligro'] + $totals['rescatados'] + $totals['tratados'] + $totals['liberados'] }}</strong>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla: En Peligro -->
            @if(!empty($enPeligro))
            <div class="card mt-3 border-danger">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Reporte de Animales en Peligro
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="bg-danger text-white">
                                <tr>
                                    <th style="width: 25%;">Provincia</th>
                                    <th style="width: 20%;">Estado</th>
                                    <th style="width: 25%;">Fecha Hallazgo</th>
                                    <th style="width: 30%;">Tiempo Transcurrido</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enPeligro as $report)
                                    <tr>
                                        <td>{{ $report['province'] }}</td>
                                        <td>
                                            <span class="badge badge-danger">En Peligro</span>
                                        </td>
                                        <td>{{ $report['fecha_hallazgo']->format('d/m/Y H:i') }}</td>
                                        <td>{{ $report['tiempo_transcurrido'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tabla: Rescatados -->
            @if(!empty($rescatados))
            <div class="card mt-3 border-warning">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-warning">
                        <i class="fas fa-ambulance mr-2"></i>Reporte de Animales en Traslado
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="bg-warning text-white">
                                <tr>
                                    <th style="width: 25%;">Centro de Destino</th>
                                    <th style="width: 15%;">Estado</th>
                                    <th style="width: 20%;">Fecha Hallazgo</th>
                                    <th style="width: 20%;">Fecha de Traslado</th>
                                    <th style="width: 20%;">Tiempo Transcurrido</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rescatados as $report)
                                    <tr>
                                        <td>
                                            @if($report['centro'])
                                                <span class="badge badge-info">
                                                    <i class="fas fa-building mr-1"></i>{{ $report['centro']->nombre }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">En Traslado</span>
                                        </td>
                                        <td>{{ $report['fecha_hallazgo']->format('d/m/Y H:i') }}</td>
                                        <td>{{ $report['fecha_traslado'] ? $report['fecha_traslado']->format('d/m/Y H:i') : '-' }}</td>
                                        <td>{{ $report['tiempo_hallazgo_traslado'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tabla: Tratados -->
            @if(!empty($tratados))
            <div class="card mt-3 border-success">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-success">
                        <i class="fas fa-stethoscope mr-2"></i>Reporte de Animales en Tratamiento
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th style="width: 20%;">Nombre</th>
                                    <th style="width: 15%;">Estado</th>
                                    <th style="width: 20%;">Fecha Hallazgo</th>
                                    <th style="width: 20%;">Fecha Inicio Tratamiento</th>
                                    <th style="width: 25%;">Tiempo desde Tratamiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tratados as $report)
                                    <tr>
                                        <td>{{ $report['nombre'] ?? 'Sin nombre' }}</td>
                                        <td>
                                            <span class="badge badge-success">Tratado</span>
                                        </td>
                                        <td>{{ $report['fecha_hallazgo']->format('d/m/Y H:i') }}</td>
                                        <td>{{ $report['fecha_tratamiento'] ? $report['fecha_tratamiento']->format('d/m/Y H:i') : '-' }}</td>
                                        <td>{{ $report['tiempo_desde_tratamiento'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tabla: Liberados -->
            @if(!empty($liberados))
            <div class="card mt-3 border-info">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-info">
                        <i class="fas fa-dove mr-2"></i>Reporte de Animales Liberados
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th style="width: 30%;">Nombre</th>
                                    <th style="width: 20%;">Estado</th>
                                    <th style="width: 25%;">Fecha Hallazgo</th>
                                    <th style="width: 25%;">Fecha Liberación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($liberados as $report)
                                    <tr>
                                        <td>{{ $report['nombre'] ?? 'Sin nombre' }}</td>
                                        <td>
                                            <span class="badge badge-info">Liberado</span>
                                        </td>
                                        <td>{{ $report['fecha_hallazgo']->format('d/m/Y H:i') }}</td>
                                        <td>{{ $report['fecha_liberacion']->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
                </div>
                <!-- Fin Subpestaña: Actividad por Estados -->

                <!-- Subpestaña: Salud Animal Actual -->
                <div class="tab-pane fade {{ (isset($subtab) && $subtab === 'health') ? 'show active' : '' }}" 
                     id="health-report" 
                     role="tabpanel" 
                     aria-labelledby="health-subtab">
                    
                    <div class="card mt-3">
                        <div class="card-header bg-success text-white">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-heartbeat mr-2"></i>Reporte de Salud Animal Actual
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <!-- Filtro por rango de fechas de inicio de tratamiento -->
                            <form method="GET" action="{{ route('reportes.index') }}" class="mb-0">
                                <input type="hidden" name="tab" value="activity">
                                <input type="hidden" name="subtab" value="health">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="text-muted"><i class="fas fa-info-circle mr-1"></i>Rango de Fechas - Inicio de Tratamiento</span>
                                    </div>
                                    <div class="col-auto">
                                        <label for="fecha_desde" class="form-label mb-0 mr-2">
                                            <i class="fas fa-calendar-alt mr-1"></i>Desde
                                        </label>
                                        <input type="date" 
                                               class="form-control d-inline-block" 
                                               style="width: auto;"
                                               id="fecha_desde" 
                                               name="fecha_desde" 
                                               value="{{ $fechaDesde ?? '' }}"
                                               max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-auto">
                                        <label for="fecha_hasta" class="form-label mb-0 mr-2">
                                            <i class="fas fa-calendar-alt mr-1"></i>Hasta
                                        </label>
                                        <input type="date" 
                                               class="form-control d-inline-block" 
                                               style="width: auto;"
                                               id="fecha_hasta" 
                                               name="fecha_hasta" 
                                               value="{{ $fechaHasta ?? '' }}"
                                               max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-filter mr-1"></i>Filtrar
                                        </button>
                                        @if(isset($fechaDesde) || isset($fechaHasta))
                                            <a href="{{ route('reportes.index', ['tab' => 'activity', 'subtab' => 'health']) }}" 
                                               class="btn btn-secondary ml-2">
                                                <i class="fas fa-times mr-1"></i>Limpiar
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body p-0 pt-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="bg-success text-white">
                                        <tr>
                                            <th style="width: 15%;">Centro</th>
                                            <th style="width: 12%;">Nombre del Animal</th>
                                            <th style="width: 18%;">Diagnóstico Inicial</th>
                                            <th style="width: 12%;">Fecha Inicial</th>
                                            <th style="width: 12%;">Fecha Última Evaluación</th>
                                            <th style="width: 21%;">Última Intervención Médica</th>
                                            <th style="width: 10%;">Estado Actual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($healthData) && !empty($healthData))
                                            @foreach($healthData as $data)
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-building mr-1"></i>{{ $data['centro'] }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $data['nombre_animal'] }}</td>
                                                    <td>{{ $data['diagnostico_inicial'] }}</td>
                                                    <td>
                                                        @if($data['fecha_creacion_hoja'])
                                                            {{ $data['fecha_creacion_hoja']->format('d/m/Y') }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($data['fecha_ultima_evaluacion'])
                                                            {{ $data['fecha_ultima_evaluacion']->format('d/m/Y') }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($data['ultima_intervencion'])
                                                            
                                                            @if($data['ultima_intervencion']['diagnostico'])
                                                                <div class="mb-1">
                                                                    {{ $data['ultima_intervencion']['diagnostico'] }}
                                                                </div>
                                                            @endif
                                                            @if($data['ultima_intervencion']['descripcion'])
                                                                <div>
                                                                    <small class="text-muted">{{ Str::limit($data['ultima_intervencion']['descripcion'], 50) }}</small>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">Sin intervenciones registradas</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-secondary">{{ $data['estado_actual'] }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    No hay animales en tratamiento actualmente
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fin Subpestaña: Salud Animal Actual -->
            </div>
            <!-- Fin Contenido de las subpestañas -->
        </div>

        <!-- Pestaña: Reportes de Gestión -->
        <div class="tab-pane fade {{ $tab === 'management' ? 'show active' : '' }}" 
             id="management" 
             role="tabpanel" 
             aria-labelledby="management-tab">
            
            <!-- Subpestañas dentro de Reportes de Gestión -->
            <ul class="nav nav-pills mt-3" id="managementSubTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ (!isset($management_subtab) || $management_subtab === 'rescue') ? 'active' : '' }}" 
                       id="rescue-subtab" 
                       href="{{ route('reportes.index', ['tab' => 'management', 'management_subtab' => 'rescue']) }}"
                       role="tab">
                        <i class="fas fa-ambulance mr-2"></i>Eficacia de Rescate
                    </a>
                </li>
                {{-- Temporalmente oculto
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ (isset($management_subtab) && $management_subtab === 'initial_review') ? 'active' : '' }}" 
                       id="initial-review-subtab" 
                       href="{{ route('reportes.index', ['tab' => 'management', 'management_subtab' => 'initial_review']) }}"
                       role="tab">
                        <i class="fas fa-stethoscope mr-2"></i>Eficacia de Revisión Inicial
                    </a>
                </li>
                --}}
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ (isset($management_subtab) && $management_subtab === 'treatment') ? 'active' : '' }}" 
                       id="treatment-subtab" 
                       href="{{ route('reportes.index', ['tab' => 'management', 'management_subtab' => 'treatment']) }}"
                       role="tab">
                        <i class="fas fa-heartbeat mr-2"></i>Eficacia de Tratamiento
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ (isset($management_subtab) && $management_subtab === 'release') ? 'active' : '' }}" 
                       id="release-subtab" 
                       href="{{ route('reportes.index', ['tab' => 'management', 'management_subtab' => 'release']) }}"
                       role="tab">
                        <i class="fas fa-dove mr-2"></i>Eficacia de Liberación
                    </a>
                </li>
            </ul>

            <!-- Contenido de las subpestañas -->
            <div class="tab-content mt-3" id="managementSubTabContent">
                <!-- Subpestaña: Eficacia de Rescate -->
                <div class="tab-pane fade {{ (!isset($management_subtab) || $management_subtab === 'rescue') ? 'show active' : '' }}" 
                     id="rescue-report" 
                     role="tabpanel" 
                     aria-labelledby="rescue-subtab">
            
            @include('reports.partials.efficiency-report', [
                'title' => 'Eficacia Mensual del Rescate de Animales',
                'icon' => 'ambulance',
                'eficaciaMensual' => $eficaciaMensual ?? 0,
                'valor130Dias' => $hallazgos30Dias ?? 0,
                'valor230Dias' => $traslados30Dias ?? 0,
                'label1' => 'hallazgos',
                'label2' => 'traslados',
                'datosDiarios' => $datosDiarios ?? [],
                'filtro' => $filtro ?? 'mes',
                'fechaDesde' => $fechaDesde ?? null,
                'fechaHasta' => $fechaHasta ?? null,
                'management_subtab' => 'rescue',
                'columnas' => [
                    ['nombre' => 'Fecha', 'campo' => 'fecha'],
                    ['nombre' => 'Cantidad de Hallazgos', 'campo' => 'hallazgos'],
                    ['nombre' => 'Cantidad de Traslados', 'campo' => 'traslados'],
                    ['nombre' => 'Eficacia Diaria (%)', 'campo' => 'eficacia'],
                    ['nombre' => 'Estado', 'campo' => 'color']
                ]
            ])
                </div>
                <!-- Fin Subpestaña: Eficacia de Rescate -->

                {{-- Temporalmente oculto
                <!-- Subpestaña: Eficacia de Revisión Inicial -->
                <div class="tab-pane fade {{ (isset($management_subtab) && $management_subtab === 'initial_review') ? 'show active' : '' }}" 
                     id="initial-review-report" 
                     role="tabpanel" 
                     aria-labelledby="initial-review-subtab">
                    
                    @include('reports.partials.efficiency-report', [
                        'title' => 'Eficacia de Revisión Inicial',
                        'icon' => 'stethoscope',
                        'eficaciaMensual' => $eficaciaMensual ?? 0,
                        'valor130Dias' => $traslados30Dias ?? 0,
                        'valor230Dias' => $evaluaciones30Dias ?? 0,
                        'label1' => 'traslados',
                        'label2' => 'evaluaciones iniciales',
                        'datosDiarios' => $datosDiarios ?? [],
                        'filtro' => $filtro ?? 'mes',
                        'fechaDesde' => $fechaDesde ?? null,
                        'fechaHasta' => $fechaHasta ?? null,
                        'management_subtab' => 'initial_review',
                        'columnas' => [
                            ['nombre' => 'Fecha', 'campo' => 'fecha'],
                            ['nombre' => 'Cantidad de Traslados', 'campo' => 'traslados'],
                            ['nombre' => 'Cantidad de Eval. Médicas Iniciales', 'campo' => 'evaluaciones'],
                            ['nombre' => 'Eficacia Diaria (%)', 'campo' => 'eficacia'],
                            ['nombre' => 'Estado', 'campo' => 'color']
                        ]
                    ])
                </div>
                <!-- Fin Subpestaña: Eficacia de Revisión Inicial -->
                --}}

                <!-- Subpestaña: Eficacia de Tratamiento -->
                <div class="tab-pane fade {{ (isset($management_subtab) && $management_subtab === 'treatment') ? 'show active' : '' }}" 
                     id="treatment-report" 
                     role="tabpanel" 
                     aria-labelledby="treatment-subtab">
                    
                    @include('reports.partials.efficiency-report', [
                        'title' => 'Eficacia de los Tratamientos',
                        'icon' => 'heartbeat',
                        'eficaciaMensual' => $eficaciaMensual ?? 0,
                        'valor130Dias' => $animalesEnTratamiento30Dias ?? 0,
                        'valor230Dias' => $animalesEstables30Dias ?? 0,
                        'label1' => 'animales en tratamiento',
                        'label2' => 'animales estables',
                        'datosDiarios' => $datosDiarios ?? [],
                        'filtro' => $filtro ?? 'mes',
                        'fechaDesde' => $fechaDesde ?? null,
                        'fechaHasta' => $fechaHasta ?? null,
                        'management_subtab' => 'treatment',
                        'columnas' => [
                            ['nombre' => 'Fecha', 'campo' => 'fecha'],
                            ['nombre' => 'Animales en Tratamiento', 'campo' => 'en_tratamiento'],
                            ['nombre' => 'Animales Estables', 'campo' => 'estables'],
                            ['nombre' => 'Eficacia Diaria (%)', 'campo' => 'eficacia'],
                            ['nombre' => 'Estado', 'campo' => 'color']
                        ]
                    ])
                </div>
                <!-- Fin Subpestaña: Eficacia de Tratamiento -->

                <!-- Subpestaña: Eficacia de Liberación -->
                <div class="tab-pane fade {{ (isset($management_subtab) && $management_subtab === 'release') ? 'show active' : '' }}" 
                     id="release-report" 
                     role="tabpanel" 
                     aria-labelledby="release-subtab">
                    
                    @include('reports.partials.efficiency-report', [
                        'title' => 'Eficacia de la Liberación',
                        'icon' => 'dove',
                        'eficaciaMensual' => $eficaciaMensual ?? 0,
                        'valor130Dias' => $animalesEstables30Dias ?? 0,
                        'valor230Dias' => $animalesLiberados30Dias ?? 0,
                        'label1' => 'animales estables',
                        'label2' => 'animales liberados',
                        'datosDiarios' => $datosDiarios ?? [],
                        'filtro' => $filtro ?? 'mes',
                        'fechaDesde' => $fechaDesde ?? null,
                        'fechaHasta' => $fechaHasta ?? null,
                        'management_subtab' => 'release',
                        'columnas' => [
                            ['nombre' => 'Fecha', 'campo' => 'fecha'],
                            ['nombre' => 'Animales Estables', 'campo' => 'estables'],
                            ['nombre' => 'Animales Liberados', 'campo' => 'liberados'],
                            ['nombre' => 'Eficacia Diaria (%)', 'campo' => 'eficacia'],
                            ['nombre' => 'Estado', 'campo' => 'color']
                        ]
                    ])
                </div>
                <!-- Fin Subpestaña: Eficacia de Liberación -->
            </div>
            <!-- Fin Contenido de las subpestañas -->
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stop
