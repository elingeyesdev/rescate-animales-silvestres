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
            <label for="cv" class="form-label">{{ __('Archivo CV (PDF/DOC o Imagen)') }}</label>
            <div class="custom-file">
                <input type="file" name="cv" accept=".pdf,.doc,.docx,image/*" class="custom-file-input @error('cv') is-invalid @enderror" id="cv">
                <label class="custom-file-label" for="cv">{{ __('Seleccionar archivo') }}</label>
            </div>
            {!! $errors->first('cv', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
            @if(!empty($veterinarian?->cv_documentado))
                <div class="mt-2"><a href="{{ asset('storage/' . $veterinarian->cv_documentado) }}" target="_blank">{{ __('Ver CV actual') }}</a></div>
            @endif
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('cv');
            input?.addEventListener('change', function(){
                const fileName = this.files && this.files[0] ? this.files[0].name : '{{ __('Seleccionar archivo') }}';
                const label = this.nextElementSibling;
                if (label) label.textContent = fileName;
            });
        });
        </script>
        <div class="form-group mb-2 mb20">
            <label for="aprobado" class="form-label">{{ __('Aprobado') }}</label>
            <select name="aprobado" id="aprobado" class="form-control @error('aprobado') is-invalid @enderror">
                <option value="">{{ __('Seleccione') }}</option>
                <option value="1" {{ (string)old('aprobado', $veterinarian?->aprobado) === '1' ? 'selected' : '' }}>{{ __('Sí') }}</option>
                <option value="0" {{ (string)old('aprobado', $veterinarian?->aprobado) === '0' ? 'selected' : '' }}>{{ __('No') }}</option>
            </select>
            {!! $errors->first('aprobado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="motivo_revision" class="form-label">{{ __('Motivo de aprobación/rechazo') }}</label>
            <textarea name="motivo_revision" id="motivo_revision" rows="3" class="form-control @error('motivo_revision') is-invalid @enderror" placeholder="{{ __('Motivo de la revisión') }}">{{ old('motivo_revision', $veterinarian?->motivo_revision) }}</textarea>
            {!! $errors->first('motivo_revision', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>