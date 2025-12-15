@extends('adminlte::page')

@section('template_title')
    {{ __('Reports') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">{{ __('Hallazgos') }}</span>
                            <div class="float-right">
                                
                                <a href="{{ route('reports.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('Crear nuevo') }}
                                </a>
                                @if(Auth::user()->hasAnyRole(['admin', 'encargado']))
                                <a href="{{ route('reports.mapa-campo') }}" class="btn btn-info btn-sm float-right mr-3" data-placement="left">
                                    <i class="fas fa-map-marked-alt"></i> {{ __('Mapa de Campo') }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <form method="GET" class="mb-3 js-auto-filter-form">
                            <div class="form-row">
                                <div class="{{ Auth::user()->hasAnyRole(['admin', 'encargado']) ? 'col-md-3' : 'col-md-4' }}">
                                    <label class="mb-1">
                                        {{ __('Urgencia') }}
                                        <button type="button" class="btn btn-link btn-sm p-0 ml-1 align-baseline" data-toggle="tooltip" title="{{ __('Qué tan pronto se debe rescatar al animal. 1–2: Baja (situación estable), 3: Media (requiere seguimiento), 4–5: Alta (atención rápida).') }}">¿{{ __('Qué es urgencia') }}?</button>
                                    </label>
                                    <select name="urgencia_nivel" class="form-control">
                                        <option value="">{{ __('Todas') }}</option>
                                        <option value="alta" {{ request('urgencia_nivel')==='alta'?'selected':'' }}>{{ __('Alta') }}</option>
                                        <option value="media" {{ request('urgencia_nivel')==='media'?'selected':'' }}>{{ __('Media') }}</option>
                                        <option value="baja" {{ request('urgencia_nivel')==='baja'?'selected':'' }}>{{ __('Baja') }}</option>
                                    </select>
                                </div>
                                @if(Auth::user()->hasAnyRole(['admin', 'encargado']))
                                <div class="col-md-3">
                                    <label class="mb-1">{{ __('Reportante') }}</label>
                                    <select name="persona_id" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        @foreach(($reporters ?? []) as $p)
                                            <option value="{{ $p->id }}" {{ (string)$p->id === (string)request('persona_id') ? 'selected' : '' }}>
                                                {{ $p->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="{{ Auth::user()->hasAnyRole(['admin', 'encargado']) ? 'col-md-3' : 'col-md-4' }}">
                                    <label class="mb-1">{{ __('Tipo de incidente') }}</label>
                                    <select name="tipo_incidente_id" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        @foreach(($incidentTypes ?? []) as $it)
                                            <option value="{{ $it->id }}" {{ (string)$it->id === (string)request('tipo_incidente_id') ? 'selected' : '' }}>
                                                {{ $it->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="{{ Auth::user()->hasAnyRole(['admin', 'encargado']) ? 'col-md-3' : 'col-md-4' }}">
                                    <label class="mb-1">{{ __('Aprobado') }}</label>
                                    <select name="aprobado" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        <option value="1" {{ request('aprobado')==='1'?'selected':'' }}>{{ __('Sí') }}</option>
                                        <option value="0" {{ request('aprobado')==='0'?'selected':'' }}>{{ __('No') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary btn-sm mr-3">{{ __('Buscar') }}</button>
                                <a href="{{ route('reports.index') }}" class="btn btn-link p-0">{{ __('Mostrar todos') }}</a>
                            </div>
                        </form>

                        <style>
                        .report-card-img {
                            width: 100%;
                            height: 180px;
                            object-fit: cover;
                            background: #f4f6f9;
                        }
                        .report-card .card-header { 
                            padding-left: 1.25rem; 
                            padding-right: 1.25rem; 
                            padding-top: 0.75rem;
                            padding-bottom: 0.75rem;
                        }
                        .report-card .card-header .card-tools { margin-left: auto; margin-right: .25rem; }
                        .report-card .card-body { 
                            padding: 0.5rem 1.25rem 0.25rem 1.25rem; 
                        }
                        .report-card .card-body .list-group-item {
                            border-left: 0;
                            border-right: 0;
                            padding: 0.35rem 0;
                            border-color: #dee2e6;
                        }
                        .report-card .card-body .list-group-item:first-child {
                            border-top: 0;
                        }
                        .report-card .card-body .list-group-item:last-child {
                            border-bottom: 0;
                            margin-bottom: 0;
                        }
                        .report-card .card-footer { 
                            padding-top: 0.25rem; 
                            padding-bottom: 0.5rem; 
                            background-color: #f8f9fa;
                            margin-top: 0;
                        }
                        /* Botones iguales y con separación uniforme */
                        .report-card .card-footer form > * { flex: 1 1 0; }
                        .report-card .card-footer form > * + * { margin-left: .5rem; }
                        .report-grid > [class*='col-'] { margin-bottom: 30px; }
                        </style>

                        <div class="row mt-3 report-grid">
                            @foreach ($reports as $report)
                                @php
                                    $urg = $report->urgencia;
                                    // Escala 1..5
                                    if (is_numeric($urg)) {
                                        if ($urg >= 4) { $urgClass = 'danger'; }       // alta
                                        elseif ($urg == 3) { $urgClass = 'warning'; }  // media
                                        else { $urgClass = 'info'; }                   // baja (1-2)
                                    } else {
                                        $urgClass = 'secondary';
                                    }
                                @endphp
                                <div class="col-md-4">
                                    <div class="card card-outline card-secondary h-100 report-card">
                                        @if($report->imagen_url)
                                            <img class="report-card-img" src="{{ asset('storage/' . $report->imagen_url) }}" alt="imagen hallazgo">
                                        @endif
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h3 class="card-title mb-0" title="{{ $report->condicionInicial?->nombre }}">
                                                <i class="fas fa-clipboard-list text-primary mr-2"></i>
                                                {{ \Illuminate\Support\Str::limit($report->condicionInicial?->nombre ?? __('Condición no especificada'), 26) }}
                                            </h3>
                                            <div class="card-tools d-flex align-items-center">
                                                <!--<i class="fas fa-exclamation-circle text-{{ $urgClass }} mr-1"></i>-->
                                                <span class="small text-muted mr-1">{{ __('Urgencia') }}:</span>
                                                <span class="badge badge-{{ $urgClass }}" title="{{ __('Urgencia') }}">
                                                    {{ is_null($urg) ? __('N/A') : $urg }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-unbordered mb-0">
                                                <li class="list-group-item">
                                                    <i class="fas fa-exclamation-triangle text-muted mr-2"></i>
                                                    <b>{{ __('Incidente:') }}</b>
                                                    <span class="float-right">{{ $report->incidentType?->nombre ?? '-' }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <i class="fas fa-{{ (int)$report->aprobado === 1 ? 'check-circle' : 'clock' }} text-muted mr-2"></i>
                                                    <b>{{ __('Aprobado:') }}</b>
                                                    <span class="float-right">
                                                        @if((int)$report->aprobado === 1)
                                                            <span class="badge badge-success">{{ __('Sí') }}</span>
                                                        @else
                                                            <span class="badge badge-warning">{{ __('No') }}</span>
                                                        @endif
                                                    </span>
                                                </li>
                                                <li class="list-group-item">
                                                    <i class="fas fa-info-circle text-muted mr-2"></i>
                                                    <b>{{ __('Estado:') }}</b>
                                                    <span class="float-right">
                                                        <span class="badge {{ $report->getEstadoBadgeClass() }}">{{ $report->getEstado() }}</span>
                                                    </span>
                                                </li>
                                                @if($report->firstTransfer?->center)
                                                <li class="list-group-item">
                                                    <i class="fas fa-hospital text-muted mr-2"></i>
                                                    <b>{{ __('Traslado a:') }}</b>
                                                    <span class="float-right">{{ \Illuminate\Support\Str::limit($report->firstTransfer->center->nombre, 20) }}</span>
                                                </li>
                                                @endif
                                                <li class="list-group-item">
                                                    <i class="fas fa-calendar-alt text-muted mr-2"></i>
                                                    <b>{{ __('Fecha:') }}</b>
                                                    <span class="float-right">{{ optional($report->created_at)->format('d/m/Y') }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                            @php
                                                $isOnlyCitizen = Auth::user()->hasRole('ciudadano') && !Auth::user()->hasAnyRole(['admin', 'encargado', 'rescatista', 'veterinario', 'cuidador']);
                                            @endphp
                                            @if($isOnlyCitizen)
                                            <a class="btn btn-primary btn-sm w-100" href="{{ route('reports.show', $report->id) }}">
                                                <i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}
                                            </a>
                                            @else
                                            <form action="{{ route('reports.destroy', $report->id) }}" method="POST" class="mb-0 d-flex w-100">
                                                <a class="btn btn-primary btn-sm" href="{{ route('reports.show', $report->id) }}">
                                                    <i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}
                                                </a>
                                                @if(Auth::user()->hasAnyRole(['admin', 'encargado']))
                                                <button type="button" 
                                                        class="btn btn-success btn-sm {{ (int)$report->aprobado === 1 ? 'disabled' : '' }}" 
                                                        data-toggle="modal" 
                                                        data-target="#modalAprobarReport{{ $report->id }}"
                                                        {{ (int)$report->aprobado === 1 ? 'disabled' : '' }}
                                                        title="{{ (int)$report->aprobado === 1 ? __('Este hallazgo ya está aprobado') : __('Aprobar o rechazar este hallazgo') }}">
                                                    <i class="fa fa-fw fa-check"></i> {{ __('Aprobar') }}
                                                </button>
                                                @endif
                                                @if(Auth::user()->hasRole('admin'))
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm js-confirm-delete">
                                                    <i class="fa fa-fw fa-trash"></i> {{ __('Eliminar') }}
                                                </button>
                                                @endif
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($reports->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> {{ __('No se ha registrado ningún hallazgo todavía.') }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card-body -->
                    @if($reports->hasPages())
                    <div class="card-footer">
                        <nav aria-label="Hallazgos Page Navigation">
                            <div class="d-flex justify-content-center">
                                {!! $reports->withQueryString()->links('pagination::bootstrap-4') !!}
                            </div>
                        </nav>
                    </div>
                    <!-- /.card-footer -->
                    @endif
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
    
    <style>
        /* Estilos para la paginación */
        .card-footer .pagination {
            margin-bottom: 0;
            justify-content: center;
        }
        .card-footer .pagination .page-item .page-link {
            color: #495057;
            border-color: #dee2e6;
        }
        .card-footer .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        .card-footer .pagination .page-item:hover:not(.active) .page-link {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        .card-footer .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
            cursor: not-allowed;
        }
    </style>
    
    {{-- Modales de aprobación para cada reporte --}}
    @foreach ($reports as $report)
        @if(Auth::user()->hasAnyRole(['admin', 'encargado']))
        <div class="modal fade" id="modalAprobarReport{{ $report->id }}" tabindex="-1" role="dialog" aria-labelledby="modalAprobarReport{{ $report->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAprobarReport{{ $report->id }}Label">
                            <i class="fa fa-check-circle"></i> {{ __('Aprobar/Rechazar Hallazgo') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('reports.approve', $report->id) }}" method="POST" id="formAprobarReport{{ $report->id }}">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <p class="mb-0">{{ __('¿Desea aprobar o rechazar este hallazgo?') }}</p>
                            <input type="hidden" name="action" id="actionReport{{ $report->id }}" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="btnRechazarReport{{ $report->id }}">
                                <i class="fa fa-times-circle"></i> {{ __('Rechazar') }}
                            </button>
                            <button type="button" class="btn btn-success" id="btnAprobarReport{{ $report->id }}">
                                <i class="fa fa-check-circle"></i> {{ __('Aprobar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.querySelector('form.js-auto-filter-form');
        if (form) {
            var applyBtn = form.querySelector('button[type="submit"]');
            applyBtn && applyBtn.addEventListener('click', function(){ /* submit explicit */ });
        }
        if (window.$ && typeof window.$.fn.tooltip === 'function') {
            window.$('[data-toggle="tooltip"]').tooltip();
        }
        
        // Prevenir que se abra el modal si el reporte ya está aprobado
        document.querySelectorAll('[data-target^="#modalAprobarReport"]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                if (this.disabled || this.classList.contains('disabled')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        });

        // Manejar aprobación/rechazo de reportes
        @foreach ($reports as $report)
            @if(Auth::user()->hasAnyRole(['admin', 'encargado']))
            (function() {
                var form = document.getElementById('formAprobarReport{{ $report->id }}');
                var actionInput = document.getElementById('actionReport{{ $report->id }}');
                var btnRechazar = document.getElementById('btnRechazarReport{{ $report->id }}');
                var btnAprobar = document.getElementById('btnAprobarReport{{ $report->id }}');
                
                function submitForm(action) {
                    // Establecer el valor de action
                    if (actionInput) {
                        actionInput.value = action;
                    }
                    
                    // Deshabilitar botones para evitar doble envío
                    if (btnRechazar) btnRechazar.disabled = true;
                    if (btnAprobar) btnAprobar.disabled = true;
                    
                    // Enviar formulario
                    if (form) {
                        form.submit();
                    }
                    return true;
                }
                
                if (btnRechazar) {
                    btnRechazar.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        submitForm('reject');
                    });
                }
                
                if (btnAprobar) {
                    btnAprobar.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        submitForm('approve');
                    });
                }
            })();
            @endif
        @endforeach
    });
    </script>
@endsection
