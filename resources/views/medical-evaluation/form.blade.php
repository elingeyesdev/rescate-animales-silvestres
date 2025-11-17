<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="tratamiento_id" class="form-label">{{ __('Tipo de Tratamiento') }}</label>
            <select name="tratamiento_id" id="tratamiento_id" class="form-control @error('tratamiento_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($treatmentTypes ?? []) as $t)
                    <option value="{{ $t->id }}" {{ (string)old('tratamiento_id', $medicalEvaluation?->tratamiento_id) === (string)$t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('tratamiento_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="descripcion" class="form-label">{{ __('Descripcion') }}</label>
            <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $medicalEvaluation?->descripcion) }}" id="descripcion" placeholder="Descripcion">
            {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="fecha" class="form-label">{{ __('Fecha') }}</label>
            <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $medicalEvaluation?->fecha) }}" id="fecha">
            {!! $errors->first('fecha', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="veterinario_id" class="form-label">{{ __('Veterinario') }}</label>
            <select name="veterinario_id" id="veterinario_id" class="form-control @error('veterinario_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($veterinarians ?? []) as $v)
                    <option value="{{ $v->id }}" {{ (string)old('veterinario_id', $medicalEvaluation?->veterinario_id) === (string)$v->id ? 'selected' : '' }}>{{ $v->person->nombre ?? ('#'.$v->id) }}</option>
                @endforeach
            </select>
            {!! $errors->first('veterinario_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="imagen" class="form-label">{{ __('Imagen (opcional)') }}</label>
            <input type="file" name="imagen" id="imagen" class="form-control @error('imagen') is-invalid @enderror" accept="image/*">
            {!! $errors->first('imagen', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            @if(!empty($medicalEvaluation?->imagen_url))
                <div class="mt-2">
                    <img src="{{ $medicalEvaluation->imagen_url }}" alt="Imagen evaluación" style="max-height:120px;">
                </div>
            @endif
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>