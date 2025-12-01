<div class="row padding-1 p-1 ">
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
            <select name="veterinario_id" id="veterinario_id" class="form-control @error('veterinario_id') is-invalid @enderror" aria-describedby="vet_specialty_hint">
                <option value="">{{ __('Seleccione') }}</option>
                @foreach(($veterinarians ?? []) as $v)
                    <option value="{{ $v->id }}" data-especialidad="{{ $v->especialidad }}" {{ (string)old('veterinario_id', $medicalEvaluation?->veterinario_id) === (string)$v->id ? 'selected' : '' }}>{{ $v->person->nombre ?? ('#'.$v->id) }}@if($v->especialidad) ({{ $v->especialidad }}) @endif</option>
                @endforeach
            </select>
            <small id="vet_specialty_hint" class="form-text text-muted"></small>
            <div id="vet_selected_summary" class="form-text text-muted"></div>
            {!! $errors->first('veterinario_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        @php
            $rep = $medicalEvaluation?->animalFile?->animal?->report ?? null;
            $arriveStatus = $medicalEvaluation?->animalFile?->animalStatus?->nombre ?? null;
            $foundImg = $rep?->imagen_url ?? null;
            $prevText = $rep?->observaciones ?? null;
        @endphp
        <div class="form-group mb-2 mb20">
            <label class="form-label">{{ __('Estado anterior (reporte)') }}</label>
            <div>{{ $prevText ?? '-' }}</div>
        </div>
        <div class="form-group mb-2 mb20">
            <label class="form-label">{{ __('Estado al llegar (hoja)') }}</label>
            <div>{{ $arriveStatus ?? '-' }}</div>
        </div>
        <div class="form-group mb-2 mb20">
            <label class="form-label">{{ __('Imagen de llegada') }}</label>
            @if($foundImg)
                <div class="mt-2">
                    <a href="{{ asset('storage/' . $foundImg) }}" target="_blank" rel="noopener">
                        <img src="{{ asset('storage/' . $foundImg) }}" alt="Imagen de llegada" style="max-height:120px;">
                    </a>
                </div>
            @else
                -
            @endif
        </div>
        <div class="form-group mb-2 mb20">
            <label for="imagen" class="form-label">{{ __('Imagen') }}</label>
            <div class="custom-file">
                <input type="file" accept="image/jpeg,image/jpg,image/png" name="imagen" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen">
                <label class="custom-file-label" for="imagen" data-browse="Subir">Subir la imagen del animal</label>
            </div>
            {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
            @php
                $initialEvalSrc = !empty($medicalEvaluation?->imagen_url) ? asset('storage/' . $medicalEvaluation->imagen_url) : null;
            @endphp
            <div class="mt-2">
                <img id="preview-eval-imagen" src="{{ $initialEvalSrc }}" alt="Imagen evaluación" style="max-height:120px; {{ empty($initialEvalSrc) ? 'display:none;' : '' }}">
            </div>
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>
@include('partials.custom-file')
<script>
document.addEventListener('DOMContentLoaded', function () {
  var input = document.getElementById('imagen');
  var img = document.getElementById('preview-eval-imagen');
  var currentObjectURL = null;
  if (input && img) {
    input.addEventListener('change', function () {
      var file = this.files && this.files[0];
      if (file && file.type && file.type.startsWith('image/') && file.type !== 'image/webp') {
        if (currentObjectURL) {
          URL.revokeObjectURL(currentObjectURL);
        }
        currentObjectURL = URL.createObjectURL(file);
        img.src = currentObjectURL;
        img.style.display = '';
      } else if (file && file.type === 'image/webp') {
        alert('El formato de imagen .webp no está permitido. Por favor, usa JPG, JPEG o PNG.');
        this.value = '';
        return;
      } else {
        if (currentObjectURL) {
          URL.revokeObjectURL(currentObjectURL);
          currentObjectURL = null;
        }
        img.removeAttribute('src');
        img.style.display = 'none';
      }
    });
  }
  var vetSel = document.getElementById('veterinario_id');
  var hint = document.getElementById('vet_specialty_hint');
  var summary = document.getElementById('vet_selected_summary');
  var updateHint = function () {
    var opt = vetSel && vetSel.selectedOptions && vetSel.selectedOptions[0];
    var esp = opt ? (opt.getAttribute('data-especialidad') || '') : '';
    
  };
  var updateSummary = function () {
    var opt = vetSel && vetSel.selectedOptions && vetSel.selectedOptions[0];
    var name = opt ? (opt.textContent || '') : '';
    if (summary) {
      summary.textContent = name ? ('Seleccionado: ' + name) : '';
    }
  };
  if (vetSel) {
    vetSel.addEventListener('change', function () { updateHint(); updateSummary(); });
    updateHint();
    updateSummary();
  }
});
</script>