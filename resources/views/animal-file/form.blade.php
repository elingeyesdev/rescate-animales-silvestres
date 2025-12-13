<div class="padding-1 p-1">
    {!! $errors->first('general', '<div class="alert alert-danger" role="alert"><strong>:message</strong></div>') !!}
    @if(($showAnimalSelect ?? true))
        <div class="form-group mb-2">
            <label for="animal_id" class="form-label">{{ __('Animal') }} <span class="text-danger">*</span></label>
            <select name="animal_id" id="animal_id" class="form-control @error('animal_id') is-invalid @enderror">
                <option value="">{{ __('Seleccione') }}</option>
                @foreach(($animals ?? []) as $a)
                    <option value="{{ $a->id }}" {{ (string)old('animal_id', $animalFile?->animal_id) === (string)$a->id ? 'selected' : '' }}>N°{{ $a->id }} {{ $a->nombre ? '- ' . $a->nombre : '' }}</option>
                @endforeach
            </select>
            {!! $errors->first('animal_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    @endif

    <div class="form-group mb-2">
        <label for="especie_id" class="form-label">{{ __('Especie') }} <span class="text-danger">*</span></label>
        <select name="especie_id" id="especie_id" class="form-control @error('especie_id') is-invalid @enderror">
            @foreach(($species ?? []) as $s)
                <option value="{{ $s->id }}" {{ (string)old('especie_id', $animalFile?->especie_id) === (string)$s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
            @endforeach
        </select>
        {!! $errors->first('especie_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
    </div>

    <div class="form-group mb-2">
        <label for="imagen" class="form-label">{{ __('Imagen') }}</label>
        <div class="custom-file">
                <input type="file" accept="image/jpeg,image/jpg,image/png" name="imagen" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen">
                <label class="custom-file-label" for="imagen" data-browse="Subir">Subir la imagen del animal</label>
            </div>
        {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
        @php($initialImg = !empty($animalFile?->imagen_url) ? asset('storage/' . $animalFile->imagen_url) : null)
        <div class="mt-2">
            <img id="preview-animalfile-imagen" src="{{ $initialImg }}" alt="Imagen" style="max-height: 120px; {{ empty($initialImg) ? 'display:none;' : '' }}"/>
        </div>
    </div>

    

    @if(empty($hideState))
        <div class="form-group mb-2">
            <label for="estado_id" class="form-label">{{ __('Estado') }} <span class="text-danger">*</span></label>
            <select name="estado_id" id="estado_id" class="form-control @error('estado_id') is-invalid @enderror">
                @foreach(($animalStatuses ?? []) as $st)
                    <option value="{{ $st->id }}" {{ (string)old('estado_id', $animalFile?->estado_id) === (string)$st->id || ((!old('estado_id', $animalFile?->estado_id)) && empty($animalFile?->estado_id) && mb_strtolower($st->nombre) === 'en recuperación') ? 'selected' : '' }}>{{ $st->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('estado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    @endif

    @if(($showSubmit ?? true))
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
        </div>
    @endif
</div>

@include('partials.custom-file')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('imagen');
    const img = document.getElementById('preview-animalfile-imagen');
    if (input && img) {
        input.addEventListener('change', function(){
            const file = this.files && this.files[0] ? this.files[0] : null;
            if (file && file.type.startsWith('image/') && file.type !== 'image/webp') {
                img.src = URL.createObjectURL(file);
                img.style.display = '';
            } else if (file && file.type === 'image/webp') {
                alert('El formato de imagen .webp no está permitido. Por favor, usa JPG, JPEG o PNG.');
                this.value = '';
            }
        });
    }
});
</script>
