<div class="padding-1 p-1">
    {!! $errors->first('general', '<div class="alert alert-danger" role="alert"><strong>:message</strong></div>') !!}
    @if(($showAnimalSelect ?? true))
        <div class="form-group mb-2">
            <label for="animal_id" class="form-label">{{ __('Animal') }}</label>
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
        <label for="animal_nombre" class="form-label">{{ __('Nombre') }}</label>
        <input type="text" name="animal_nombre" id="animal_nombre" class="form-control @error('animal_nombre') is-invalid @enderror" value="{{ old('animal_nombre', $animalFile?->animal?->nombre) }}" placeholder="Nombre del animal">
        {!! $errors->first('animal_nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
    </div>

    @if(empty($hideType))
        <div class="form-group mb-2">
            <label for="tipo_id" class="form-label">{{ __('Tipo de Animal') }}</label>
            <select name="tipo_id" id="tipo_id" class="form-control @error('tipo_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($animalTypes ?? []) as $t)
                    @if(!Str::contains(mb_strtolower($t->nombre), 'domést'))
                        <option value="{{ $t->id }}" {{ (string)old('tipo_id', $animalFile?->tipo_id) === (string)$t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                    @endif
                @endforeach
            </select>
            {!! $errors->first('tipo_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    @else
        <input type="hidden" name="tipo_id" value="{{ $defaultTypeId ?? old('tipo_id', $animalFile?->tipo_id) }}">
    @endif

    <div class="form-group mb-2">
        <label for="especie_id" class="form-label">{{ __('Especie') }}</label>
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
                <input type="file" accept="image/*" name="imagen" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen">
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
            <label for="estado_id" class="form-label">{{ __('Estado') }}</label>
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
            if (file && file.type.startsWith('image/')) {
                img.src = URL.createObjectURL(file);
                img.style.display = '';
            }
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const especieSelect = document.getElementById('especie_id');
    const razaSelect = document.getElementById('raza_id');

    especieSelect?.addEventListener('change', async function() {
        const speciesId = this.value;
        razaSelect.innerHTML = '<option value="">Cargando...</option>';
        if (!speciesId) { razaSelect.innerHTML = '<option value="">Seleccione especie primero</option>'; return; }
        try {
            const resp = await fetch(`{{ route('breeds.bySpecies', ['species' => 'ID']) }}`.replace('ID', speciesId));
            const data = await resp.json();
            razaSelect.innerHTML = '<option value="">Seleccione</option>';
            const selected = '{{ old('raza_id', $animalFile?->raza_id) }}';
            let hasUnknown = false;
            data.forEach(b => {
                const nameLower = String(b.nombre || '').toLowerCase();
                if (nameLower.includes('desconoc')) hasUnknown = true;
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.textContent = b.nombre;
                if (String(selected) === String(b.id)) opt.selected = true;
                razaSelect.appendChild(opt);
            });
            if (!hasUnknown) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'Desconocido (no definido)';
                razaSelect.appendChild(opt);
            }
        } catch (e) {
            razaSelect.innerHTML = '<option value="">Error al cargar</option>';
        }
    });

    if (especieSelect && especieSelect.value) {
        const event = new Event('change');
        especieSelect.dispatchEvent(event);
    }
});
</script>