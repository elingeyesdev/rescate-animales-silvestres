<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $animalCondition?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="severidad" class="form-label">{{ __('Severidad') }}</label>
            @php($sev = old('severidad', $animalCondition?->severidad))
            <select name="severidad" id="severidad" class="form-control @error('severidad') is-invalid @enderror">
                <option value="1" {{ (string)$sev==='1'?'selected':'' }}>1</option>
                <option value="2" {{ (string)$sev==='2'?'selected':'' }}>2</option>
                <option value="3" {{ (string)$sev==='3'?'selected':'' }}>3</option>
                <option value="4" {{ (string)$sev==='4'?'selected':'' }}>4</option>
                <option value="5" {{ (string)$sev==='5'?'selected':'' }}>5</option>
            </select>
            {!! $errors->first('severidad', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="activo" class="form-label">{{ __('Activo') }}</label>
            @php($a = old('activo', $animalCondition?->activo))
            <select name="activo" id="activo" class="form-control @error('activo') is-invalid @enderror">
                <option value="1" {{ (string)$a==='1'?'selected':'' }}>{{ __('Sí') }}</option>
                <option value="0" {{ (string)$a==='0'?'selected':'' }}>{{ __('No') }}</option>
            </select>
            {!! $errors->first('activo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

