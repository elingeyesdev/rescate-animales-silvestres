@extends('adminlte::page')

@section('template_title')
    {{ $adoption->name ?? __('Show') . " " . __('Adoption') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div>
                            <span class="card-title">{{ __('Show') }} {{ __('Adoption') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-primary btn-sm" href="{{ route('adoptions.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Direccion:</strong>
                                    {{ $adoption->direccion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Latitud:</strong>
                                    {{ $adoption->latitud }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Longitud:</strong>
                                    {{ $adoption->longitud }}
                                </div>
                                @if(!is_null($adoption->latitud) && !is_null($adoption->longitud))
                                <div class="form-group mb-2 mb20">
                                    <strong>Ubicación:</strong>
                                    <div id="adoption_map" style="height: 320px; border-radius: 6px; overflow: hidden;"></div>
                                </div>
                                @endif
                                <div class="form-group mb-2 mb20">
                                    <strong>Detalle:</strong>
                                    {{ $adoption->detalle }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Aprobada:</strong>
                                    {{ (int)$adoption->aprobada === 1 ? 'Sí' : 'No' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Adoptante:</strong>
                                    {{ $adoption->adopter?->nombre ?? '-' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@include('partials.leaflet')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var rawLat = @json($adoption->latitud);
    var rawLon = @json($adoption->longitud);
    var lat = parseFloat(rawLat);
    var lon = parseFloat(rawLon);
    var hasLat = rawLat !== null && rawLat !== '' && Number.isFinite(lat);
    var hasLon = rawLon !== null && rawLon !== '' && Number.isFinite(lon);
    if (hasLat && hasLon) {
        window.initStaticMap({
            mapId: 'adoption_map',
            lat: lat,
            lon: lon,
            zoom: 16,
            popup: @json($adoption->direccion ?? null),
        });
    }
});
</script>
@include('partials.page-pad')
@endsection
