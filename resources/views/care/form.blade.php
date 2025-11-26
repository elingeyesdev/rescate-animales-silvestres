<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="hoja_animal_id" class="form-label">{{ __('Animal') }}</label>
            <select name="hoja_animal_id" id="hoja_animal_id" class="form-control @error('hoja_animal_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($animalFiles ?? []) as $af)
                    <option value="{{ $af->id }}" {{ (string)old('hoja_animal_id', $care?->hoja_animal_id) === (string)$af->id ? 'selected' : '' }}>{{ $af->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('hoja_animal_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tipo_cuidado_id" class="form-label">{{ __('Tipo de Cuidado') }}</label>
            <select name="tipo_cuidado_id" id="tipo_cuidado_id" class="form-control @error('tipo_cuidado_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($careTypes ?? []) as $ct)
                    <option value="{{ $ct->id }}" {{ (string)old('tipo_cuidado_id', $care?->tipo_cuidado_id) === (string)$ct->id ? 'selected' : '' }}>{{ $ct->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('tipo_cuidado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="descripcion" class="form-label">{{ __('Descripcion') }}</label>
            <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $care?->descripcion) }}" id="descripcion" placeholder="Descripcion">
            {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <!-- Fecha: se asigna automáticamente por el sistema -->
        <div class="form-group mb-2 mb20">
            <label for="imagen" class="form-label">{{ __('Imagen (opcional)') }}</label>
            <div class="custom-file">
                <input type="file" name="imagen" id="imagen" class="custom-file-input @error('imagen') is-invalid @enderror" accept="image/*">
                <label class="custom-file-label" for="imagen">{{ __('Seleccionar imagen') }}</label>
            </div>
            {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
            @php
                $initialCareSrc = !empty($care?->imagen_url)
                    ? asset('storage/' . $care->imagen_url)
                    : null;
            @endphp
            <div class="mt-2">
                <img id="preview-care-imagen" src="{{ $initialCareSrc }}" alt="Imagen cuidado" style="max-height:120px; {{ empty($initialCareSrc) ? 'display:none;' : '' }}">
            </div>
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('imagen');
    var img = document.getElementById('preview-care-imagen');
    if (input && img) {
        input.addEventListener('change', function () {
            if (this.files && this.files[0] && this.files[0].type.startsWith('image/')) {
                var url = URL.createObjectURL(this.files[0]);
                img.src = url;
                img.style.display = '';
            }
        });
    }
});
</script>