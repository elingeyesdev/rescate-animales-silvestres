@extends('adminlte::page')

@section('template_title')
    {{ $animalFile->name ?? __('Show') . ' ' . __('Animal File') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div>
                            <span class="card-title">{{ __('Show') }} {{ __('Animal File') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-info btn-sm mr-2" href="{{ route('animal-histories.show', $animalFile->id) }}">
                                <i class="fas fa-history"></i> {{ __('Ver historial') }}
                            </a>
                            <a class="btn btn-primary btn-sm" href="{{ route('animal-files.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <h5 class="mb-3">{{ __('Información del Animal') }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $animalFile->animal?->nombre ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Sexo:</strong>
                                    {{ $animalFile->animal?->sexo ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Especie:</strong>
                                    {{ $animalFile->species?->nombre ?? '-' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $animalFile->animalStatus?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Imagen:</strong>
                                    @if($animalFile->imagen_url)
                                        <div style="max-width: 100%; overflow: hidden; border-radius: 4px;">
                                            <img src="{{ asset('storage/' . $animalFile->imagen_url) }}" alt="img" style="max-width: 100%; max-height: 180px; height: auto; width: auto; object-fit: contain; border-radius: 4px;">
                                        </div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($animalFile->animal?->report)
                    @php
                        $report = $animalFile->animal->report;
                        $urg = $report->urgencia;
                        if (is_numeric($urg)) {
                            if ($urg >= 4) { $urgClass = 'danger'; }
                            elseif ($urg == 3) { $urgClass = 'warning'; }
                            else { $urgClass = 'info'; }
                        } else { $urgClass = 'secondary'; }
                    @endphp
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">{{ __('Información del Hallazgo') }}</span>
                        </div>
                        <div class="card-body bg-white">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2 mb20">
                                        <strong>{{ __('Estado inicial del animal') }}:</strong>
                                        {{ $report->condicionInicial?->nombre ?? '-' }}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <strong>{{ __('Tipo de incidente') }}:</strong>
                                        {{ $report->incidentType?->nombre ?? '-' }}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <strong>{{ __('Urgencia') }}:</strong>
                                        <span class="badge badge-{{ $urgClass }}">{{ is_null($urg) ? __('N/A') : $urg }}</span>
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <strong>{{ __('Aprobado') }}:</strong>
                                        {{ (int)$report->aprobado === 1 ? __('Sí') : __('No') }}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <strong>{{ __('Tamaño') }}:</strong>
                                        {{ $report->tamano ?? '-' }}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <strong>{{ __('¿Puede moverse?') }}:</strong>
                                        {{ is_null($report->puede_moverse) ? '-' : ($report->puede_moverse ? __('Sí') : __('No')) }}
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
                                        <strong>{{ __('Fecha de reporte') }}:</strong>
                                        {{ optional($report->created_at)->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <strong>{{ __('Dirección') }}:</strong>
                                        {{ $report->direccion ?: '-' }}
                                    </div>
                                    
                                    <div class="form-group mb-2 mb20">
                                        <strong>{{ __('Observaciones') }}:</strong>
                                        {{ $report->observaciones ?: '-' }}
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
                @endif
            </div>
        </div>
    </section>
    @if($animalFile->animal?->report && !is_null($animalFile->animal->report->latitud) && !is_null($animalFile->animal->report->longitud))
        @include('partials.leaflet')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var rawLat = @json($animalFile->animal->report->latitud);
            var rawLon = @json($animalFile->animal->report->longitud);
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
                    popup: @json($animalFile->animal->report->direccion ?? null),
                });
            }
        });
        </script>
    @endif
    @include('partials.page-pad')
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
