<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="animal_id" class="form-label">{{ __('Animal') }}</label>
            <select name="animal_id" id="animal_id" class="form-control @error('animal_id') is-invalid @enderror">
                <option value="">{{ __('Seleccione') }}</option>
                @foreach(($animals ?? []) as $a)
                    <option value="{{ $a->id }}" {{ (string)old('animal_id', $animalFile?->animal_id) === (string)$a->id ? 'selected' : '' }}>
                        #{{ $a->id }} {{ $a->nombre ? '- ' . $a->nombre : '' }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('animal_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tipo_id" class="form-label">{{ __('Tipo de Animal') }}</label>
            <select name="tipo_id" id="tipo_id" class="form-control @error('tipo_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($animalTypes ?? []) as $t)
                    <option value="{{ $t->id }}" {{ (string)old('tipo_id', $animalFile?->tipo_id) === (string)$t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('tipo_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        
        <div class="form-group mb-2 mb20">
            <label for="especie_id" class="form-label">{{ __('Especie') }}</label>
            <select name="especie_id" id="especie_id" class="form-control @error('especie_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($species ?? []) as $s)
                    <option value="{{ $s->id }}" {{ (string)old('especie_id', $animalFile?->especie_id) === (string)$s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('especie_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="imagen" class="form-label">{{ __('Imagen') }}</label>
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" accept="image/*" name="imagen" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen">
                    <label class="custom-file-label" for="imagen">{{ __('Elegir imagen') }}</label>
                </div>
            </div>
            {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
            @if(!empty($animalFile?->imagen_url))
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $animalFile->imagen_url) }}" alt="Imagen" style="max-height: 120px;"/>
                </div>
            @endif
        </div>
        <div class="form-group mb-2 mb20">
            <label for="raza_id" class="form-label">{{ __('Raza') }}</label>
            <select name="raza_id" id="raza_id" class="form-control @error('raza_id') is-invalid @enderror">
                <option value="">Seleccione especie primero</option>
                @isset($breeds)
                    @foreach($breeds as $b)
                        <option value="{{ $b->id }}" {{ (string)old('raza_id', $animalFile?->raza_id) === (string)$b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
                    @endforeach
                @endisset
            </select>
            {!! $errors->first('raza_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="estado_id" class="form-label">{{ __('Estado') }}</label>
            <select name="estado_id" id="estado_id" class="form-control @error('estado_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($animalStatuses ?? []) as $st)
                    <option value="{{ $st->id }}" {{ (string)old('estado_id', $animalFile?->estado_id) === (string)$st->id ? 'selected' : '' }}>{{ $st->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('estado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        @if(!empty($animalFile?->id))
        <div class="form-group mb-2 mb20">
            <label for="adopcion_id" class="form-label">{{ __('Adopción (opcional)') }}</label>
            <select name="adopcion_id" id="adopcion_id" class="form-control @error('adopcion_id') is-invalid @enderror">
                <option value="">Ninguna</option>
                @foreach(($adoptions ?? []) as $a)
                    <option value="{{ $a->id }}" {{ (string)old('adopcion_id', $animalFile?->adopcion_id) === (string)$a->id ? 'selected' : '' }}>#{{ $a->id }}</option>
                @endforeach
            </select>
            {!! $errors->first('adopcion_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="liberacion_id" class="form-label">{{ __('Liberación (opcional)') }}</label>
            <select name="liberacion_id" id="liberacion_id" class="form-control @error('liberacion_id') is-invalid @enderror">
                <option value="">Ninguna</option>
                @foreach(($releases ?? []) as $rl)
                    <option value="{{ $rl->id }}" {{ (string)old('liberacion_id', $animalFile?->liberacion_id) === (string)$rl->id ? 'selected' : '' }}>#{{ $rl->id }}</option>
                @endforeach
            </select>
            {!! $errors->first('liberacion_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        @endif

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('imagen');
    input?.addEventListener('change', function(){
        const fileName = this.files && this.files[0] ? this.files[0].name : '{{ __('Elegir imagen') }}';
        const label = this.nextElementSibling;
        if (label) label.textContent = fileName;
    });
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
            data.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.textContent = b.nombre;
                if (String(selected) === String(b.id)) opt.selected = true;
                razaSelect.appendChild(opt);
            });
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