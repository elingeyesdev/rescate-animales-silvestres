@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-dark">Panel de Control</h1>
        <div class="d-flex align-items-center">
            @if(Auth::user()->hasAnyRole(['admin', 'encargado']))
            <a href="{{ route('dashboard.export-pdf') }}" class="btn btn-danger btn-sm mr-2" target="_blank">
                <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
            </a>
            <a href="{{ route('dashboard.export-excel') }}" class="btn btn-success btn-sm mr-2" target="_blank">
                <i class="fas fa-file-excel mr-1"></i> Exportar a Excel
            </a>
            @endif
            <small class="text-muted"><i class="fas fa-calendar-alt mr-1"></i> {{ date('d/m/Y') }}</small>
        </div>
    </div>
@stop

@section('css')
<style>
    /* Efecto Hover Personalizado para Botones de Acción */
    .btn-action-custom {
        background-color: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    }
    .btn-action-custom:hover {
        transform: translateY(-5px); /* Se eleva */
        box-shadow: 0 14px 28px rgba(0,0,0,0.15), 0 10px 10px rgba(0,0,0,0.12); /* Sombra profunda */
        border-color: transparent;
        z-index: 10;
    }
    .btn-action-custom i {
        transition: transform 0.3s ease;
    }
    .btn-action-custom:hover i {
        transform: scale(1.2); /* El icono crece un poco */
    }
    
    /* Alinear el content_header con el contenido principal */
    .content-header {
        padding-left: 15px !important;
        padding-right: 15px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-bottom: 0 !important; /* Eliminar margen inferior */
        padding-bottom: 0.5rem !important; /* Reducir padding inferior */
        width: 100% !important;
        box-sizing: border-box !important;
    }
    
    /* Asegurar que el contenedor principal tenga el mismo padding horizontal que el card del index */
    /* El card del index está dentro de section.content (padding 15px) + col-sm-12 (padding 15px) = 30px total */
    .content-wrapper .content {
        padding-left: 5px;
        padding-right: 5px;
    }
    
    /* Asegurar que el container-fluid tenga el mismo padding horizontal que el card del index */
    /* El card del index tiene padding horizontal de: .content (15px) + .col-sm-12 (15px) = 30px */
    .container-fluid {
        width: 100%;
        box-sizing: border-box;
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
    
    /* Reducir significativamente el padding vertical entre Panel de Control y los cards */
    .container-fluid {
        padding-top: 0.25rem; /* Reducido aún más */
    }
    
    @media (min-width: 768px) {
        .container-fluid {
            padding-top: 0.25rem; /* Reducido aún más */
        }
    }
    
    /* Aplicar el mismo margen inferior que tiene el index (30px según .report-grid > [class*='col-'] { margin-bottom: 30px; }) */
    .container-fluid .row > [class*="col-"] {
        margin-bottom: 30px;
    }
    
    /* Asegurar que todos los divs internos tengan el mismo espaciado */
    .info-box,
    .card:not(.shadow-none) {
        margin-bottom: 0 !important;
    }
    
    /* Ajustar el margen de la sección de acciones rápidas */
    .card.shadow-none.bg-transparent {
        margin-bottom: 15px !important;
    }

    /* Ocultar elementos colapsados al imprimir o exportar a PDF */
    @media print {
        .collapsed-card {
            display: none !important;
        }
        
        .no-export-when-collapsed.collapsed-card {
            display: none !important;
        }
    }
    
    /* Para PDFs generados con DomPDF, ocultar cards colapsados */
    .collapsed-card .card-body {
        display: none !important;
    }
</style>
@stop

@section('content')
<div class="container-fluid pb-4">

    {{-- =========================================================== --}}
    {{-- SECCIÓN: ADMIN Y ENCARGADO --}}
    {{-- =========================================================== --}}
    @if(Auth::user()->hasAnyRole(['admin', 'encargado']))
        
        {{-- Sistema de Pestañas para Dashboard --}}
        <div class="card card-primary card-outline">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="resumen-tab" data-toggle="tab" href="#resumen" role="tab" aria-controls="resumen" aria-selected="true">
                            <i class="fas fa-chart-line mr-2"></i>Resumen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="analisis-tab" data-toggle="tab" href="#analisis" role="tab" aria-controls="analisis" aria-selected="false">
                            <i class="fas fa-chart-bar mr-2"></i>Análisis
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.mapa-campo') }}">
                            <i class="fas fa-map-marked-alt mr-2"></i>Mapa de Campo
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="dashboardTabsContent">
                    
                    {{-- PESTAÑA 1: RESUMEN --}}
                    <div class="tab-pane fade show active" id="resumen" role="tabpanel" aria-labelledby="resumen-tab">
                        
                        {{-- 1. Tarjetas de Estadísticas Principales (KPIs) --}}
                        <div class="row">
                            {{-- Hallazgos Pendientes --}}
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box shadow-sm mb-3 h-100">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-search-location"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text text-muted">Hallazgos Pendientes</span>
                                        <span class="info-box-number display-6">{{ $pendingReportsCount ?? 0 }}</span>
                                        
                                        @php 
                                            $total = $totalReports ?? 0; 
                                            $pending = $pendingReportsCount ?? 0; 
                                            $pct = $total > 0 ? intval(($pending / $total) * 100) : 0; 
                                        @endphp
                                        <div class="progress progress-sm mt-2">
                                            <div class="progress-bar bg-info" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="progress-description text-xs text-muted">
                                            {{ $pct }}% del total reportado
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Solicitudes de Personal --}}
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box shadow-sm mb-3 h-100">
                                    <span class="info-box-icon bg-warning elevation-1 text-white"><i class="fas fa-users-cog"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text text-muted">Solicitudes Pendientes</span>
                                        <span class="info-box-number">
                                            {{ ($pendingRescuersCount ?? 0) + ($pendingVeterinariansCount ?? 0) + ($pendingCaregiversCount ?? 0) }}
                                        </span>
                                        <a href="{{ route('people.index') }}" class="text-xs text-warning font-weight-bold mt-2 d-block">
                                            Revisar solicitudes <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Total Animales --}}
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box shadow-sm mb-3 h-100">
                                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-paw"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text text-muted">Animales en Sistema</span>
                                        <span class="info-box-number">{{ $totalAnimals ?? 0 }}</span>
                                        <a href="{{ route('animal-files.index') }}" class="text-xs text-success font-weight-bold mt-2 d-block">
                                            Ver animales <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Mensajes --}}
                            <div class="col-12 col-sm-6 col-md-3">
                                <a href="{{ route('profile.index') }}#contactar" class="text-decoration-none">
                                    <div class="info-box shadow-sm mb-3 h-100">
                                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-envelope-open-text"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted">Mensajes Nuevos</span>
                                            <span class="info-box-number">{{ $unreadMessagesCount ?? 0 }}</span>
                                            <span class="progress-description text-xs text-muted">
                                                Bandeja de entrada
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        {{-- KPIs: Actividad, Eficacia y Efectividad --}}
                        <div class="row mt-2">
            {{-- KPIs de Actividad --}}
            <div class="col-12 col-lg-4 mb-2">
                <div class="card shadow-sm mb-0 h-100">
                    <div class="card-header bg-gradient-primary text-white py-2">
                        <h4 class="card-title mb-0" style="font-size: 0.95rem;">
                            <i class="fas fa-chart-line mr-1"></i>Actividad
                        </h4>
                    </div>
                    <div class="card-body p-2">
                        <div class="row no-gutters">
                            <div class="col-4 text-center border-right">
                                <i class="fas fa-heartbeat text-info" style="font-size: 1.1rem;"></i>
                                <div class="h5 mb-0" style="font-size: 1.3rem;">{{ $animalsBeingRescued ?? 0 }}</div>
                                <small class="text-muted" style="font-size: 0.65rem;">Rescatados</small>
                            </div>
                            <div class="col-4 text-center border-right">
                                <i class="fas fa-ambulance text-warning" style="font-size: 1.1rem;"></i>
                                <div class="h5 mb-0" style="font-size: 1.3rem;">{{ $animalsBeingTransferred ?? 0 }}</div>
                                <small class="text-muted" style="font-size: 0.65rem;">Trasladados</small>
                            </div>
                            <div class="col-4 text-center">
                                <i class="fas fa-stethoscope text-success" style="font-size: 1.1rem;"></i>
                                <div class="h5 mb-0" style="font-size: 1.3rem;">{{ $animalsBeingTreated ?? 0 }}</div>
                                <small class="text-muted" style="font-size: 0.65rem;">Tratados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPIs de Eficacia --}}
            <div class="col-12 col-lg-4 mb-2">
                <div class="card shadow-sm mb-0 h-100">
                    <div class="card-header bg-gradient-success text-white py-2">
                        <h4 class="card-title mb-0" style="font-size: 0.95rem;">
                            <i class="fas fa-check-circle mr-1"></i>Eficacia
                        </h4>
                    </div>
                    <div class="card-body p-2">
                        @php
                            $efficiencyAttended = $efficiencyAttendedRescued ?? ['attended' => 0, 'rescued' => 0, 'percentage' => 0];
                            $efficiencyReady = $efficiencyReadyAttended ?? ['ready' => 0, 'attended' => 0, 'percentage' => 0];
                            $effectiveness = $effectivenessReleasedRescued ?? ['released' => 0, 'rescued' => 0, 'percentage' => 0];
                        @endphp
                        
                        {{-- Eficacia de Rescate --}}
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    <i class="fas fa-heartbeat text-danger" style="font-size: 0.7rem;"></i> Eficacia de Rescate
                                </small>
                                <span class="badge badge-success" style="font-size: 0.75rem;">{{ $efficiencyAttended['percentage'] }}%</span>
                            </div>
                            <div class="progress" style="height: 14px;">
                                <div class="progress-bar bg-success" style="width: {{ $efficiencyAttended['percentage'] }}%">
                                    <small style="font-size: 0.6rem;">{{ $efficiencyAttended['attended'] }}/{{ $efficiencyAttended['rescued'] }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- Eficacia de Tratamiento --}}
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    <i class="fas fa-stethoscope text-info" style="font-size: 0.7rem;"></i> Eficacia de Tratamiento
                                </small>
                                <span class="badge badge-info" style="font-size: 0.75rem;">{{ $efficiencyReady['percentage'] }}%</span>
                            </div>
                            <div class="progress" style="height: 14px;">
                                <div class="progress-bar bg-info" style="width: {{ $efficiencyReady['percentage'] }}%">
                                    <small style="font-size: 0.6rem;">{{ $efficiencyReady['ready'] }}/{{ $efficiencyReady['attended'] }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- Eficacia de Liberación --}}
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    <i class="fas fa-dove text-warning" style="font-size: 0.7rem;"></i> Eficacia de Liberación
                                </small>
                                <span class="badge badge-warning" style="font-size: 0.75rem;">{{ $effectiveness['percentage'] }}%</span>
                            </div>
                            <div class="progress" style="height: 14px;">
                                <div class="progress-bar bg-warning" style="width: {{ $effectiveness['percentage'] }}%">
                                    <small style="font-size: 0.6rem;">{{ $effectiveness['released'] }}/{{ $effectiveness['rescued'] }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI de Efectividad --}}
            <div class="col-12 col-lg-4 mb-2">
                <div class="card shadow-sm mb-0 h-100">
                    <div class="card-header bg-gradient-danger text-white py-2">
                        <h4 class="card-title mb-0" style="font-size: 0.95rem;">
                            <i class="fas fa-trophy mr-1"></i>Efectividad
                        </h4>
                    </div>
                    <div class="card-body p-2">
                        @php
                            $effectiveness = $effectivenessReleasedRescued ?? ['released' => 0, 'rescued' => 0, 'percentage' => 0];
                        @endphp
                        
                        <div class="text-center">
                            <h3 class="mb-0 text-danger" style="font-size: 1.5rem;">{{ $effectiveness['percentage'] }}%</h3>
                            <small class="text-muted d-block mb-1" style="font-size: 0.65rem;">Tasa de Éxito</small>
                            
                            <div class="row no-gutters mb-0">
                                <div class="col-6 pr-1">
                                    <div class="bg-light rounded p-1 text-center">
                                        <i class="fas fa-dove text-danger" style="font-size: 0.9rem;"></i>
                                        <div class="h6 mb-0" style="font-size: 1rem;">{{ $effectiveness['released'] }}</div>
                                        <small style="font-size: 0.75rem;">Liberados</small>
                                    </div>
                                </div>
                                <div class="col-6 pl-1">
                                    <div class="bg-light rounded p-1 text-center">
                                        <i class="fas fa-paw text-primary" style="font-size: 0.9rem;"></i>
                                        <div class="h6 mb-0" style="font-size: 1rem;">{{ $effectiveness['rescued'] }}</div>
                                        <small style="font-size: 0.75rem;">Rescatados</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="progress" style="height: 18px;">
                                <div class="progress-bar bg-danger" style="width: {{ $effectiveness['percentage'] }}%">
                                    <small style="font-size: 0.65rem;">{{ $effectiveness['released'] }}/{{ $effectiveness['rescued'] }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                    </div>
                    
                    {{-- PESTAÑA 2: ANÁLISIS --}}
                    <div class="tab-pane fade" id="analisis" role="tabpanel" aria-labelledby="analisis-tab">
                        
                        {{-- Sección de Gráficos Compactos --}}
                        <div class="row">
                            {{-- Gráfico 1: Comparativa Operativa --}}
                            @if(isset($reportsByMonth))
                            <div class="col-lg-6 mb-3">
                                <div class="card card-outline card-purple shadow-sm h-100 no-export-when-collapsed">
                                    <div class="card-header border-0 py-2">
                                        <h4 class="card-title font-weight-bold text-dark mb-1" style="font-size: 1rem;">
                                            <i class="fas fa-chart-bar mr-2 text-purple"></i> Comparativa Operativa
                                        </h4>
                                        <div class="row align-items-center">
                                            <div class="col-auto pr-2">
                                                <label class="small font-weight-bold text-muted mb-0" style="font-size: 0.7rem;">Período:</label>
                                                <select id="filterPeriodCompare" class="form-control form-control-sm" style="font-size: 0.7rem; padding: 0.15rem 0.3rem; width: auto; min-width: 120px;">
                                                    <option value="all">Todo el período</option>
                                                    <option value="week">Última semana</option>
                                                    <option value="month">Último mes</option>
                                                    <option value="3months">Últimos 3 meses</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label class="small font-weight-bold text-muted mb-0 mr-2" style="font-size: 0.7rem;">Mostrar:</label>
                                                <div class="d-inline-flex flex-wrap align-items-center">
                                                    <div class="form-check form-check-inline mb-0 mr-2" style="line-height: 1;">
                                                        <input class="form-check-input series-toggle" type="checkbox" id="toggleHallazgos" data-series="hallazgos" checked style="margin-top: 0.1rem;">
                                                        <label class="form-check-label mb-0" for="toggleHallazgos" style="font-size: 0.65rem;">Hallazgos</label>
                                                    </div>
                                                    <div class="form-check form-check-inline mb-0 mr-2" style="line-height: 1;">
                                                        <input class="form-check-input series-toggle" type="checkbox" id="toggleTraslados" data-series="traslados" checked style="margin-top: 0.1rem;">
                                                        <label class="form-check-label mb-0" for="toggleTraslados" style="font-size: 0.65rem;">Traslados</label>
                                                    </div>
                                                    <div class="form-check form-check-inline mb-0 mr-2" style="line-height: 1;">
                                                        <input class="form-check-input series-toggle" type="checkbox" id="toggleLiberaciones" data-series="liberaciones" checked style="margin-top: 0.1rem;">
                                                        <label class="form-check-label mb-0" for="toggleLiberaciones" style="font-size: 0.65rem;">Liberaciones</label>
                                                    </div>
                                                    <div class="form-check form-check-inline mb-0" style="line-height: 1;">
                                                        <input class="form-check-input series-toggle" type="checkbox" id="toggleHojas" data-series="hojas" checked style="margin-top: 0.1rem;">
                                                        <label class="form-check-label mb-0" for="toggleHojas" style="font-size: 0.65rem;">Hojas</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body py-2">
                                        <canvas id="operationsCompareChart" style="max-height: 220px; height: 220px !important;"></canvas>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Gráfico 2: Estado de Animales --}}
                            @if(isset($animalsByStatus))
                            <div class="col-lg-6 mb-3">
                                <div class="card card-outline card-info shadow-sm h-100 no-export-when-collapsed">
                                    <div class="card-header border-0 py-2">
                                        <h4 class="card-title font-weight-bold text-dark mb-1" style="font-size: 1rem;">
                                            <i class="fas fa-chart-pie mr-2 text-info"></i> Estado Actual
                                        </h4>
                                        <div class="row align-items-center">
                                            <div class="col-auto pr-2">
                                                <label class="small font-weight-bold text-muted mb-0" style="font-size: 0.7rem;">Período:</label>
                                                <select id="filterPeriodStatus" class="form-control form-control-sm" style="font-size: 0.7rem; padding: 0.15rem 0.3rem; width: auto; min-width: 120px;">
                                                    <option value="all">Todo el período</option>
                                                    <option value="week">Última semana</option>
                                                    <option value="month">Último mes</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label class="small font-weight-bold text-muted mb-0 mr-2" style="font-size: 0.7rem;">Estados:</label>
                                                <div id="statusCheckboxes" class="d-inline-flex flex-wrap align-items-center">
                                                    @if(isset($animalsByStatus))
                                                        @foreach($animalsByStatus as $status => $count)
                                                            <div class="form-check form-check-inline mb-0 mr-2" style="line-height: 1;">
                                                                <input class="form-check-input status-toggle" type="checkbox" id="toggleStatus{{ $loop->index }}" data-status="{{ $status }}" checked style="margin-top: 0.1rem;">
                                                                <label class="form-check-label mb-0" for="toggleStatus{{ $loop->index }}" style="font-size: 0.65rem;">
                                                                    {{ $status }} ({{ $count }})
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body py-2">
                                        <canvas id="animalsStatusChart" style="max-height: 220px; height: 220px !important;"></canvas>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        {{-- Segunda Fila: Solicitudes y Top 5 --}}
                        <div class="row">
                            {{-- Gráfico 3: Solicitudes de Voluntariado --}}
                            @if(isset($applicationsByType))
                            <div class="col-lg-6 mb-3">
                                <div class="card card-outline card-secondary shadow-sm h-100 no-export-when-collapsed">
                                    <div class="card-header border-0 py-2">
                                        <h4 class="card-title font-weight-bold mb-0" style="font-size: 1rem;">
                                            <i class="fas fa-user-plus mr-2 text-secondary"></i> Solicitudes de Voluntariado
                                        </h4>
                                    </div>
                                    <div class="card-body py-2">
                                        <canvas id="applicationsChart" style="max-height: 200px; height: 200px !important;"></canvas>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Widget: Top 5 Voluntarios Más Activos --}}
                            <div class="col-lg-6 mb-3">
                                <div class="card card-outline card-warning shadow-sm h-100 no-export-when-collapsed">
                                    <div class="card-header border-0 py-2">
                                        <h4 class="card-title font-weight-bold mb-0" style="font-size: 1rem;">
                                            <i class="fas fa-trophy mr-2 text-warning"></i> Top 5 Voluntarios Más Activos
                                        </h4>
                                    </div>
                                    <div class="card-body py-2" style="max-height: 250px; overflow-y: auto;">
                                        @php $topVolunteers = $topVolunteers ?? []; @endphp
                                        @if(count($topVolunteers) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 40px;">N°</th>
                                                            <th>Voluntario</th>
                                                            <th class="text-center">Total</th>
                                                            <th class="text-right">Desglose</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($topVolunteers as $index => $volunteer)
                                                            <tr>
                                                                <td>
                                                                    @if($index === 0)
                                                                        <span class="badge badge-warning">🥇</span>
                                                                    @elseif($index === 1)
                                                                        <span class="badge badge-secondary">🥈</span>
                                                                    @elseif($index === 2)
                                                                        <span class="badge badge-info">🥉</span>
                                                                    @else
                                                                        <strong>{{ $index + 1 }}</strong>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div>
                                                                        <strong>{{ $volunteer['nombre'] }}</strong>
                                                                        <br>
                                                                        <small class="text-muted">{{ $volunteer['email'] }}</small>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge badge-primary" style="font-size: 1rem;">{{ $volunteer['total'] }}</span>
                                                                </td>
                                                                <td class="text-right">
                                                                    <small class="text-muted">
                                                                        @if($volunteer['reports'] > 0)
                                                                            <span class="badge badge-info">{{ $volunteer['reports'] }} Hallazgos</span>
                                                                        @endif
                                                                        @if($volunteer['transfers'] > 0)
                                                                            <span class="badge badge-success">{{ $volunteer['transfers'] }} Traslados</span>
                                                                        @endif
                                                                        @if($volunteer['evaluations'] > 0)
                                                                            <span class="badge badge-warning">{{ $volunteer['evaluations'] }} Evaluaciones</span>
                                                                        @endif
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4 text-muted">
                                                <i class="fas fa-users fa-2x mb-2"></i>
                                                <p>No hay datos de voluntarios disponibles</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                            </div>
                            </div>
                        </div>

    @endif

    {{-- =========================================================== --}}
    {{-- SECCIÓN: RESUMEN GENERAL (Roles Rescatista/Vet/Ciudadano) --}}
    {{-- =========================================================== --}}
    
    @php
        $isOnlyCitizen = Auth::user()->hasRole('ciudadano') && !Auth::user()->hasAnyRole(['admin', 'encargado', 'rescatista', 'veterinario', 'cuidador']);
    @endphp

    @if($isOnlyCitizen || Auth::user()->hasAnyRole(['rescatista', 'veterinario', 'cuidador']))
    <div class="row mt-0 align-items-stretch">        
        <div class="col-md-3 col-sm-6 col-12 d-flex">
            <div class="card bg-gradient-info text-white shadow-sm mb-3 w-100">
                <div class="card-body">
                    <h2 class="font-weight-bold mb-0">{{ $totalAnimals ?? 0 }}</h2>
                    <p class="mb-0">Animales Rescatados</p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <i class="fas fa-paw fa-2x opacity-50 float-right" style="opacity: 0.3;"></i>
                    @if(!$isOnlyCitizen)
                    <a href="{{ route('animal-files.index') }}" class="text-white small stretched-link">Ver detalles <i class="fas fa-arrow-right ml-1"></i></a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12 d-flex">
            <div class="card bg-gradient-success text-white shadow-sm mb-3 w-100">
                <div class="card-body">
                    <h2 class="font-weight-bold mb-0">{{ $releasedAnimals ?? 0 }}</h2>
                    <p class="mb-0">Devueltos al Hábitat</p>
                    @php $totalA = $totalAnimals ?? 0; $released = $releasedAnimals ?? 0; $rpct = $totalA > 0 ? intval(($released / $totalA) * 100) : 0; @endphp
                    <div class="progress mt-2" style="height: 4px; background-color: rgba(255,255,255,0.3);">
                        <div class="progress-bar bg-white" style="width: {{ $rpct }}%"></div>
                    </div>
                    <small>{{ $rpct }}% tasa de éxito</small>
                </div>
                @if(!$isOnlyCitizen)
                <a href="{{ route('releases.index') }}" class="stretched-link"></a>
                @endif
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12 d-flex">
            <div class="card card-outline card-warning shadow-sm mb-3 w-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="font-weight-bold text-warning">{{ $totalReports ?? 0 }}</h3>
                            <p class="text-muted mb-0">Hallazgos Recibidos</p>
                        </div>
                        <i class="fas fa-clipboard-list fa-2x text-warning opacity-50"></i>
                    </div>
                </div>
                @if(!$isOnlyCitizen)
                <div class="card-footer bg-white text-center p-1">
                     <a href="{{ route('reports.index') }}" class="text-warning small">Ir a reportes</a>
                </div>
                @endif
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12 d-flex">
            @if(Auth::user()->hasRole('cuidador') && !Auth::user()->hasAnyRole(['admin', 'encargado']))
            <div class="card card-outline card-primary shadow-sm mb-3 w-100">
                <div class="card-body">
                     <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="font-weight-bold text-primary">{{ $totalAnimals ?? 0 }}</h3>
                            <p class="text-muted mb-0">Animales</p>
                        </div>
                        <i class="fas fa-paw fa-2x text-primary opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-white text-center p-1">
                     <a href="{{ route('animal-histories.index') }}" class="text-primary small">Ver historial</a>
                </div>
            </div>
            @else
            <div class="card card-outline card-primary shadow-sm mb-3 w-100">
                <div class="card-body">
                     <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="font-weight-bold text-primary">{{ $totalTransfers ?? 0 }}</h3>
                            <p class="text-muted mb-0">Traslados</p>
                        </div>
                        <i class="fas fa-ambulance fa-2x text-primary opacity-50"></i>
                    </div>
                </div>
                @if(!$isOnlyCitizen)
                <div class="card-footer bg-white text-center p-1">
                     <a href="{{ route('transfers.index') }}" class="text-primary small">Ver logística</a>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- =========================================================== --}}
    {{-- SECCIÓN: KPIs PARA VETERINARIOS --}}
    {{-- =========================================================== --}}
    @if(Auth::user()->hasRole('veterinario') && !Auth::user()->hasAnyRole(['admin', 'encargado']))
        <div class="row mt-2">
            <div class="col-12">
                <div class="card shadow-sm mb-2">
                    <div class="card-header bg-gradient-success text-white py-2">
                        <h4 class="card-title mb-0" style="font-size: 0.95rem;">
                            <i class="fas fa-user-md mr-1"></i>Mi Actividad
                        </h4>
                    </div>
                    <div class="card-body p-2">
                        <div class="row no-gutters text-center">
                            <div class="col-4 border-right">
                                <i class="fas fa-paw text-info mb-1" style="font-size: 1.5rem;"></i>
                                <div class="h4 mb-0">{{ $myAnimalFiles ?? 0 }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Pacientes</small>
                            </div>
                            <div class="col-4 border-right">
                                <i class="fas fa-clipboard-check text-warning mb-1" style="font-size: 1.5rem;"></i>
                                <div class="h4 mb-0">{{ $recentEvaluations ?? 0 }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Recientes (7d)</small>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-heartbeat text-success mb-1" style="font-size: 1.5rem;"></i>
                                <div class="h4 mb-0">{{ $animalsInTreatment ?? 0 }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">En Tratamiento</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================== --}}
    {{-- SECCIÓN: KPIs PARA RESCATISTAS --}}
    {{-- =========================================================== --}}
    @if(Auth::user()->hasRole('rescatista') && !Auth::user()->hasAnyRole(['admin', 'encargado']))
        <div class="row mt-2">
            <div class="col-12">
                <div class="card shadow-sm mb-2">
                    <div class="card-header bg-gradient-primary text-white py-2">
                        <h4 class="card-title mb-0" style="font-size: 0.95rem;">
                            <i class="fas fa-ambulance mr-1"></i>Mi Actividad
                        </h4>
                    </div>
                    <div class="card-body p-2">
                        <div class="row no-gutters text-center">
                            <div class="col-6 border-right">
                                <i class="fas fa-exchange-alt text-primary mb-1" style="font-size: 1.5rem;"></i>
                                <div class="h4 mb-0">{{ $myTransfers ?? 0 }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Total Traslados</small>
                            </div>
                            <div class="col-6">
                                <i class="fas fa-clock text-info mb-1" style="font-size: 1.5rem;"></i>
                                <div class="h4 mb-0">{{ $recentTransfers ?? 0 }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Recientes (7d)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================== --}}
    {{-- SECCIÓN: BIENVENIDA ESPECÍFICA (Resto de roles) --}}
    {{-- =========================================================== --}}
    @if(!Auth::user()->hasAnyRole(['admin', 'encargado']))
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-lg border-0 bg-white">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-paw fa-2x text-dark mb-3" style="width: 80px;"></i>
                        <h2 class="display-4 font-weight-bold text-dark">Hola, {{ Auth::user()->person->nombre ?? 'Usuario' }}</h2>
                        <p class="lead text-muted">Bienvenido al Sistema de Rescate de Animales.</p>
                        
                        <div class="d-flex justify-content-center mt-4">
                            @if(Auth::user()->hasRole('veterinario'))
                                <div class="px-4 border-right">
                                    <h5 class="font-weight-bold text-success">{{ $myAnimalFiles ?? 0 }}</h5>
                                    <small class="text-uppercase text-muted">Mis Pacientes</small>
                                </div>
                            @endif
                            @if(Auth::user()->hasRole('rescatista'))
                                <div class="px-4">
                                    <h5 class="font-weight-bold text-primary">{{ $myTransfers ?? 0 }}</h5>
                                    <small class="text-uppercase text-muted">Mis Traslados</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@include('partials.page-pad')
@endsection

@section('js')
@if(Auth::user()->hasAnyRole(['admin', 'encargado']))
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    Chart.defaults.font.family = "'Source Sans Pro', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.color = '#666';

    // 1. Gráfico Comparativa Operativa con Filtros
    @php $totalsAvailable = isset($totalReports) || isset($totalTransfers) || isset($releasedAnimals) || isset($totalAnimals); @endphp
    @if($totalsAvailable)
    let compareChart = null;
    const cmp = document.getElementById('operationsCompareChart');
    if (cmp) {
        // Datos originales
        const originalTotals = {
            hallazgos: {{ $totalReports ?? 0 }},
            traslados: {{ $totalTransfers ?? 0 }},
            liberaciones: {{ $releasedAnimals ?? 0 }},
            hojas: {{ $totalAnimals ?? 0 }}
        };
        
        // Datos detallados para filtrado por fecha
        const reportsDetailed = @json($reportsDetailed ?? []);
        const transfersDetailed = @json($transfersDetailed ?? []);
        const releasesDetailed = @json($releasesDetailed ?? []);
        const animalFilesDetailed = @json($animalFilesDetailed ?? []);
        
        // Estado de series visibles
        const visibleSeries = {
            hallazgos: true,
            traslados: true,
            liberaciones: true,
            hojas: true
        };
        
        // Función para obtener fecha límite según período
        function getDateLimit(period) {
            const now = new Date();
            switch(period) {
                case 'week':
                    return new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                case 'month':
                    return new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                case '3months':
                    return new Date(now.getTime() - 90 * 24 * 60 * 60 * 1000);
                default:
                    return null;
            }
        }
        
        // Función para contar elementos en un período
        function countInPeriod(detailedData, period) {
            if (period === 'all') return detailedData.length;
            const limit = getDateLimit(period);
            if (!limit) return detailedData.length;
            return detailedData.filter(date => new Date(date) >= limit).length;
        }
        
        // Función para actualizar el gráfico
        function updateCompareChart() {
            const period = document.getElementById('filterPeriodCompare')?.value || 'all';
            
            // Calcular totales filtrados
            const filteredTotals = {
                hallazgos: countInPeriod(reportsDetailed, period),
                traslados: countInPeriod(transfersDetailed, period),
                liberaciones: countInPeriod(releasesDetailed, period),
                hojas: countInPeriod(animalFilesDetailed, period)
            };
            
            // Preparar datos según series visibles
            const labels = [];
            const values = [];
            const colors = [];
            const borderColors = [];
            
            const seriesConfig = [
                { key: 'hallazgos', label: 'Hallazgos', color: '#e74c3c' },
                { key: 'traslados', label: 'Traslados', color: '#3498db' },
                { key: 'liberaciones', label: 'Liberaciones', color: '#2ecc71' },
                { key: 'hojas', label: 'Hojas de vida', color: '#8e44ad' }
            ];
            
            seriesConfig.forEach(series => {
                if (visibleSeries[series.key]) {
                    labels.push(series.label);
                    values.push(filteredTotals[series.key]);
                    colors.push(series.color);
                    borderColors.push(series.color);
                }
            });
            
            if (values.length === 0) {
                if (compareChart) {
                    compareChart.destroy();
                }
                cmp.outerHTML = '<div class="text-center text-muted p-4">Seleccione al menos una serie para mostrar</div>';
                return;
            }
            
            if (compareChart) {
                compareChart.data.labels = labels;
                compareChart.data.datasets[0].data = values;
                compareChart.data.datasets[0].backgroundColor = colors;
                compareChart.data.datasets[0].borderColor = borderColors;
                compareChart.update();
            } else {
                compareChart = new Chart(cmp, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Totales',
                            data: values,
                            backgroundColor: colors,
                            borderColor: borderColors,
                            borderWidth: 1,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Comparativa de Totales' }
                        },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
        }
        
        // Event listeners para filtros
        const periodFilter = document.getElementById('filterPeriodCompare');
        if (periodFilter) {
            periodFilter.addEventListener('change', updateCompareChart);
        }
        
        document.querySelectorAll('.series-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const series = this.getAttribute('data-series');
                visibleSeries[series] = this.checked;
                updateCompareChart();
            });
        });
        
        // Inicializar gráfico
        updateCompareChart();
    }
    @endif

    // 2. Gráfico de Animales por Estado (Doughnut) con Filtros
    @if(isset($animalsByStatus) && !empty($animalsByStatus))
    let animalsChart = null;
    const animalsCtx = document.getElementById('animalsStatusChart');
    if (animalsCtx) {
        const originalAnimalsData = @json($animalsByStatus);
        const animalFilesDetailed = @json($animalFilesDetailed ?? []);
        
        // Estado de estados visibles
        const visibleStatuses = {};
        Object.keys(originalAnimalsData).forEach(status => {
            visibleStatuses[status] = true;
        });
        
        // Colores para cada estado
        const statusColors = [
            '#10b981', // Emerald
            '#3b82f6', // Blue
            '#f59e0b', // Amber
            '#ef4444', // Red
            '#8b5cf6', // Violet
            '#6b7280', // Gray
            '#ec4899', // Pink
        ];
        
        // Función para obtener fecha límite según período
        function getDateLimitStatus(period) {
            const now = new Date();
            switch(period) {
                case 'week':
                    return new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                case 'month':
                    return new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                default:
                    return null;
            }
        }
        
        // Función para contar animales por estado en un período
        // Nota: Como no tenemos la relación directa estado-fecha, usamos los datos originales
        // y aplicamos un factor de proporción basado en el período
        function getFilteredAnimalsData(period) {
            if (period === 'all') {
                return originalAnimalsData;
            }
            
            // Para períodos específicos, necesitaríamos datos más detallados
            // Por ahora, retornamos los datos originales
            // En una implementación completa, se haría una consulta adicional
            return originalAnimalsData;
        }
        
        // Función para actualizar el gráfico
        function updateAnimalsChart() {
            const period = document.getElementById('filterPeriodStatus')?.value || 'all';
            const filteredData = getFilteredAnimalsData(period);
            
            // Filtrar por estados visibles
            const labels = [];
            const values = [];
            const colors = [];
            let colorIndex = 0;
            
            Object.keys(filteredData).forEach(status => {
                if (visibleStatuses[status]) {
                    labels.push(status);
                    values.push(filteredData[status]);
                    colors.push(statusColors[colorIndex % statusColors.length]);
                    colorIndex++;
                }
            });
            
            if (values.length === 0) {
                if (animalsChart) {
                    animalsChart.destroy();
                }
                animalsCtx.outerHTML = '<div class="text-center text-muted p-4">Seleccione al menos un estado para mostrar</div>';
                return;
            }
            
            if (animalsChart) {
                animalsChart.data.labels = labels;
                animalsChart.data.datasets[0].data = values;
                animalsChart.data.datasets[0].backgroundColor = colors;
                animalsChart.update();
            } else {
                animalsChart = new Chart(animalsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            }
        }
        
        // Event listeners para filtros
        const periodFilterStatus = document.getElementById('filterPeriodStatus');
        if (periodFilterStatus) {
            periodFilterStatus.addEventListener('change', updateAnimalsChart);
        }
        
        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const status = this.getAttribute('data-status');
                visibleStatuses[status] = this.checked;
                updateAnimalsChart();
            });
        });
        
        // Inicializar gráfico
        updateAnimalsChart();
    }
    @endif

    // 3. Gráfico de Solicitudes (Barras Multicolor Horizontales)
    @if(isset($applicationsByType) && !empty($applicationsByType))
    const applicationsCtx = document.getElementById('applicationsChart');
    if (applicationsCtx) {
        const applicationsData = @json($applicationsByType);
        
        new Chart(applicationsCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(applicationsData),
                datasets: [{
                    label: 'Solicitudes',
                    data: Object.values(applicationsData),
                    // Array de colores diferentes para cada barra
                    backgroundColor: [
                        '#4f46e5', // Indigo
                        '#06b6d4', // Cyan
                        '#f97316', // Orange
                        '#ec4899', // Pink
                        '#10b981', // Emerald
                        '#8b5cf6'  // Violet
                    ],
                    borderRadius: 4,
                    barThickness: 25,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Mantiene la orientación horizontal
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + Math.round(context.parsed.x);
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        beginAtZero: true, 
                        grid: { display: false },
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            callback: function(value) {
                                return Math.round(value);
                            }
                        }
                    },
                    y: { grid: { display: false, drawBorder: false } }
                }
            }
        });
    }
    @endif

    // Reinicializar gráficos cuando se cambia de pestaña
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('#dashboardTabs a[data-toggle="tab"]');
        tabs.forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function(e) {
                // Forzar actualización de gráficos cuando se muestra una pestaña
                if (typeof Chart !== 'undefined') {
                    Chart.helpers.each(Chart.instances, function(instance) {
                        instance.resize();
                    });
                }
            });
        });
    });
});
</script>

@endif
@endsection