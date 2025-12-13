<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="persona_id" class="form-label">{{ __('Persona que traslada') }}</label>
            <select name="persona_id" id="persona_id" class="form-control @error('persona_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach(($people ?? []) as $p)
                    <option value="{{ $p->id }}" {{ (string)old('persona_id', $transfer?->persona_id) === (string)$p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('persona_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="animal_file_id" class="form-label">{{ __('Hoja del animal') }} <span class="text-danger">*</span></label>
            <select name="animal_file_id" id="animal_file_id" class="form-control @error('animal_file_id') is-invalid @enderror">
                <option value="">{{ __('Seleccione') }}</option>
                @foreach(($animalFiles ?? []) as $af)
                    <option value="{{ $af->id }}" data-animal-id="{{ $af->animal_id }}" {{ (string)old('animal_file_id') === (string)$af->id ? 'selected' : '' }}>
                        #{{ $af->id }} {{ $af->animal?->nombre ?? '' }} (Animal #{{ $af->animal_id }})
                    </option>
                @endforeach
            </select>
            {!! $errors->first('animal_file_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            <input type="hidden" name="animal_id" id="animal_id" value="{{ old('animal_id', $transfer?->animal_id) }}">
        </div>

        

        <div class="form-group mb-2 mb20" id="centers_map_wrap" style="display:none;">
            <label class="form-label">{{ __('Seleccione el centro de destino en el mapa') }} <span class="text-danger">*</span></label>
            <div id="centers_map" style="height: 280px; border-radius: 4px; margin-bottom: 8px;"></div>
            <input type="hidden" name="centro_id" id="centro_id" value="{{ old('centro_id', $transfer?->centro_id) }}">
            <div id="centers_legend" class="small text-muted"></div>
            {!! $errors->first('centro_id', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="observaciones" class="form-label">{{ __('Observaciones') }}</label>
            <input type="text" name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" value="{{ old('observaciones', $transfer?->observaciones) }}" id="observaciones" placeholder="Observaciones">
            {!! $errors->first('observaciones', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>
@include('partials.leaflet')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const animalFileSel = document.getElementById('animal_file_id');
    const currentWrap = document.getElementById('current_center_wrap');
    const currentName = document.getElementById('current_center_name');
    const centersWrap = document.getElementById('centers_map_wrap');
    const centersMapEl = document.getElementById('centers_map');
    const centersLegend = document.getElementById('centers_legend');
    const centerInput = document.getElementById('centro_id');
    const hiddenAnimalId = document.getElementById('animal_id');

    let centersMap = null;

    function renderCentersMap(destinations, current) {
        centersWrap.style.display = '';
        if (!centersMap) {
            centersMap = L.map('centers_map').setView([ -17.7833, -63.1821 ], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(centersMap);
        }
        // clear existing markers by resetting the map layer
        centersMap.eachLayer(function (layer) {
            if (layer instanceof L.Marker) centersMap.removeLayer(layer);
        });
        centersLegend.innerHTML = '';

        if (current && current.latitud && current.longitud) {
            const cur = L.marker([current.latitud, current.longitud]).addTo(centersMap);
            cur.bindPopup(`{{ __('Centro actual') }}: <strong>${current.nombre}</strong>`).openPopup();
            centersMap.setView([current.latitud, current.longitud], 13);
            currentName.textContent = `#${current.id} ${current.nombre}`;
            currentWrap.style.display = '';
        } else {
            currentWrap.style.display = 'none';
        }

        destinations.forEach(c => {
            if (c.latitud && c.longitud) {
                const m = L.marker([c.latitud, c.longitud]).addTo(centersMap);
                m.bindPopup(`<strong>${c.nombre}</strong>`);
                m.on('click', () => {
                    centerInput.value = c.id;
                    highlightLegend(c.id);
                });
                // legend item
                const span = document.createElement('span');
                span.textContent = `#${c.id} ${c.nombre}`;
                span.style.cursor = 'pointer';
                span.style.display = 'inline-block';
                span.style.marginRight = '10px';
                span.id = `legend_center_${c.id}`;
                span.onclick = () => { centersMap.setView([c.latitud, c.longitud], 15); centerInput.value = c.id; highlightLegend(c.id); };
                centersLegend.appendChild(span);
            }
        });

        function highlightLegend(id){
            destinations.forEach(c => {
                const el = document.getElementById(`legend_center_${c.id}`);
                if (el) el.style.fontWeight = (String(c.id) === String(id)) ? '700' : '400';
            });
        }
    }

    function onAnimalChange(){
        const sel = animalFileSel;
        const id = sel?.selectedOptions?.[0]?.getAttribute('data-animal-id') || '';
        if (hiddenAnimalId) hiddenAnimalId.value = id || '';
        centerInput.value = '';
        if (!id) { currentName.textContent = ''; currentWrap.style.display = 'none'; centersWrap.style.display = 'none'; return; }
        const url = new URL('{{ route('transfers.index') }}', window.location.origin);
        url.searchParams.set('current_center', '1');
        url.searchParams.set('animal_id', id);
        fetch(url.toString())
            .then(r => r.json())
            .then(data => {
                renderCentersMap(data.destinations || [], data.current || null);
            })
            .catch(() => { currentWrap.style.display = 'none'; centersWrap.style.display = 'none'; });
    }

    animalFileSel?.addEventListener('change', onAnimalChange);
    // init if preselected
    if (animalFileSel && animalFileSel.value) { onAnimalChange(); }
});
</script>