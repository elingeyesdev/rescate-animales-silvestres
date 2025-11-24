<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $center?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label class="form-label">{{ __('Ubicación (clic en el mapa)') }}</label>
            <div id="center_map" style="height: 300px; border-radius: 4px;"></div>
            <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $center?->latitud) }}">
            <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $center?->longitud) }}">
        </div>
        <div class="form-group mb-2 mb20">
            <label for="direccion" class="form-label">{{ __('Direccion') }}</label>
            <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $center?->direccion) }}" id="direccion" placeholder="Direccion">
            {!! $errors->first('direccion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="contacto" class="form-label">{{ __('Contacto') }}</label>
            <input type="text" name="contacto" class="form-control @error('contacto') is-invalid @enderror" value="{{ old('contacto', $center?->contacto) }}" id="contacto" placeholder="Contacto">
            {!! $errors->first('contacto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

@include('partials.leaflet')
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.initMapWithGeolocation({
        mapId: 'center_map',
        latInputId: 'latitud',
        lonInputId: 'longitud',
        start: { lat: -17.7833, lon: -63.1821, zoom: 13 },
        enableReverseGeocode: true,
        dirInputId: 'direccion',
    });
});
</script>