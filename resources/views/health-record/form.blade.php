<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="tipo" class="form-label">{{ __('Tipo') }}</label>
            <input type="text" name="tipo" class="form-control @error('tipo') is-invalid @enderror" value="{{ old('tipo', $healthRecord?->tipo) }}" id="tipo" placeholder="Tipo">
            {!! $errors->first('tipo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="descripcion" class="form-label">{{ __('Descripcion') }}</label>
            <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $healthRecord?->descripcion) }}" id="descripcion" placeholder="Descripcion">
            {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tratamiento" class="form-label">{{ __('Tratamiento') }}</label>
            <input type="text" name="tratamiento" class="form-control @error('tratamiento') is-invalid @enderror" value="{{ old('tratamiento', $healthRecord?->tratamiento) }}" id="tratamiento" placeholder="Tratamiento">
            {!! $errors->first('tratamiento', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="fecha_revision" class="form-label">{{ __('Fecha Revision') }}</label>
            <input type="text" name="fecha_revision" class="form-control @error('fecha_revision') is-invalid @enderror" value="{{ old('fecha_revision', $healthRecord?->fecha_revision) }}" id="fecha_revision" placeholder="Fecha Revision">
            {!! $errors->first('fecha_revision', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>