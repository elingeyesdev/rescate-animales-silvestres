@extends('adminlte::page')

@section('template_title')
    {{ $center->name ?? __('Show') . ' ' . __('Center') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} {{ __('Center') }}</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('centers.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $center->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Direccion:</strong>
                                    {{ $center->direccion }}
                                </div>
                                <!-- Ocultamos latitud y longitud en detalle -->
                                @if(!is_null($center->latitud) && !is_null($center->longitud))
                                <div class="form-group mb-2 mb20">
                                    <strong>Ubicación:</strong>
                                    <div id="center_map" style="height: 320px; border-radius: 6px; overflow: hidden;"></div>
                                </div>
                                @endif
                                <div class="form-group mb-2 mb20">
                                    <strong>Contacto:</strong>
                                    {{ $center->contacto }}
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
    var rawLat = @json($center->latitud);
    var rawLon = @json($center->longitud);
    var lat = parseFloat(rawLat);
    var lon = parseFloat(rawLon);
    var hasLat = rawLat !== null && rawLat !== '' && Number.isFinite(lat);
    var hasLon = rawLon !== null && rawLon !== '' && Number.isFinite(lon);
    if (hasLat && hasLon) {
        window.initStaticMap({
            mapId: 'center_map',
            lat: lat,
            lon: lon,
            zoom: 16,
            popup: @json($center->direccion ?? null),
        });
    }
});
</script>
@endsection
