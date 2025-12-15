@extends('adminlte::page')

@section('template_title')
    {{ $report->name ?? __('Show') . " " . __('Report') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-clipboard-list mr-1"></i>
                                {{ __('Información del Hallazgo') }}
                            </h3>
                        </div>
                        <div>
                            @if(Auth::user()->hasAnyRole(['admin', 'encargado']) && (int)$report->aprobado !== 1)
                            <button type="button" 
                                    class="btn btn-success btn-sm mr-2" 
                                    data-toggle="modal" 
                                    data-target="#modalAprobarReport{{ $report->id }}"
                                    title="{{ __('Aprobar o rechazar este hallazgo') }}">
                                <i class="fa fa-check"></i> {{ __('Aprobar/Rechazar') }}
                            </button>
                            @endif
                            <a class="btn btn-success btn-sm" href="{{ route('reports.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        @php
                            $urg = $report->urgencia;
                            if (is_numeric($urg)) {
                                if ($urg >= 4) { $urgClass = 'danger'; }
                                elseif ($urg == 3) { $urgClass = 'warning'; }
                                else { $urgClass = 'info'; }
                            } else { $urgClass = 'secondary'; }
                        @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Se oculta Reportante según requerimiento -->
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Estado inicial del animal') }}:</strong> {{ $report->condicionInicial?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Tipo de incidente') }}:</strong> {{ $report->incidentType?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Urgencia') }}:</strong>
                                    <span class="badge badge-{{ $urgClass }}">{{ is_null($urg) ? __('N/A') : $urg }}</span>
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Aprobado') }}:</strong> {{ (int)$report->aprobado === 1 ? __('Sí') : __('No') }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Estado') }}:</strong>
                                    <span class="badge {{ $report->getEstadoBadgeClass() }} ml-2">{{ $report->getEstado() }}</span>
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Tamaño') }}:</strong>
                                    @php
                                        $tamano = $report->tamano ?? null;
                                        if ($tamano) {
                                            // Convertir a minúsculas primero
                                            $tamanoLower = mb_strtolower(trim($tamano));
                                            // Mapear valores comunes a formato correcto
                                            $mapa = [
                                                'pequeno' => 'Pequeño',
                                                'pequeño' => 'Pequeño',
                                                'mediano' => 'Mediano',
                                                'grande' => 'Grande'
                                            ];
                                            $tamanoFormateado = $mapa[$tamanoLower] ?? ucfirst($tamanoLower);
                                        } else {
                                            $tamanoFormateado = '-';
                                        }
                                    @endphp
                                    {{ $tamanoFormateado }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('¿Puede moverse?') }}:</strong> {{ is_null($report->puede_moverse) ? '-' : ($report->puede_moverse ? __('Sí') : __('No')) }}
                                </div>
                                
                                @if($report->firstTransfer?->center)
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Traslado a') }}:</strong>
                                    {{ $report->firstTransfer->center->nombre }}
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Fecha de reporte') }}:</strong> {{ optional($report->created_at)->format('d/m/Y H:i') }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Dirección') }}:</strong> {{ $report->direccion ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Observaciones') }}:</strong> {{ $report->observaciones ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Imagen del hallazgo') }}:</strong>
                                    @if($report->imagen_url)
                                        <div style="max-width: 100%; overflow: hidden; border-radius: 4px;">
                                            <img src="{{ asset('storage/' . $report->imagen_url) }}" alt="img" style="max-width: 100%; max-height: 180px; height: auto; width: auto; object-fit: contain; border-radius: 4px;">
                                        </div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if(!is_null($report->latitud) && !is_null($report->longitud))
                        <div class="form-group mb-2 mb20">
                            <strong>{{ __('Ubicación del hallazgo') }}:</strong>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div id="report_map" style="height: 400px; border-radius: 6px; overflow: hidden; width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
@include('partials.leaflet')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var rawLat = @json($report->latitud);
    var rawLon = @json($report->longitud);
    var lat = parseFloat(rawLat);
    var lon = parseFloat(rawLon);
    var hasLat = rawLat !== null && rawLat !== '' && Number.isFinite(lat);
    var hasLon = rawLon !== null && rawLon !== '' && Number.isFinite(lon);
    if (hasLat && hasLon) {
        window.initStaticMap({
            mapId: 'report_map',
            lat: lat,
            lon: lon,
            zoom: 16,
            popup: @json($report->direccion ?? null),
        });
    }
});
</script>

{{-- Modal de aprobación para el reporte --}}
@if(Auth::user()->hasAnyRole(['admin', 'encargado']) && (int)$report->aprobado !== 1)
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
                <input type="hidden" name="redirect_to" value="show">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endif

@include('partials.page-pad')
@endsection
