<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $animalType?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="permite_adopcion" class="form-label">{{ __('Permite Adopcion') }}</label>
            <input type="text" name="permite_adopcion" class="form-control @error('permite_adopcion') is-invalid @enderror" value="{{ old('permite_adopcion', $animalType?->permite_adopcion) }}" id="permite_adopcion" placeholder="Permite Adopcion">
            {!! $errors->first('permite_adopcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="permite_liberacion" class="form-label">{{ __('Permite Liberacion') }}</label>
            <input type="text" name="permite_liberacion" class="form-control @error('permite_liberacion') is-invalid @enderror" value="{{ old('permite_liberacion', $animalType?->permite_liberacion) }}" id="permite_liberacion" placeholder="Permite Liberacion">
            {!! $errors->first('permite_liberacion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>