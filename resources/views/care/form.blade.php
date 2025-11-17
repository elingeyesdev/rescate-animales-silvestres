<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="hoja_animal_id" class="form-label">{{ __('Animal') }}</label>
            <select name="hoja_animal_id" id="hoja_animal_id" class="form-control @error('hoja_animal_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($animalFiles ?? []) as $af)
                    <option value="{{ $af->id }}" {{ (string)old('hoja_animal_id', $care?->hoja_animal_id) === (string)$af->id ? 'selected' : '' }}>{{ $af->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('hoja_animal_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tipo_cuidado_id" class="form-label">{{ __('Tipo de Cuidado') }}</label>
            <select name="tipo_cuidado_id" id="tipo_cuidado_id" class="form-control @error('tipo_cuidado_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($careTypes ?? []) as $ct)
                    <option value="{{ $ct->id }}" {{ (string)old('tipo_cuidado_id', $care?->tipo_cuidado_id) === (string)$ct->id ? 'selected' : '' }}>{{ $ct->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('tipo_cuidado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="descripcion" class="form-label">{{ __('Descripcion') }}</label>
            <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $care?->descripcion) }}" id="descripcion" placeholder="Descripcion">
            {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="fecha" class="form-label">{{ __('Fecha') }}</label>
            <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $care?->fecha) }}" id="fecha">
            {!! $errors->first('fecha', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="imagen" class="form-label">{{ __('Imagen (opcional)') }}</label>
            <input type="file" name="imagen" id="imagen" class="form-control @error('imagen') is-invalid @enderror" accept="image/*">
            {!! $errors->first('imagen', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            @if(!empty($care?->imagen_url))
                <div class="mt-2">
                    <img src="{{ $care->imagen_url }}" alt="Imagen cuidado" style="max-height:120px;">
                </div>
            @endif
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>