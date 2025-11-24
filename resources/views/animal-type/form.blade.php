<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $animalType?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label class="form-label d-block">{{ __('Permite Adopción') }}</label>
            <input type="hidden" name="permite_adopcion" value="0">
            <div class="icheck-primary d-inline mr-3">
                @php($adop = old('permite_adopcion', $animalType?->permite_adopcion))
                <input type="radio" id="permite_adopcion_si" name="permite_adopcion" value="1" {{ (string)$adop === '1' ? 'checked' : '' }}>
                <label for="permite_adopcion_si">Sí</label>
            </div>
            <div class="icheck-primary d-inline">
                <input type="radio" id="permite_adopcion_no" name="permite_adopcion" value="0" {{ (string)$adop === '0' ? 'checked' : '' }}>
                <label for="permite_adopcion_no">No</label>
            </div>
            {!! $errors->first('permite_adopcion', '<div class="text-danger small" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label class="form-label d-block">{{ __('Permite Liberación') }}</label>
            <input type="hidden" name="permite_liberacion" value="0">
            <div class="icheck-primary d-inline mr-3">
                @php($lib = old('permite_liberacion', $animalType?->permite_liberacion))
                <input type="radio" id="permite_liberacion_si" name="permite_liberacion" value="1" {{ (string)$lib === '1' ? 'checked' : '' }}>
                <label for="permite_liberacion_si">Sí</label>
            </div>
            <div class="icheck-primary d-inline">
                <input type="radio" id="permite_liberacion_no" name="permite_liberacion" value="0" {{ (string)$lib === '0' ? 'checked' : '' }}>
                <label for="permite_liberacion_no">No</label>
            </div>
            {!! $errors->first('permite_liberacion', '<div class="text-danger small" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const adopSi = document.getElementById('permite_adopcion_si');
            const adopNo = document.getElementById('permite_adopcion_no');
            const libSi = document.getElementById('permite_liberacion_si');
            const libNo = document.getElementById('permite_liberacion_no');

            function enforcePairing(from) {
                // Regla: exactamente uno en "Sí" y el otro en "No"
                if (from === 'adopSi' && adopSi.checked) {
                    libNo.checked = true;
                } else if (from === 'adopNo' && adopNo.checked) {
                    libSi.checked = true;
                } else if (from === 'libSi' && libSi.checked) {
                    adopNo.checked = true;
                } else if (from === 'libNo' && libNo.checked) {
                    adopSi.checked = true;
                }
                // Si por alguna razón quedan ambos en No o ambos en Sí, forzar adopción = Sí, liberación = No
                if ((adopNo.checked && libNo.checked) || (adopSi.checked && libSi.checked)) {
                    adopSi.checked = true;
                    libNo.checked = true;
                }
            }

            // Eventos
            adopSi?.addEventListener('change', () => enforcePairing('adopSi'));
            adopNo?.addEventListener('change', () => enforcePairing('adopNo'));
            libSi?.addEventListener('change', () => enforcePairing('libSi'));
            libNo?.addEventListener('change', () => enforcePairing('libNo'));

            // Estado inicial: si nada está marcado o ambos están en el mismo valor, fijar adopción = Sí, liberación = No
            const noneChecked = !(adopSi.checked || adopNo.checked || libSi.checked || libNo.checked);
            const sameChoice = (adopSi.checked && libSi.checked) || (adopNo.checked && libNo.checked);
            if (noneChecked || sameChoice) {
                adopSi.checked = true;
                libNo.checked = true;
            } else {
                // Asegurar consistencia por si solo un lado fue seteado
                enforcePairing(null);
            }
        });
        </script>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>