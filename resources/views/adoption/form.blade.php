<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label class="form-label">{{ __('Ubicación (clic en el mapa)') }}</label>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div id="adoption_map" style="height: 300px; border-radius: 4px;"></div>
                </div>
            </div>
            <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $adoption?->latitud) }}">
            <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $adoption?->longitud) }}">
        </div>
        <div class="form-group mb-2 mb20">
            <label for="direccion" class="form-label">{{ __('Direccion') }}</label>
            <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $adoption?->direccion) }}" id="direccion" placeholder="Direccion">
            {!! $errors->first('direccion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="detalle" class="form-label">{{ __('Detalle') }}</label>
            <input type="text" name="detalle" class="form-control @error('detalle') is-invalid @enderror" value="{{ old('detalle', $adoption?->detalle) }}" id="detalle" placeholder="Detalle">
            {!! $errors->first('detalle', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="aprobada" class="form-label">{{ __('Aprobada') }}</label>
            <select name="aprobada" id="aprobada" class="form-control @error('aprobada') is-invalid @enderror">
                @php($ap = old('aprobada', $adoption?->aprobada))
                <option value="0" {{ (string)$ap === '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ (string)$ap === '1' ? 'selected' : '' }}>Sí</option>
            </select>
            {!! $errors->first('aprobada', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="animal_file_id" class="form-label">{{ __('Animal (adoptable)') }}</label>
            <select name="animal_file_id" id="animal_file_id" class="form-control @error('animal_file_id') is-invalid @enderror">
                <option value="">{{ __('Seleccione') }}</option>
                @foreach(($animalFiles ?? []) as $af)
                    <option value="{{ $af->id }}" {{ (string)old('animal_file_id', $adoption?->animal_file_id) === (string)$af->id ? 'selected' : '' }}>
                        #{{ $af->id }} {{ $af->nombre ? '- ' . $af->nombre : '' }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('animal_file_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="adoptante_id" class="form-label">{{ __('Adoptante') }}</label>
            <select name="adoptante_id" id="adoptante_id" class="form-control @error('adoptante_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($people ?? []) as $p)
                    <option value="{{ $p->id }}" {{ (string)old('adoptante_id', $adoption?->adoptante_id) === (string)$p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('adoptante_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
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
        mapId: 'adoption_map',
        latInputId: 'latitud',
        lonInputId: 'longitud',
        dirInputId: 'direccion',
        start: { lat: -17.7833, lon: -63.1821, zoom: 13 },
        enableReverseGeocode: true,
    });
});
</script>