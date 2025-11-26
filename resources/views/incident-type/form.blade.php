<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $incidentType?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="riesgo" class="form-label">{{ __('Nivel de riesgo') }}</label>
            @php($r = old('riesgo', $incidentType?->riesgo))
            <select name="riesgo" id="riesgo" class="form-control @error('riesgo') is-invalid @enderror">
                <option value="0" {{ (string)$r==='0'?'selected':'' }}>{{ __('Bajo') }}</option>
                <option value="1" {{ (string)$r==='1'?'selected':'' }}>{{ __('Medio') }}</option>
                <option value="2" {{ (string)$r==='2'?'selected':'' }}>{{ __('Alto') }}</option>
            </select>
            {!! $errors->first('riesgo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="activo" class="form-label">{{ __('Activo') }}</label>
            @php($a = old('activo', $incidentType?->activo))
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

