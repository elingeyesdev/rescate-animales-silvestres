@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-dark">Panel de Control</h1>
        <small class="text-muted"><i class="fas fa-calendar-alt mr-1"></i> {{ date('d/m/Y') }}</small>
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
</style>
@stop

@section('content')
<div class="container-fluid pb-4">

    {{-- =========================================================== --}}
    {{-- SECCIÓN: ADMIN Y ENCARGADO --}}
    {{-- =========================================================== --}}
    @if(Auth::user()->hasAnyRole(['admin', 'encargado']))
        
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

        {{-- 2. Acciones Rápidas con Efecto Hover Mejorado --}}
        <div class="card shadow-none bg-transparent border-0 mb-2">
            <div class="card-body py-2 px-0">
                <p class="text-muted text-uppercase font-weight-bold text-xs mb-2 pl-1 text-center">Acciones Rápidas</p>
                <div class="row g-2 justify-content-center">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="{{ route('reports.index') }}" class="btn btn-app btn-action-custom w-100">
                            @if(($pendingReportsCount ?? 0) > 0) <span class="badge bg-purple">{{ $pendingReportsCount }}</span> @endif
                            <i class="fas fa-map-marked-alt text-purple"></i> Hallazgos
                        </a>
                    </div>
                    
                    @if(Auth::user()->hasAnyRole(['admin','encargado']))
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="{{ route('animal-files.create') }}" class="btn btn-app btn-action-custom w-100">
                            <i class="fas fa-plus-circle text-success"></i> Nuevo Animal
                        </a>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="{{ route('releases.create') }}" class="btn btn-app btn-action-custom w-100">
                            <i class="fas fa-dove text-info"></i> Liberación
                        </a>
                    </div>
                    @endif

                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="{{ route('animal-files.index') }}" class="btn btn-app btn-action-custom w-100">
                            <i class="fas fa-list text-secondary"></i> Fichas
                        </a>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="{{ route('releases.index') }}" class="btn btn-app btn-action-custom w-100">
                            <i class="fas fa-history text-muted"></i> Historial Lib.
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Sección de Gráficos (Altura Igualada) --}}
        @if(isset($reportsByMonth) || isset($animalsByStatus))
        <div class="row align-items-stretch">
            
            {{-- Gráfico Principal: RADAR (Reemplazo visual interesante) --}}
            @if(isset($reportsByMonth))
            <div class="@if(isset($animalsByStatus)) col-lg-7 @else col-lg-12 @endif d-flex">
                <div class="card card-outline card-purple shadow-sm w-100">
                    <div class="card-header border-0">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-chart-bar mr-2 text-purple"></i> Comparativa Operativa (Totales)
                        </h3>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div style="width: 100%;">
                            <canvas id="operationsCompareChart" style="min-height: 320px; height: 320px; max-height: 320px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Gráfico Secundario: Estado de Animales --}}
            @if(isset($animalsByStatus))
            <div class="col-lg-5 d-flex">
                <div class="card card-outline card-info shadow-sm w-100">
                    <div class="card-header border-0">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-chart-pie mr-2 text-info"></i> Estado Actual
                        </h3>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div style="width: 100%;">
                             <canvas id="animalsStatusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                    <div class="card-footer bg-white p-0">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item border-bottom">
                                <a href="#" class="nav-link text-muted py-2">
                                    Total Registrados <span class="float-right badge bg-primary">{{ $totalAnimals ?? 0 }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
        
        {{-- Gráfico Terciario: Solicitudes (Multicolor y Horizontal) --}}
        @if(isset($applicationsByType))
        <div class="row mt-3">
             <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-user-plus mr-2 text-secondary"></i> Solicitudes de Voluntariado/Personal
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="applicationsChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- 4. Bandeja de Mensajes --}}
        <div class="row mt-3" id="mensajes">
            <div class="col-lg-12">
                <div class="card card-outline card-danger shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-inbox mr-2"></i> Mensajes Recientes
                        </h3>
                        <div class="card-tools">
                            @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                <span class="badge badge-danger">{{ $unreadMessagesCount }} nuevos</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($unreadMessages) && $unreadMessages->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 20%">Usuario</th>
                                            <th style="width: 15%">Motivo</th>
                                            <th>Mensaje</th>
                                            <th style="width: 15%">Fecha</th>
                                            <th style="width: 10%" class="text-right">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($unreadMessages as $message)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-gray-light rounded-circle d-flex justify-content-center align-items-center mr-2" style="width:35px; height:35px;">
                                                            <i class="fas fa-user text-muted"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-weight-bold">{{ $message->user->person->nombre ?? 'Usuario' }}</div>
                                                            <small class="text-muted">{{ $message->user->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $motivos = \App\Models\ContactMessage::getMotivos();
                                                        $motivoLabel = $motivos[$message->motivo] ?? $message->motivo;
                                                        $badgeColor = 'secondary';
                                                        if($message->motivo == 'emergencia') $badgeColor = 'danger';
                                                        if($message->motivo == 'consulta') $badgeColor = 'info';
                                                        if($message->motivo == 'adopcion') $badgeColor = 'success';
                                                    @endphp
                                                    <span class="badge badge-{{ $badgeColor }}">{{ $motivoLabel }}</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0 text-sm text-muted text-truncate" style="max-width: 400px;">
                                                        {{ Str::limit($message->mensaje, 100) }}
                                                    </p>
                                                </td>
                                                <td class="text-muted text-sm">
                                                    <i class="far fa-clock mr-1"></i> {{ $message->created_at->diffForHumans() }}
                                                </td>
                                                <td class="text-right">
                                                    <form action="{{ route('contact-messages.update', $message->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <button type="submit" class="btn btn-xs btn-outline-success" title="Marcar como leído">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Clean" style="width: 64px; opacity: 0.5;">
                                <p class="text-muted mt-3">¡Todo limpio! No hay mensajes nuevos.</p>
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

    // 1. Gráfico de Hallazgos (LÍNEA)
    @php $totalsAvailable = isset($totalReports) || isset($totalTransfers) || isset($releasedAnimals) || isset($totalAnimals); @endphp
    @if($totalsAvailable)
    const cmp = document.getElementById('operationsCompareChart');
    if (cmp) {
        const totals = {
            hallazgos: {{ $totalReports ?? 0 }},
            traslados: {{ $totalTransfers ?? 0 }},
            liberaciones: {{ $releasedAnimals ?? 0 }},
            hojas: {{ $totalAnimals ?? 0 }}
        };
        const labels = ['Hallazgos','Traslados','Liberaciones','Hojas de vida'];
        const values = [totals.hallazgos, totals.traslados, totals.liberaciones, totals.hojas];
        const sum = values.reduce((a,b)=>a+b,0);
        if (sum === 0) {
            cmp.outerHTML = '<div class="text-center text-muted">Sin datos totales para mostrar</div>';
        } else {
            new Chart(cmp, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Totales',
                        data: values,
                        backgroundColor: ['#e74c3c','#3498db','#2ecc71','#8e44ad'],
                        borderColor: ['#e74c3c','#3498db','#2ecc71','#8e44ad'],
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
    @endif

    // 2. Gráfico de Animales por Estado (Doughnut)
    @if(isset($animalsByStatus) && !empty($animalsByStatus))
    const animalsCtx = document.getElementById('animalsStatusChart');
    if (animalsCtx) {
        const animalsData = @json($animalsByStatus);
        
        new Chart(animalsCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(animalsData),
                datasets: [{
                    data: Object.values(animalsData),
                    backgroundColor: [
                        '#10b981', // Emerald
                        '#3b82f6', // Blue
                        '#f59e0b', // Amber
                        '#ef4444', // Red
                        '#8b5cf6', // Violet
                        '#6b7280', // Gray
                        '#ec4899', // Pink
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { usePointStyle: true, boxWidth: 10, padding: 15, font: { size: 11 } }
                    }
                }
            }
        });
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
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { display: false } },
                    y: { grid: { display: false, drawBorder: false } }
                }
            }
        });
    }
    @endif
});
</script>
@endif
@endsection