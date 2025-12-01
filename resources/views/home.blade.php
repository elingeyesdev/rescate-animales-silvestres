@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')
    <h1>Inicio</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(Auth::user()->hasAnyRole(['admin', 'encargado']))
        {{-- DASHBOARD PARA ADMIN Y ENCARGADO --}}
        

        <div class="row">
            {{-- Estadísticas rápidas --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $pendingReportsCount ?? 0 }}</h3>
                        <p>Hallazgos Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <a href="{{ route('reports.index') }}" class="small-box-footer">
                        Ver más <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ ($pendingRescuersCount ?? 0) + ($pendingVeterinariansCount ?? 0) + ($pendingCaregiversCount ?? 0) }}</h3>
                        <p>Solicitudes Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <a href="{{ route('people.index') }}" class="small-box-footer">
                        Ver más <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalAnimals ?? 0 }}</h3>
                        <p>Animales en Sistema</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <a href="{{ route('animal-files.index') }}" class="small-box-footer">
                        Ver más <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $unreadMessagesCount ?? 0 }}</h3>
                        <p>Mensajes No Leídos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <a href="#mensajes" class="small-box-footer">
                        Ver más <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Gráficos para Admin y Encargado --}}
        @if(isset($reportsByMonth) || isset($animalsByStatus) || isset($applicationsByType))
        <div class="row">
            {{-- Gráfico de Hallazgos por Mes --}}
            @if(isset($reportsByMonth))
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Hallazgos por Mes (Últimos 6 meses)
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="reportsChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            @endif

            {{-- Gráfico de Animales por Estado --}}
            @if(isset($animalsByStatus))
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Animales por Estado
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="animalsStatusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Gráfico de Solicitudes por Tipo --}}
        @if(isset($applicationsByType))
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Solicitudes por Tipo
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="applicationsChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
        <div class="row">
            {{-- Mensajes de contacto no leídos --}}
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-transparent">
                        <h3 class="card-title">
                            <i class="fas fa-envelope"></i> Mensajes de Contacto
                            @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                <span class="badge badge-danger ml-2">{{ $unreadMessagesCount }}</span>
                            @endif
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($unreadMessages) && $unreadMessages->count() > 0)
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Motivo</th>
                                            <th>Mensaje</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($unreadMessages as $message)
                                            <tr>
                                                <td>
                                                    {{ $message->user->person->nombre ?? $message->user->email }}
                                                    <br><small class="text-muted">{{ $message->user->email }}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $motivos = \App\Models\ContactMessage::getMotivos();
                                                        $motivoLabel = $motivos[$message->motivo] ?? $message->motivo;
                                                    @endphp
                                                    {{ $motivoLabel }}
                                                </td>
                                                <td>
                                                    <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ Str::limit($message->mensaje, 100) }}
                                                    </div>
                                                </td>
                                                <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <form action="{{ route('contact-messages.update', $message->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check"></i> Marcar como leído
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-3 text-center text-muted">
                                No hay mensajes de contacto pendientes.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ESTADÍSTICAS GENERALES PARA TODOS LOS ROLES --}}
    @php
        $isOnlyCitizen = Auth::user()->hasRole('ciudadano') && !Auth::user()->hasAnyRole(['admin', 'encargado', 'rescatista', 'veterinario', 'cuidador']);
    @endphp
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalAnimals ?? 0 }}</h3>
                    <p>Animales en el Sistema</p>
                </div>
                <div class="icon">
                    <i class="fas fa-paw"></i>
                </div>
                @if(!$isOnlyCitizen)
                <a href="{{ route('animal-files.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
                @else
                <div class="small-box-footer" style="background-color: rgba(0,0,0,.1); padding: 10px; text-align: center; color: rgba(255,255,255,.8);">
                    &nbsp;
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $releasedAnimals ?? 0 }}</h3>
                    <p>Animales Liberados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dove"></i>
                </div>
                @if(!$isOnlyCitizen)
                <a href="{{ route('releases.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
                @else
                <div class="small-box-footer" style="background-color: rgba(0,0,0,.1); padding: 10px; text-align: center; color: rgba(255,255,255,.8);">
                    &nbsp;
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalReports ?? 0 }}</h3>
                    <p>Total de Hallazgos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                @if(!$isOnlyCitizen)
                <a href="{{ route('reports.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
                @else
                <div class="small-box-footer" style="background-color: rgba(0,0,0,.1); padding: 10px; text-align: center; color: rgba(255,255,255,.8);">
                    &nbsp;
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalTransfers ?? 0 }}</h3>
                    <p>Traslados Realizados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
                @if(!$isOnlyCitizen)
                <a href="{{ route('transfers.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
                @else
                <div class="small-box-footer" style="background-color: rgba(0,0,0,.1); padding: 10px; text-align: center; color: rgba(255,255,255,.8);">
                    &nbsp;
                </div>
                @endif
            </div>
        </div>
    </div>

    @if(!Auth::user()->hasAnyRole(['admin', 'encargado']))
        {{-- DASHBOARD PARA OTROS ROLES --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Bienvenido, {{ Auth::user()->person->nombre ?? Auth::user()->email }}</h3>
                    </div>
                    <div class="card-body">
                        <p>Este es tu panel de control.</p>
                        @if(Auth::user()->hasRole('veterinario'))
                            <p>Animales bajo tu cuidado: <strong>{{ $myAnimalFiles ?? 0 }}</strong></p>
                        @endif
                        @if(Auth::user()->hasRole('rescatista'))
                            <p>Traslados realizados: <strong>{{ $myTransfers ?? 0 }}</strong></p>
                        @endif
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
    // Gráfico de hallazgos por mes
    @if(isset($reportsByMonth) && !empty($reportsByMonth))
    const reportsCtx = document.getElementById('reportsChart');
    if (reportsCtx) {
        const reportsData = @json($reportsByMonth);
        const labels = Object.keys(reportsData);
        const data = Object.values(reportsData);
        
        new Chart(reportsCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Hallazgos',
                    data: data,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    @endif

    // Gráfico de animales por estado
    @if(isset($animalsByStatus) && !empty($animalsByStatus))
    const animalsCtx = document.getElementById('animalsStatusChart');
    if (animalsCtx) {
        const animalsData = @json($animalsByStatus);
        const statusLabels = Object.keys(animalsData);
        const statusData = Object.values(animalsData);
        
        new Chart(animalsCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(86, 119, 226, 0.8)',
                        'rgba(22, 180, 43, 0.8)',
                        'rgba(126, 196, 206, 0.8)',
                        'rgba(133, 65, 107, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    @endif

    // Gráfico de solicitudes por tipo
    @if(isset($applicationsByType) && !empty($applicationsByType))
    const applicationsCtx = document.getElementById('applicationsChart');
    if (applicationsCtx) {
        const applicationsData = @json($applicationsByType);
        const appLabels = Object.keys(applicationsData);
        const appData = Object.values(applicationsData);
        
        new Chart(applicationsCtx, {
            type: 'bar',
            data: {
                labels: appLabels,
                datasets: [{
                    label: 'Cantidad',
                    data: appData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endif
@endsection
