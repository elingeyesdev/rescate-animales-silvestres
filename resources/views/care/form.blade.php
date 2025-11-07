<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="hoja_animal_id" class="form-label">{{ __('Hoja Animal Id') }}</label>
            <input type="text" name="hoja_animal_id" class="form-control @error('hoja_animal_id') is-invalid @enderror" value="{{ old('hoja_animal_id', $care?->hoja_animal_id) }}" id="hoja_animal_id" placeholder="Hoja Animal Id">
            {!! $errors->first('hoja_animal_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tipo_cuidado_id" class="form-label">{{ __('Tipo Cuidado Id') }}</label>
            <input type="text" name="tipo_cuidado_id" class="form-control @error('tipo_cuidado_id') is-invalid @enderror" value="{{ old('tipo_cuidado_id', $care?->tipo_cuidado_id) }}" id="tipo_cuidado_id" placeholder="Tipo Cuidado Id">
            {!! $errors->first('tipo_cuidado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="descripcion" class="form-label">{{ __('Descripcion') }}</label>
            <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $care?->descripcion) }}" id="descripcion" placeholder="Descripcion">
            {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="fecha" class="form-label">{{ __('Fecha') }}</label>
            <input type="text" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $care?->fecha) }}" id="fecha" placeholder="Fecha">
            {!! $errors->first('fecha', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>