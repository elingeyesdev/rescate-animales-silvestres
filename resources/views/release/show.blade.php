@extends('adminlte::page')

@section('template_title')
    {{ $release->name ?? __('Show') . ' ' . __('Release') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                @if($release->animalFile)
                <div class="card">
                    <div class="card-header bg-info d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-paw mr-1"></i>
                                {{ __('Información del Animal') }}
                            </h3>
                        </div>
                        <div>
                            <a class="btn btn-primary btn-sm" href="{{ route('releases.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Nombre') }}:</strong>
                                    {{ $release->animalFile->animal?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Especie') }}:</strong>
                                    {{ $release->animalFile->species?->nombre ?? '-' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Estado') }}:</strong>
                                    {{ $release->animalFile->animalStatus?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Imagen') }}:</strong>
                                    @if($release->animalFile->imagen_url)
                                        <div style="max-width: 100%; overflow: hidden; border-radius: 4px;">
                                            <img src="{{ asset('storage/' . $release->animalFile->imagen_url) }}" alt="img" style="max-width: 100%; max-height: 180px; height: auto; width: auto; object-fit: contain; border-radius: 4px;">
                                        </div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card">
                    <div class="card-header bg-success d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-dove mr-1"></i>
                                {{ __('Información de la Liberación') }}
                            </h3>
                        </div>
                        @if(!$release->animalFile)
                        <div>
                            <a class="btn btn-primary btn-sm" href="{{ route('releases.index') }}"> {{ __('Back') }}</a>
                        </div>
                        @endif
                    </div>
                    <div class="card-body bg-white">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Dirección') }}:</strong>
                                    {{ $release->direccion ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Aprobada') }}:</strong>
                                    {{ (int)$release->aprobada === 1 ? __('Sí') : __('No') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Fecha de liberación') }}:</strong>
                                    {{ optional($release->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                        @if($release->detalle)
                        <div class="form-group mb-2 mb20">
                            <strong>{{ __('Detalle') }}:</strong>
                            {{ $release->detalle }}
                        </div>
                        @endif
                        @if(!is_null($release->latitud) && !is_null($release->longitud))
                        <div class="form-group mb-2 mb20">
                            <strong>{{ __('Ubicación de la liberación') }}:</strong>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div id="release_map" style="height: 400px; border-radius: 6px; overflow: hidden; width: 100%;"></div>
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
@include('partials.page-pad')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var rawLat = @json($release->latitud);
    var rawLon = @json($release->longitud);
    var lat = parseFloat(rawLat);
    var lon = parseFloat(rawLon);
    var hasLat = rawLat !== null && rawLat !== '' && Number.isFinite(lat);
    var hasLon = rawLon !== null && rawLon !== '' && Number.isFinite(lon);
    if (hasLat && hasLon) {
        window.initStaticMap({
            mapId: 'release_map',
            lat: lat,
            lon: lon,
            zoom: 16,
            popup: @json($release->direccion ?? null),
        });
    }
});
</script>
@endsection
