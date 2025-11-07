<div class="row padding-1 p-1">
    <div class="col-md-12">
    <div class="form-group mb-2 mb20">
            <label class="form-label">{{ __('Ubicación (clic en el mapa)') }}</label>
            <div id="release_map" style="height: 300px; border-radius: 4px;"></div>
            <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $release?->latitud) }}">
            <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $release?->longitud) }}">
        </div>
        <div class="form-group mb-2 mb20">
            <label for="direccion" class="form-label">{{ __('Direccion') }}</label>
            <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $release?->direccion) }}" id="direccion" placeholder="Direccion">
            {!! $errors->first('direccion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="detalle" class="form-label">{{ __('Detalle') }}</label>
            <input type="text" name="detalle" class="form-control @error('detalle') is-invalid @enderror" value="{{ old('detalle', $release?->detalle) }}" id="detalle" placeholder="Detalle">
            {!! $errors->first('detalle', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        
        @if(!empty($release?->id))
        <div class="form-group mb-2 mb20">
            <label for="aprobada" class="form-label">{{ __('Aprobada') }}</label>
            <select name="aprobada" id="aprobada" class="form-control @error('aprobada') is-invalid @enderror">
                @php($ap = old('aprobada', $release?->aprobada))
                <option value="0" {{ (string)$ap === '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ (string)$ap === '1' ? 'selected' : '' }}>Sí</option>
            </select>
            {!! $errors->first('aprobada', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        @endif

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

@include('partials.leaflet')
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.initMapWithGeolocation({
        mapId: 'release_map',
        latInputId: 'latitud',
        lonInputId: 'longitud',
        dirInputId: 'direccion',
        start: { lat: -17.7833, lon: -63.1821, zoom: 13 },
        enableReverseGeocode: true,
    });
});
</script>