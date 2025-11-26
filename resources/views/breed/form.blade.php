<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="especie_id" class="form-label">{{ __('Especie') }}</label>
            <select name="especie_id" id="especie_id" class="form-control @error('especie_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($species ?? []) as $s)
                    <option value="{{ $s->id }}" {{ (string)old('especie_id', $breed?->especie_id) === (string)$s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('especie_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $breed?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>