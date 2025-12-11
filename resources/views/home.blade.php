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
            <a href="{{ route('dashboard.export-csv') }}" class="btn btn-success btn-sm mr-2" target="_blank">
                <i class="fas fa-file-csv mr-1"></i> Exportar CSV
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

    /* Estilos del Mapa de Campo */
    .custom-marker {
        background: transparent;
        border: none;
    }
    .prediction-marker {
        background: transparent;
        border: none;
    }
    .prediction-marker div {
        animation: pulse-prediction 2s infinite;
    }
    @keyframes pulse-prediction {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.8;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    .weather-panel {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        transition: all 0.3s ease;
    }
    .weather-panel button:hover {
        background: rgba(255,255,255,0.3) !important;
    }
    @media (max-width: 768px) {
        .weather-panel {
            max-width: 200px;
            min-width: 180px;
        }
    }
    .map-controls {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .map-controls .form-check-inline {
        display: block;
        margin-right: 0;
    }
    .map-controls .form-check-label {
        cursor: pointer;
        user-select: none;
    }
    .map-controls .form-check-label:hover {
        color: #007bff;
    }
    .map-legend {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    @media (max-width: 768px) {
        .map-controls {
            max-width: 240px;
            padding: 8px;
            font-size: 10px;
        }
        .map-controls .form-control-sm {
            font-size: 11px;
        }
        .map-legend {
            max-width: 180px;
            font-size: 10px;
            padding: 6px;
        }
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
                        <a class="nav-link" id="mapa-tab" data-toggle="tab" href="#mapa" role="tab" aria-controls="mapa" aria-selected="false">
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
                    
                    {{-- PESTAÑA 3: MAPA DE CAMPO --}}
                    <div class="tab-pane fade" id="mapa" role="tabpanel" aria-labelledby="mapa-tab">
                        {{-- Mapa de Campo Completo (Ancho Completo) --}}
                        @if(isset($reports) && isset($releases))
                        <div class="row">
                            <div class="col-12">
                <div class="card shadow-sm no-export-when-collapsed">
                    
                    <div class="card-body bg-white" style="padding-top: 0.75rem;">
                        <!-- Leyenda de urgencia fuera del mapa -->
                        <div class="mb-3">
                            <div class="mb-1">
                                <strong><i class="fas fa-exclamation-triangle"></i> Hallazgos por Urgencia:</strong>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-danger mr-2" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fas fa-exclamation-circle"></i> Alta (4-5)
                                </span>
                                <span class="badge badge-warning mr-2" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fas fa-exclamation-triangle"></i> Media (3)
                                </span>
                                <span class="badge badge-info mr-2" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fas fa-info-circle"></i> Baja (1-2)
                                </span>
                            </div>
                        </div>

                        <!-- Contenedor del mapa con posición relativa para controles flotantes -->
                        <div style="position: relative; width: 100%; min-height: 500px;">
                            <div id="mapaCampo" style="height: 500px; width: 100%; border-radius: 6px; border: 1px solid #dee2e6; background-color: #f0f0f0;"></div>
                            
                            <!-- Controles flotantes dentro del mapa -->
                            <div class="map-controls" style="position: absolute; top: 10px; left: 10px; z-index: 1000; background: white; padding: 10px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); max-width: 280px;">
                                <!-- Filtro de estado de hallazgos -->
                                <div class="mb-2">
                                    <label for="filterReportStatus" class="form-label" style="font-size: 12px; font-weight: bold; margin-bottom: 4px;">
                                        <i class="fas fa-filter"></i> Estado Hallazgos
                                    </label>
                                    <select class="form-control form-control-sm" id="filterReportStatus" style="font-size: 12px;">
                                        <option value="all">Hallazgos aprobados</option>
                                        <option value="with_file">Tienen hoja de animal</option>
                                        <option value="without_file">Pendientes</option>
                                    </select>
                                </div>
                                
                                <!-- Filtro por especie -->
                                <div class="mb-2">
                                    <label for="filterSpecies" class="form-label" style="font-size: 12px; font-weight: bold; margin-bottom: 4px;">
                                        <i class="fas fa-filter"></i> Especie
                                    </label>
                                    <select class="form-control form-control-sm" id="filterSpecies" style="font-size: 12px;">
                                        <option value="">Todas</option>
                                        @foreach($species ?? [] as $specie)
                                            <option value="{{ $specie->id }}">{{ $specie->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Toggles compactos -->
                                <div style="font-size: 11px;">
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="toggleReports" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="toggleReports" style="font-size: 11px;">
                                            <i class="fas fa-clipboard-list"></i> Hallazgos Aprobados
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="toggleReleases" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="toggleReleases" style="font-size: 11px;">
                                            <i class="fas fa-dove"></i> Animales Liberados
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="toggleFocosCalor" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="toggleFocosCalor" style="font-size: 11px;">
                                            <i class="fas fa-satellite"></i> Focos
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="togglePredictions" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="togglePredictions" style="font-size: 11px;">
                                            <i class="fas fa-fire"></i> Predicciones
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Panel de clima en la esquina superior derecha -->
                            <div id="weatherPanel" class="weather-panel" style="position: absolute; top: 10px; right: 10px; z-index: 1000; background: white; padding: 0; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); max-width: 200px; min-width: 170px; display: none;">
                                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 8px; border-radius: 6px 6px 0 0;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                        <h6 style="margin: 0; font-weight: bold; color: white; font-size: 12px;">
                                            <i class="fas fa-cloud-sun"></i> Clima
                                        </h6>
                                        <button id="closeWeatherPanel" style="background: rgba(255,255,255,0.2); border: none; color: white; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div id="weatherLoading" style="text-align: center; color: white; padding: 12px;">
                                        <i class="fas fa-spinner fa-spin" style="font-size: 18px; margin-bottom: 6px;"></i>
                                        <div style="font-size: 10px;">Cargando...</div>
                                    </div>
                                    <div id="weatherContent" style="display: none;">
                                        <div style="text-align: center; font-size: 24px; font-weight: bold; color: white; margin-bottom: 2px;">
                                            <span id="weatherTemp">--</span><span style="font-size: 16px;">°C</span>
                                        </div>
                                        <div style="text-align: center; font-size: 10px; opacity: 0.9; color: white;">
                                            <span id="weatherDesc">--</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="weatherDetails" style="padding: 8px; display: none;">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; font-size: 10px; margin-bottom: 8px;">
                                        <div style="padding: 6px; background-color: #f8f9fa; border-radius: 4px;">
                                            <div style="color: #6c757d; margin-bottom: 2px; font-size: 9px;">
                                                <i class="fas fa-tint"></i> Humedad
                                            </div>
                                            <div style="font-weight: bold; font-size: 12px;">
                                                <span id="weatherHumidity">--</span>%
                                            </div>
                                        </div>
                                        <div style="padding: 6px; background-color: #f8f9fa; border-radius: 4px;">
                                            <div style="color: #6c757d; margin-bottom: 2px; font-size: 9px;">
                                                <i class="fas fa-wind"></i> Viento
                                            </div>
                                            <div style="font-weight: bold; font-size: 12px;">
                                                <span id="weatherWindSpeed">--</span> km/h
                                            </div>
                                        </div>
                                        <div style="padding: 6px; background-color: #f8f9fa; border-radius: 4px;">
                                            <div style="color: #6c757d; margin-bottom: 2px; font-size: 9px;">
                                                <i class="fas fa-compass"></i> Dirección
                                            </div>
                                            <div style="font-weight: bold; font-size: 12px;">
                                                <span id="weatherWindDir">--</span>
                                            </div>
                                        </div>
                                        <div style="padding: 6px; background-color: #f8f9fa; border-radius: 4px;" hidden>
                                            <div style="color: #6c757d; margin-bottom: 2px; font-size: 9px;">
                                                <i class="fas fa-cloud-rain"></i> Precip.
                                            </div>
                                            <div style="font-weight: bold; font-size: 12px;">
                                                <span id="weatherPrecip">--</span> mm
                                            </div>
                                        </div>
                                    </div>
                                    <div style="padding: 6px; background-color: #e7f3ff; border-radius: 4px; font-size: 9px; color: #0066cc; margin-bottom: 6px;" hidden>
                                        <i class="fas fa-map-marker-alt"></i> 
                                        <span id="weatherCoords">--</span>
                                    </div>
                                    <div style="text-align: center; font-size: 9px; color: #6c757d;">
                                        <i class="fas fa-info-circle"></i> OpenMeteo
                                    </div>
                                </div>
                                <div id="weatherError" style="padding: 8px; display: none; text-align: center;">
                                    <div style="color: #dc3545; margin-bottom: 6px;">
                                        <i class="fas fa-exclamation-triangle" style="font-size: 18px;"></i>
                                    </div>
                                    <div style="font-size: 10px; color: #6c757d;">
                                        No se pudieron obtener los datos.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Leyenda flotante en la esquina inferior derecha -->
                            <div class="map-legend" style="position: absolute; bottom: 10px; right: 10px; z-index: 1000; background: white; padding: 8px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); max-width: 240px; font-size: 11px;">
                                <div style="margin-bottom: 4px;">
                                    <strong>Hallazgos:</strong>
                                    <div style="margin-top: 2px;">
                                        <span style="display: inline-block; width: 14px; height: 14px; background-color: #dc3545; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                        <span style="margin-left: 4px;">Alta</span>
                                        <span style="display: inline-block; width: 14px; height: 14px; background-color: #ffc107; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle; margin-left: 8px;"></span>
                                        <span style="margin-left: 4px;">Media</span>
                                        <span style="display: inline-block; width: 14px; height: 14px; background-color: #17a2b8; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle; margin-left: 8px;"></span>
                                        <span style="margin-left: 4px;">Baja</span>
                                    </div>
                                    <div style="margin-top: 4px; font-size: 10px;">
                                        <span style="display: inline-block; width: 14px; height: 14px; background-color: #dc3545; border: 3px solid #28a745; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle; position: relative;">
                                            <i class="fas fa-check" style="position: absolute; bottom: -3px; right: -3px; color: #28a745; background: white; border-radius: 50%; font-size: 8px; width: 10px; height: 10px; display: flex; align-items: center; justify-content: center;"></i>
                                        </span>
                                        <span style="margin-left: 4px;">Con hoja de vida</span>
                                    </div>
                                </div>
                                <div style="margin-bottom: 4px;">
                                    <span style="display: inline-block; width: 14px; height: 14px; background-color: #007bff; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                    <span style="margin-left: 4px;">Animal Liberado</span>
                                </div>
                                <div style="margin-bottom: 4px;">
                                    <span style="display: inline-block; width: 10px; height: 10px; background-color: #ff0000; border: 1px solid #fff; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                    <span style="margin-left: 4px;">Foco de Calor</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                        </div>
                        @endif
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
                        <p class="lead text-muted">Bienvenido al Sistema de Rescate y Gestión de Fauna.</p>
                        
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

    // Inicializar mapa de campo completo
    @if(Auth::user()->hasAnyRole(['admin', 'encargado']) && isset($reports) && isset($releases))
    (function() {
        let map = null;
        let markers = [];
        let releaseMarkers = [];
        let predictionLayers = [];
        let focosCalorMarkers = [];
        let loadedPredictions = new Set();
        let showReports = true;
        let showReleases = true;
        let showPredictions = true;
        let showFocosCalor = true;
        let selectedSpeciesId = null;
        let reportStatusFilter = 'all';
        let weatherRequestInProgress = false;

        const reportsData = @json($reports ?? []);
        const releasesData = @json($releases ?? []);
        const focosCalorData = @json($focosCalorFormatted ?? []);

        function initMap() {
            if (typeof L === 'undefined') {
                console.log('[Mapa] Esperando a que Leaflet se cargue...');
                setTimeout(initMap, 100);
                return;
            }

            const mapEl = document.getElementById('mapaCampo');
            if (!mapEl) {
                console.error('[Mapa] No se encontró el elemento #mapaCampo');
                return;
            }

            if (mapEl.offsetWidth === 0 || mapEl.offsetHeight === 0) {
                console.warn('[Mapa] El contenedor del mapa no tiene dimensiones, reintentando...');
                setTimeout(initMap, 200);
                return;
            }

            console.log('[Mapa] Inicializando mapa...');
            
            try {
                map = L.map('mapaCampo').setView([-17.7833, -63.1821], 11);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
                    maxZoom: 19 
                }).addTo(map);
                
                console.log('[Mapa] Mapa inicializado correctamente');
            } catch (error) {
                console.error('[Mapa] Error al inicializar el mapa:', error);
                return;
            }

            addReportsMarkers();
            addReleaseMarkers();
            addFocosCalorMarkers();

            const toggleReports = document.getElementById('toggleReports');
            if (toggleReports) {
                toggleReports.addEventListener('change', function() {
                    showReports = this.checked;
                    updateReportsMarkers();
                });
            }

            const toggleReleases = document.getElementById('toggleReleases');
            if (toggleReleases) {
                toggleReleases.addEventListener('change', function() {
                    showReleases = this.checked;
                    updateReleaseMarkers();
                });
            }

            const togglePredictions = document.getElementById('togglePredictions');
            if (togglePredictions) {
                togglePredictions.addEventListener('change', function() {
                    showPredictions = this.checked;
                    updatePredictionLayers();
                });
            }

            const toggleFocosCalor = document.getElementById('toggleFocosCalor');
            if (toggleFocosCalor) {
                toggleFocosCalor.addEventListener('change', function() {
                    showFocosCalor = this.checked;
                    updateFocosCalorMarkers();
                });
            }

            const filterReportStatus = document.getElementById('filterReportStatus');
            if (filterReportStatus) {
                filterReportStatus.addEventListener('change', function() {
                    reportStatusFilter = this.value;
                    updateReportsMarkers();
                });
            }

            const filterSpecies = document.getElementById('filterSpecies');
            if (filterSpecies) {
                filterSpecies.addEventListener('change', function() {
                    selectedSpeciesId = this.value ? parseInt(this.value) : null;
                    updateReleaseMarkers();
                });
            }

            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                console.log('[Clima] Obteniendo datos meteorológicos para:', lat, lng);
                getWeatherData(lat, lng);
            });
        }

        function getWeatherData(latitude, longitude) {
            if (weatherRequestInProgress) {
                console.log('[Clima] Ya hay una petición en curso, omitiendo...');
                return;
            }

            const weatherPanel = document.getElementById('weatherPanel');
            const weatherLoading = document.getElementById('weatherLoading');
            const weatherContent = document.getElementById('weatherContent');
            const weatherDetails = document.getElementById('weatherDetails');
            const weatherError = document.getElementById('weatherError');

            if (!weatherPanel) return;

            weatherPanel.style.display = 'block';
            weatherLoading.style.display = 'block';
            weatherContent.style.display = 'none';
            weatherDetails.style.display = 'none';
            weatherError.style.display = 'none';

            weatherRequestInProgress = true;

            fetch(`/api/weather?latitude=${latitude}&longitude=${longitude}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('[Clima] Datos recibidos:', data);
                    
                    weatherLoading.style.display = 'none';
                    weatherContent.style.display = 'block';
                    weatherDetails.style.display = 'block';
                    weatherError.style.display = 'none';

                    const weatherDescription = getWeatherDescription(data.weatherCode);
                    const windDirectionName = getWindDirectionName(data.windDirection);

                    document.getElementById('weatherTemp').textContent = data.temperature;
                    document.getElementById('weatherDesc').textContent = weatherDescription;
                    document.getElementById('weatherHumidity').textContent = data.humidity;
                    document.getElementById('weatherWindSpeed').textContent = data.windSpeed;
                    document.getElementById('weatherWindDir').textContent = windDirectionName;
                    document.getElementById('weatherPrecip').textContent = data.precipitation;
                    document.getElementById('weatherCoords').textContent = `Lat: ${latitude.toFixed(6)}, Lng: ${longitude.toFixed(6)}`;
                    
                    weatherRequestInProgress = false;
                })
                .catch(error => {
                    console.error('[Clima] Error al obtener datos meteorológicos:', error);
                    
                    weatherLoading.style.display = 'none';
                    weatherContent.style.display = 'none';
                    weatherDetails.style.display = 'none';
                    weatherError.style.display = 'block';
                    
                    weatherRequestInProgress = false;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const closeWeatherPanel = document.getElementById('closeWeatherPanel');
            if (closeWeatherPanel) {
                closeWeatherPanel.addEventListener('click', function() {
                    const weatherPanel = document.getElementById('weatherPanel');
                    if (weatherPanel) {
                        weatherPanel.style.display = 'none';
                    }
                });
            }
        });

        function getWeatherDescription(code) {
            const descriptions = {
                0: 'Despejado', 1: 'Mayormente despejado', 2: 'Parcialmente nublado', 3: 'Nublado',
                45: 'Niebla', 48: 'Niebla depositada', 51: 'Llovizna ligera', 53: 'Llovizna moderada',
                55: 'Llovizna densa', 56: 'Llovizna helada ligera', 57: 'Llovizna helada densa',
                61: 'Lluvia ligera', 63: 'Lluvia moderada', 65: 'Lluvia intensa',
                66: 'Lluvia helada ligera', 67: 'Lluvia helada intensa', 71: 'Nieve ligera',
                73: 'Nieve moderada', 75: 'Nieve intensa', 77: 'Granizo',
                80: 'Chubascos ligeros', 81: 'Chubascos moderados', 82: 'Chubascos intensos',
                85: 'Nevadas ligeras', 86: 'Nevadas intensas', 95: 'Tormenta',
                96: 'Tormenta con granizo', 99: 'Tormenta intensa con granizo',
            };
            return descriptions[code] || 'Desconocido';
        }

        function getWindDirectionName(degrees) {
            const directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
            const index = Math.round(degrees / 22.5) % 16;
            return directions[index] || 'N';
        }
        
        function addFocosCalorMarkers() {
            if (!map) return;
            
            if (!focosCalorData || focosCalorData.length === 0) {
                console.log('[Focos Calor] No hay datos de NASA FIRMS para mostrar');
                return;
            }
            
            console.log(`[Focos Calor] Agregando ${focosCalorData.length} focos de calor al mapa`);
            
            focosCalorData.forEach(function(foco) {
                if (!foco.lat || !foco.lng) return;
                
                const point = L.circleMarker([foco.lat, foco.lng], {
                    radius: 6,
                    fillColor: '#ff0000',
                    color: '#ffffff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.9
                });
                
                if (showFocosCalor) {
                    point.addTo(map);
                }
                
                const confidence = foco.confidence || 0;
                const popupContent = `
                    <div style="min-width: 200px;">
                        <h6 style="margin: 0 0 8px 0; font-weight: bold;">
                            <i class="fas fa-satellite"></i> Foco de Calor
                        </h6>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Confianza:</strong> 
                            <span class="badge badge-${confidence >= 70 ? 'danger' : (confidence >= 30 ? 'warning' : 'secondary')}">
                                ${confidence}%
                            </span>
                        </div>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Fecha:</strong> ${foco.date}
                        </div>
                        <div style="font-size: 11px; color: #6c757d; margin-top: 8px;">
                            <i class="fas fa-info-circle"></i> Datos de NASA FIRMS
                        </div>
                    </div>
                `;
                
                point.bindPopup(popupContent);
                
                point.on('click', function(e) {
                    if (e.originalEvent) {
                        e.originalEvent.stopPropagation();
                    }
                    getWeatherData(foco.lat, foco.lng);
                });
                
                focosCalorMarkers.push(point);
            });
        }

        function updateFocosCalorMarkers() {
            focosCalorMarkers.forEach(function(marker) {
                if (showFocosCalor) {
                    if (!map.hasLayer(marker)) {
                        marker.addTo(map);
                    }
                } else {
                    if (map.hasLayer(marker)) {
                        map.removeLayer(marker);
                    }
                }
            });
        }

        function addReleaseMarkers() {
            if (!map) return;

            console.log(`[Liberaciones] Agregando ${releasesData.length} liberaciones al mapa`);

            releasesData.forEach(function(release) {
                if (!release.latitud || !release.longitud) return;

                const lat = parseFloat(release.latitud);
                const lng = parseFloat(release.longitud);
                
                if (isNaN(lat) || isNaN(lng)) return;

                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: #007bff; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-dove" style="color: white; font-size: 12px;"></i>
                    </div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
                });

                const marker = L.marker([lat, lng], { icon: icon });
                
                if (showReleases && (!selectedSpeciesId || release.especie_id == selectedSpeciesId)) {
                    marker.addTo(map);
                }

                const especieNombre = release.especie && release.especie.nombre 
                    ? release.especie.nombre 
                    : 'Especie no identificada';
                const animalNombre = release.animal && release.animal.nombre 
                    ? release.animal.nombre 
                    : 'Animal #' + (release.animal ? release.animal.id : 'N/A');
                
                let popupContent = `
                    <div style="min-width: 200px;">
                        <h6 style="margin: 0 0 8px 0; font-weight: bold;">
                            <i class="fas fa-dove"></i> Liberación
                        </h6>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Animal:</strong> ${animalNombre}
                        </div>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Especie:</strong> ${especieNombre}
                        </div>
                        ${release.fecha ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <strong>Fecha:</strong> ${release.fecha}
                            </div>
                        ` : ''}
                        ${release.direccion ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <i class="fas fa-map-pin"></i> ${release.direccion}
                            </div>
                        ` : ''}
                        ${release.detalle ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <strong>Detalle:</strong> ${release.detalle}
                            </div>
                        ` : ''}
                    </div>
                `;

                marker.bindPopup(popupContent);

                marker.on('click', function(e) {
                    if (e.originalEvent) {
                        e.originalEvent.stopPropagation();
                    }
                    getWeatherData(lat, lng);
                });

                releaseMarkers.push({
                    release: release,
                    marker: marker
                });
            });
        }

        function updateReleaseMarkers() {
            releaseMarkers.forEach(function(item) {
                const release = item.release;
                const marker = item.marker;
                
                const shouldShow = showReleases && (!selectedSpeciesId || release.especie_id == selectedSpeciesId);
                
                if (shouldShow) {
                    if (!map.hasLayer(marker)) {
                        marker.addTo(map);
                    }
                } else {
                    if (map.hasLayer(marker)) {
                        map.removeLayer(marker);
                    }
                }
            });
        }

        function updateReportsMarkers() {
            markers.forEach(function(item) {
                const report = item.report;
                const marker = item.marker;
                
                let shouldShow = showReports;
                if (shouldShow && reportStatusFilter !== 'all') {
                    if (reportStatusFilter === 'with_file' && !report.tiene_hoja_vida) {
                        shouldShow = false;
                    } else if (reportStatusFilter === 'without_file' && report.tiene_hoja_vida) {
                        shouldShow = false;
                    }
                }
                
                if (shouldShow) {
                    if (!map.hasLayer(marker)) {
                        marker.addTo(map);
                    }
                } else {
                    if (map.hasLayer(marker)) {
                        map.removeLayer(marker);
                    }
                }
            });
        }

        function addReportsMarkers() {
            if (!map) {
                console.error('[Reportes] El mapa no está inicializado');
                return;
            }

            if (!reportsData || !Array.isArray(reportsData)) {
                console.error('[Reportes] No hay datos de reportes o no es un array');
                return;
            }

            console.log(`[Reportes] Agregando ${reportsData.length} reportes al mapa`);

            reportsData.forEach(function(report) {
                if (!report.latitud || !report.longitud) return;

                const lat = parseFloat(report.latitud);
                const lng = parseFloat(report.longitud);
                
                if (isNaN(lat) || isNaN(lng)) return;

                const urgencia = report.urgencia;
                let color = '#6c757d';
                let iconClass = 'fa-map-marker-alt';

                if (urgencia !== null && urgencia !== undefined) {
                    if (urgencia >= 4) {
                        color = '#dc3545';
                    } else if (urgencia === 3) {
                        color = '#ffc107';
                    } else if (urgencia <= 2) {
                        color = '#17a2b8';
                    }
                }

                if (report.incendio_id) {
                    color = '#ff4444';
                    iconClass = 'fa-fire';
                }
                
                const isSimulado = report.id === 'simulado';
                const tieneHojaVida = report.tiene_hoja_vida || false;
                const borderColor = tieneHojaVida ? '#28a745' : 'white';
                const borderWidth = tieneHojaVida ? 4 : 3;
                const iconSize = tieneHojaVida ? 30 : 28;
                
                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: ${color}; width: ${iconSize}px; height: ${iconSize}px; border-radius: 50%; border: ${borderWidth}px solid ${borderColor}; box-shadow: 0 2px 6px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; position: relative;">
                        <i class="fas ${iconClass}" style="color: white; font-size: 14px;"></i>
                        ${tieneHojaVida ? '<i class="fas fa-check-circle" style="position: absolute; bottom: -2px; right: -2px; color: #28a745; background: white; border-radius: 50%; font-size: 10px; width: 14px; height: 14px; display: flex; align-items: center; justify-content: center;"></i>' : ''}
                    </div>`,
                    iconSize: [iconSize, iconSize],
                    iconAnchor: [iconSize/2, iconSize/2],
                });

                const marker = L.marker([lat, lng], { icon: icon });
                
                if (showReports) {
                    marker.addTo(map);
                }

                const condicionNombre = report.condicion_inicial && report.condicion_inicial.nombre 
                    ? report.condicion_inicial.nombre 
                    : 'Hallazgo';
                const incidenteNombre = report.incident_type && report.incident_type.nombre 
                    ? report.incident_type.nombre 
                    : null;
                
                let popupContent = `
                    <div style="min-width: 200px;">
                        <div style="margin-bottom: 8px; padding: 8px; background-color: ${color}; border-radius: 4px; text-align: center;">
                            <div style="color: white; font-size: 24px; font-weight: bold; margin-bottom: 2px;">
                                ${urgencia !== null && urgencia !== undefined ? urgencia : 'N/A'}
                            </div>
                            <div style="color: white; font-size: 11px; text-transform: uppercase;">
                                ${urgencia >= 4 ? 'Alta Urgencia' : (urgencia === 3 ? 'Media Urgencia' : (urgencia <= 2 ? 'Baja Urgencia' : 'Sin Urgencia'))}
                            </div>
                        </div>
                        ${tieneHojaVida ? `
                            <div style="margin-bottom: 8px; padding: 6px; background-color: #28a745; border-radius: 4px; text-align: center;">
                                <i class="fas fa-check-circle" style="color: white; margin-right: 4px;"></i>
                                <span style="color: white; font-size: 12px; font-weight: bold;">Ya tiene hoja de vida</span>
                            </div>
                        ` : `
                            <div style="margin-bottom: 8px; padding: 6px; background-color: #ffc107; border-radius: 4px; text-align: center;">
                                <i class="fas fa-clock" style="color: white; margin-right: 4px;"></i>
                                <span style="color: white; font-size: 12px; font-weight: bold;">Pendiente de rescate</span>
                            </div>
                        `}
                        <h6 style="margin: 0 0 8px 0; font-weight: bold;">
                            <i class="fas fa-clipboard-list"></i> ${condicionNombre}
                        </h6>
                        ${report.incendio_id ? `
                            <div style="font-size: 12px; margin-bottom: 4px; color: #ff4444;">
                                <i class="fas fa-fire"></i> <strong>Hallazgo en incendio</strong>
                            </div>
                        ` : ''}
                        ${incidenteNombre ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <strong>Incidente:</strong> ${incidenteNombre}
                            </div>
                        ` : ''}
                        ${report.direccion ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <i class="fas fa-map-pin"></i> ${report.direccion}
                            </div>
                        ` : ''}
                        <div style="font-size: 11px; color: #6c757d; margin-top: 8px;">
                            ${isSimulado ? `
                                <span class="badge badge-warning">
                                    <i class="fas fa-flask"></i> Simulación de demostración
                                </span>
                            ` : `
                                <a href="${window.location.origin}/reports/${report.id}" class="btn btn-sm btn-primary" target="_blank" style="color: white;">
                                    <i class="fas fa-eye"></i> Ver detalles
                                </a>
                            `}
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);

                marker.on('click', function(e) {
                    if (e.originalEvent) {
                        e.originalEvent.stopPropagation();
                    }
                    getWeatherData(lat, lng);
                });

                markers.push({
                    report: report,
                    marker: marker
                });
            });

            if (showPredictions) {
                reportsData.forEach(function(report) {
                    if (report.incendio_id) {
                        console.log(`[Predicciones] Cargando predicción para reporte ${report.id} (incendio_id: ${report.incendio_id})`);
                        loadFirePrediction(report.incendio_id);
                    }
                });
            }

            const allVisibleMarkers = [
                ...markers.filter(m => {
                    if (!showReports) return false;
                    if (reportStatusFilter === 'all') return true;
                    if (reportStatusFilter === 'with_file' && m.report.tiene_hoja_vida) return true;
                    if (reportStatusFilter === 'without_file' && !m.report.tiene_hoja_vida) return true;
                    return false;
                }).map(m => m.marker),
                ...releaseMarkers.filter(m => showReleases && (!selectedSpeciesId || m.release.especie_id == selectedSpeciesId)).map(m => m.marker)
            ];
            
            if (allVisibleMarkers.length > 0) {
                const group = new L.featureGroup(allVisibleMarkers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        function loadFirePrediction(focoIncendioId) {
            if (loadedPredictions.has(focoIncendioId)) {
                console.log(`[Predicciones] Predicción ${focoIncendioId} ya cargada, omitiendo`);
                return;
            }
            
            loadedPredictions.add(focoIncendioId);
            console.log(`[Predicciones] Solicitando predicción para foco_incendio_id: ${focoIncendioId}`);
            
            fetch(`/api/fire-predictions?foco_incendio_id=${focoIncendioId}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.data && data.data.length > 0) {
                        const prediction = data.data[0];
                        console.log(`[Predicciones] Predicción recibida, dibujando en mapa`);
                        drawFirePrediction(prediction);
                    } else {
                        console.warn(`[Predicciones] No se encontraron datos de predicción para foco_incendio_id: ${focoIncendioId}`);
                        loadedPredictions.delete(focoIncendioId);
                    }
                })
                .catch(error => {
                    console.error(`[Predicciones] Error al cargar predicción para foco_incendio_id ${focoIncendioId}:`, error);
                    loadedPredictions.delete(focoIncendioId);
                });
        }

        function drawFirePrediction(prediction) {
            if (!map || !prediction.path || !Array.isArray(prediction.path)) return;

            const path = prediction.path;
            const circles = [];
            let polyline = null;
            
            const focoIncendioId = prediction.foco_incendio_id || prediction.id || null;

            if (path.length > 1 && showPredictions) {
                const latlngs = path
                    .filter(p => p.lat && p.lng)
                    .map(p => [p.lat, p.lng]);

                if (latlngs.length > 1) {
                    polyline = L.polyline(latlngs, {
                        color: '#ff0000',
                        weight: 4,
                        opacity: 0.8,
                        dashArray: '10, 5',
                        zIndexOffset: -100,
                        focoIncendioId: focoIncendioId
                    });
                    
                    if (showPredictions) {
                        polyline.addTo(map);
                    }
                    predictionLayers.push(polyline);
                }
            }

            path.forEach(function(point, index) {
                if (!point.lat || !point.lng) return;

                const radius = (point.spread_radius_km || 0) * 1000;
                const intensity = point.intensity || 0;

                let color = '#ff8800';
                let borderColor = '#ff6600';
                let opacity = 0.4;
                let borderWidth = 3;
                
                if (intensity >= 7) {
                    color = '#ff0000';
                    borderColor = '#cc0000';
                    opacity = 0.6;
                    borderWidth = 4;
                } else if (intensity >= 5) {
                    color = '#ff4400';
                    borderColor = '#ff2200';
                    opacity = 0.5;
                    borderWidth = 3;
                } else if (intensity >= 3) {
                    color = '#ff8800';
                    borderColor = '#ff6600';
                    opacity = 0.4;
                    borderWidth = 2;
                }

                const circle = L.circle([point.lat, point.lng], {
                    radius: radius,
                    color: borderColor,
                    fillColor: color,
                    fillOpacity: opacity,
                    weight: borderWidth,
                    zIndexOffset: 100,
                    focoIncendioId: focoIncendioId
                });

                if (showPredictions) {
                    circle.addTo(map);
                }

                circles.push(circle);

                const popupContent = `
                    <div style="min-width: 180px;">
                        <h6 style="margin: 0 0 8px 0;">
                            <i class="fas fa-fire"></i> Predicción - Hora ${point.hour}
                        </h6>
                        <div style="font-size: 12px;">
                            <div><strong>Intensidad:</strong> ${intensity.toFixed(2)}</div>
                            <div><strong>Radio:</strong> ${point.spread_radius_km?.toFixed(2) || 0} km</div>
                            <div><strong>Área afectada:</strong> ${point.affected_area_km2?.toFixed(2) || 0} km²</div>
                            <div><strong>Perímetro:</strong> ${point.perimeter_km?.toFixed(2) || 0} km</div>
                        </div>
                    </div>
                `;
                circle.bindPopup(popupContent);
            });

            predictionLayers.push(...circles);
        }

        function updatePredictionLayers() {
            predictionLayers.forEach(function(layer) {
                if (showPredictions) {
                    if (!map.hasLayer(layer)) {
                        map.addLayer(layer);
                    }
                } else {
                    if (map.hasLayer(layer)) {
                        map.removeLayer(layer);
                    }
                }
            });

            if (showPredictions) {
                reportsData.forEach(function(report) {
                    if (report.incendio_id && !loadedPredictions.has(report.incendio_id)) {
                        loadFirePrediction(report.incendio_id);
                    }
                });
            }
        }

        function startInit() {
            if (typeof L === 'undefined') {
                console.log('[Mapa] Leaflet aún no está cargado, esperando...');
                setTimeout(startInit, 100);
                return;
            }
            
            // Verificar si el card del mapa está colapsado
            const mapaCard = document.querySelector('#mapaCampo')?.closest('.card');
            if (mapaCard && mapaCard.classList.contains('collapsed-card')) {
                // Si está colapsado, esperar a que se expanda
                const collapseBtn = mapaCard.querySelector('[data-card-widget="collapse"]');
                if (collapseBtn) {
                    collapseBtn.addEventListener('click', function() {
                        setTimeout(function() {
                            if (!mapaCard.classList.contains('collapsed-card')) {
                                setTimeout(initMap, 350);
                            }
                        }, 100);
                    });
                }
                return;
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(initMap, 100);
                });
            } else {
                setTimeout(initMap, 100);
            }
        }
        
        startInit();
        
        // Reinicializar mapa cuando se expande el card después de estar colapsado
        document.addEventListener('DOMContentLoaded', function() {
            const mapaCard = document.querySelector('#mapaCampo')?.closest('.card');
            if (mapaCard) {
                const collapseBtn = mapaCard.querySelector('[data-card-widget="collapse"]');
                if (collapseBtn) {
                    collapseBtn.addEventListener('click', function() {
                        setTimeout(function() {
                            if (!mapaCard.classList.contains('collapsed-card') && !map) {
                                setTimeout(initMap, 350);
                            } else if (map && !mapaCard.classList.contains('collapsed-card')) {
                                // Invalidar tamaño si el mapa ya existe
                                setTimeout(function() {
                                    if (map) {
                                        map.invalidateSize();
                                    }
                                }, 350);
                            }
                        }, 100);
                    });
                }
            }
            
            // Reinicializar mapa cuando se muestra la pestaña del mapa
            const mapaTab = document.getElementById('mapa-tab');
            if (mapaTab) {
                mapaTab.addEventListener('shown.bs.tab', function() {
                    setTimeout(function() {
                        if (!map) {
                            initMap();
                        } else {
                            // Invalidar tamaño del mapa cuando se muestra la pestaña
                            setTimeout(function() {
                                if (map) {
                                    map.invalidateSize();
                                }
                            }, 100);
                        }
                    }, 100);
                });
            }
        });
    })();
    
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
    @endif
});
</script>

{{-- Incluir Leaflet para el mapa de campo --}}
@if(Auth::user()->hasAnyRole(['admin', 'encargado']))
@include('partials.leaflet')
@endif
@endif
@endsection