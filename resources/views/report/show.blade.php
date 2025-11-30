@extends('adminlte::page')

@section('template_title')
    {{ $report->name ?? __('Show') . " " . __('Report') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div>
                            <span class="card-title">{{ __('Show') }} {{ __('Report') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-primary btn-sm" href="{{ route('reports.index') }}"> {{ __('Back') }}</a>
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
                                    <strong>{{ __('Tamaño') }}:</strong> {{ $report->tamano ?? '-' }}
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
                                    <strong>{{ __('Imagen') }}:</strong>
                                    @if($report->imagen_url)
                                        <div><img src="{{ asset('storage/' . $report->imagen_url) }}" alt="img" style="max-height:180px; border-radius:4px;"></div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if(!is_null($report->latitud) && !is_null($report->longitud))
                        <div class="form-group mb-2 mb20">
                            <strong>{{ __('Ubicación') }}:</strong>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div id="report_map" style="height: 320px; border-radius: 6px; overflow: hidden;"></div>
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
@include('partials.page-pad')
@endsection
