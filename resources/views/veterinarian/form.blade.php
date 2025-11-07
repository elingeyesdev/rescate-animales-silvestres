<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="persona_id" class="form-label">{{ __('Persona') }}</label>
            <select name="persona_id" id="persona_id" class="form-control @error('persona_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($people ?? []) as $p)
                    <option value="{{ $p->id }}" {{ (string)old('persona_id', $veterinarian?->persona_id) === (string)$p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('persona_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="especialidad" class="form-label">{{ __('Especialidad') }}</label>
            <input type="text" name="especialidad" class="form-control @error('especialidad') is-invalid @enderror" value="{{ old('especialidad', $veterinarian?->especialidad) }}" id="especialidad" placeholder="Especialidad">
            {!! $errors->first('especialidad', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="cv_documentado" class="form-label d-block">{{ __('CV Documentado') }}</label>
            <div class="icheck-primary d-inline mr-3">
                <input type="radio" id="cv_si" name="cv_documentado" value="1" {{ (string)old('cv_documentado', $veterinarian?->cv_documentado) === '1' ? 'checked' : '' }}>
                <label for="cv_si">Sí</label>
            </div>
            <div class="icheck-primary d-inline">
                <input type="radio" id="cv_no" name="cv_documentado" value="0" {{ (string)old('cv_documentado', $veterinarian?->cv_documentado) === '0' ? 'checked' : '' }}>
                <label for="cv_no">No</label>
            </div>
            {!! $errors->first('cv_documentado', '<div class="text-danger small" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="cv" class="form-label">{{ __('Archivo CV (PDF/DOC)') }}</label>
            <input type="file" name="cv" accept=".pdf,.doc,.docx" class="form-control-file @error('cv') is-invalid @enderror" id="cv">
            {!! $errors->first('cv', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
            @if(!empty($veterinarian?->cv_path))
                <div class="mt-2"><a href="{{ asset('storage/' . $veterinarian->cv_path) }}" target="_blank">{{ __('Ver CV actual') }}</a></div>
            @endif
        </div>
        

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>