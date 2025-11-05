<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="estado_salud" class="form-label">{{ __('Estado Salud') }}</label>
            <input type="text" name="estado_salud" class="form-control @error('estado_salud') is-invalid @enderror" value="{{ old('estado_salud', $animalProfile?->estado_salud) }}" id="estado_salud" placeholder="Estado Salud">
            {!! $errors->first('estado_salud', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="sexo" class="form-label">{{ __('Sexo') }}</label>
            <input type="text" name="sexo" class="form-control @error('sexo') is-invalid @enderror" value="{{ old('sexo', $animalProfile?->sexo) }}" id="sexo" placeholder="Sexo">
            {!! $errors->first('sexo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="especie" class="form-label">{{ __('Especie') }}</label>
            <input type="text" name="especie" class="form-control @error('especie') is-invalid @enderror" value="{{ old('especie', $animalProfile?->especie) }}" id="especie" placeholder="Especie">
            {!! $errors->first('especie', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="raza" class="form-label">{{ __('Raza') }}</label>
            <input type="text" name="raza" class="form-control @error('raza') is-invalid @enderror" value="{{ old('raza', $animalProfile?->raza) }}" id="raza" placeholder="Raza">
            {!! $errors->first('raza', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="alimentacion" class="form-label">{{ __('Alimentacion') }}</label>
            <input type="text" name="alimentacion" class="form-control @error('alimentacion') is-invalid @enderror" value="{{ old('alimentacion', $animalProfile?->alimentacion) }}" id="alimentacion" placeholder="Alimentacion">
            {!! $errors->first('alimentacion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="frecuencia" class="form-label">{{ __('Frecuencia') }}</label>
            <input type="text" name="frecuencia" class="form-control @error('frecuencia') is-invalid @enderror" value="{{ old('frecuencia', $animalProfile?->frecuencia) }}" id="frecuencia" placeholder="Frecuencia">
            {!! $errors->first('frecuencia', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="cantidad" class="form-label">{{ __('Cantidad') }}</label>
            <input type="text" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" value="{{ old('cantidad', $animalProfile?->cantidad) }}" id="cantidad" placeholder="Cantidad">
            {!! $errors->first('cantidad', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="color" class="form-label">{{ __('Color') }}</label>
            <input type="text" name="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color', $animalProfile?->color) }}" id="color" placeholder="Color">
            {!! $errors->first('color', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="imagen" class="form-label">{{ __('Imagen') }}</label>
            <input type="text" name="imagen" class="form-control @error('imagen') is-invalid @enderror" value="{{ old('imagen', $animalProfile?->imagen) }}" id="imagen" placeholder="Imagen">
            {!! $errors->first('imagen', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="reporte_id" class="form-label">{{ __('Reporte Id') }}</label>
            <input type="text" name="reporte_id" class="form-control @error('reporte_id') is-invalid @enderror" value="{{ old('reporte_id', $animalProfile?->reporte_id) }}" id="reporte_id" placeholder="Reporte Id">
            {!! $errors->first('reporte_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="detalle" class="form-label">{{ __('Detalle') }}</label>
            <input type="text" name="detalle" class="form-control @error('detalle') is-invalid @enderror" value="{{ old('detalle', $animalProfile?->detalle) }}" id="detalle" placeholder="Detalle">
            {!! $errors->first('detalle', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>