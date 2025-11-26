@extends('adminlte::page')

@section('template_title')
    {{ $release->name ?? __('Show') . ' ' . __('Release') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div>
                            <span class="card-title">{{ __('Show') }} {{ __('Release') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-primary btn-sm" href="{{ route('releases.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Direccion:</strong>
                                    {{ $release->direccion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Detalle:</strong>
                                    {{ $release->detalle }}
                                </div>
                                <!-- Ocultamos Latitud/Longitud en detalle -->
                                @if(!is_null($release->latitud) && !is_null($release->longitud))
                                <div class="form-group mb-2 mb20">
                                    <strong>Ubicación:</strong>
                                    <div id="release_map" style="height: 320px; border-radius: 6px; overflow: hidden;"></div>
                                </div>
                                @endif
                                <div class="form-group mb-2 mb20">
                                    <strong>Aprobada:</strong>
                                    {{ (int)$release->aprobada === 1 ? 'Sí' : 'No' }}
                                </div>

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
