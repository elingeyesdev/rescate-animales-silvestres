<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $animalFile?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tipo" class="form-label">{{ __('Tipo') }}</label>
            <input type="text" name="tipo" class="form-control @error('tipo') is-invalid @enderror" value="{{ old('tipo', $animalFile?->tipo) }}" id="tipo" placeholder="Tipo">
            {!! $errors->first('tipo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tipo_id" class="form-label">{{ __('Tipo Id') }}</label>
            <input type="text" name="tipo_id" class="form-control @error('tipo_id') is-invalid @enderror" value="{{ old('tipo_id', $animalFile?->tipo_id) }}" id="tipo_id" placeholder="Tipo Id">
            {!! $errors->first('tipo_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="reporte_id" class="form-label">{{ __('Reporte Id') }}</label>
            <input type="text" name="reporte_id" class="form-control @error('reporte_id') is-invalid @enderror" value="{{ old('reporte_id', $animalFile?->reporte_id) }}" id="reporte_id" placeholder="Reporte Id">
            {!! $errors->first('reporte_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="especie_id" class="form-label">{{ __('Especie Id') }}</label>
            <input type="text" name="especie_id" class="form-control @error('especie_id') is-invalid @enderror" value="{{ old('especie_id', $animalFile?->especie_id) }}" id="especie_id" placeholder="Especie Id">
            {!! $errors->first('especie_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="raza_id" class="form-label">{{ __('Raza Id') }}</label>
            <input type="text" name="raza_id" class="form-control @error('raza_id') is-invalid @enderror" value="{{ old('raza_id', $animalFile?->raza_id) }}" id="raza_id" placeholder="Raza Id">
            {!! $errors->first('raza_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="estado_id" class="form-label">{{ __('Estado Id') }}</label>
            <input type="text" name="estado_id" class="form-control @error('estado_id') is-invalid @enderror" value="{{ old('estado_id', $animalFile?->estado_id) }}" id="estado_id" placeholder="Estado Id">
            {!! $errors->first('estado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="adopcion_id" class="form-label">{{ __('Adopcion Id') }}</label>
            <input type="text" name="adopcion_id" class="form-control @error('adopcion_id') is-invalid @enderror" value="{{ old('adopcion_id', $animalFile?->adopcion_id) }}" id="adopcion_id" placeholder="Adopcion Id">
            {!! $errors->first('adopcion_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="liberacion_id" class="form-label">{{ __('Liberacion Id') }}</label>
            <input type="text" name="liberacion_id" class="form-control @error('liberacion_id') is-invalid @enderror" value="{{ old('liberacion_id', $animalFile?->liberacion_id) }}" id="liberacion_id" placeholder="Liberacion Id">
            {!! $errors->first('liberacion_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>