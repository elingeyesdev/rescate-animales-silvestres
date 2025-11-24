<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        @if(!empty($report?->id))
        <div class="form-group mb-2 mb20">
            <label for="aprobado" class="form-label">{{ __('Aprobado') }}</label>
            <select name="aprobado" id="aprobado" class="form-control @error('aprobado') is-invalid @enderror">
                @php($ap = old('aprobado', $report?->aprobado))
                <option value="0" {{ (string)$ap === '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ (string)$ap === '1' ? 'selected' : '' }}>Sí</option>
            </select>
            {!! $errors->first('aprobado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="direccion" class="form-label">{{ __('Dirección') }}</label>
            <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $report?->direccion) }}" id="direccion" placeholder="Dirección">
            {!! $errors->first('direccion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        @endif

        <div class="form-group mb-2 mb20">
            <label for="imagen" class="form-label">{{ __('Imagen') }}</label>
            <input type="file" accept="image/*" name="imagen" class="form-control @error('imagen') is-invalid @enderror" id="imagen">
            {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
            @if(!empty($report?->imagen_url))
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $report->imagen_url) }}" alt="Imagen del reporte" style="max-height: 120px;"/>
                </div>
            @endif
        </div>

        <div class="form-group mb-2 mb20">
            <label for="observaciones" class="form-label">{{ __('Observaciones') }}</label>
            <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" rows="3" placeholder="Observaciones">{{ old('observaciones', $report?->observaciones) }}</textarea>
            {!! $errors->first('observaciones', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="cantidad_animales" class="form-label">{{ __('Cantidad de Animales') }}</label>
            <input type="number" min="1" name="cantidad_animales" class="form-control @error('cantidad_animales') is-invalid @enderror" value="{{ old('cantidad_animales', $report?->cantidad_animales ?? 1) }}" id="cantidad_animales" placeholder="1">
            {!! $errors->first('cantidad_animales', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        @if(empty($report?->id))
        <div class="form-group mb-2 mb20">
            <label class="form-label">{{ __('Ubicación (clic en el mapa)') }}</label>
            <div class="row">
                <div class="col-12">
                    <div id="mapid" style="height: 360px; border-radius: 4px;"></div>
                </div>
            </div>
            <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud') }}">
            <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud') }}">
            <input type="hidden" name="direccion" id="direccion" value="{{ old('direccion') }}">
            <small class="text-muted" id="direccion_text"></small>
        </div>

        <div class="form-group mb-2 mb20">
            <label for="traslado_inmediato" class="form-label d-block">{{ __('¿Se realizará traslado inmediato?') }}</label>
            <select name="traslado_inmediato" id="traslado_inmediato" class="form-control @error('traslado_inmediato') is-invalid @enderror" style="max-width:260px;">
                <option value="0" {{ old('traslado_inmediato','0')=='0'?'selected':'' }}>{{ __('No') }}</option>
                <option value="1" {{ old('traslado_inmediato')=='1'?'selected':'' }}>{{ __('Sí') }}</option>
            </select>
            {!! $errors->first('traslado_inmediato', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20" id="centro_wrap" style="display:none;">
            <label class="form-label">{{ __('Seleccione el centro de destino en el mapa') }}</label>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div id="centers_map" style="height: 280px; border-radius: 4px; margin-bottom: 8px;"></div>
                </div>
            </div>
            <input type="hidden" name="centro_id" id="centro_id" value="{{ old('centro_id') }}">
            <div id="centers_legend" class="small text-muted"></div>
            {!! $errors->first('centro_id', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
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
        const file = this.files && this.files[0] ? this.files[0] : null;
        const fileName = file ? file.name : '{{ __('Elegir imagen') }}';
        const label = this.nextElementSibling;
        if (label) label.textContent = fileName;
        let preview = document.getElementById('imagen_preview');
        if (!preview) {
            const container = this.closest('.form-group');
            preview = document.createElement('img');
            preview.id = 'imagen_preview';
            preview.style.maxHeight = '120px';
            preview.style.display = 'none';
            preview.className = 'mt-2';
            container.appendChild(preview);
        }
        if (file && file.type.startsWith('image/')) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        } else if (preview) {
            preview.style.display = 'none';
        }
    });
});
</script>

@if(empty($report?->id))
@include('partials.leaflet')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Mapa de ubicación del hallazgo (con geolocalización)
    window.initMapWithGeolocation({
        mapId: 'mapid',
        latInputId: 'latitud',
        lonInputId: 'longitud',
        dirInputId: 'direccion',
        start: { lat: -17.7833, lon: -63.1821, zoom: 13 },
        enableReverseGeocode: true,
    });

    const sel = document.getElementById('traslado_inmediato');
    const wrap = document.getElementById('centro_wrap');
    let centersMap = null;
    let centersMarkers = [];

    function initCentersMap() {
        if (centersMap) return;
        const centersData = @json($centers);
        centersMap = L.map('centers_map').setView([ -17.7833, -63.1821 ], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(centersMap);
        const legend = document.getElementById('centers_legend');
        const selectedInput = document.getElementById('centro_id');
        centersData.forEach((c) => {
            if (c.latitud && c.longitud) {
                const m = L.marker([c.latitud, c.longitud]).addTo(centersMap);
                m.bindPopup(`<strong>${c.nombre}</strong>`);
                m.on('click', () => {
                    selectedInput.value = c.id;
                    highlightLegend(c.id);
                });
                centersMarkers.push({ id: c.id, marker: m });
            }
        });
        // build legend
        if (legend) {
            legend.innerHTML = '';
            centersData.forEach(c => {
                const span = document.createElement('span');
                span.textContent = `#${c.id} ${c.nombre}`;
                span.style.cursor = 'pointer';
                span.style.display = 'inline-block';
                span.style.marginRight = '10px';
                span.onclick = () => {
                    if (c.latitud && c.longitud) {
                        centersMap.setView([c.latitud, c.longitud], 15);
                        document.getElementById('centro_id').value = c.id;
                        highlightLegend(c.id);
                    }
                };
                span.id = `legend_center_${c.id}`;
                legend.appendChild(span);
            });
        }
        function highlightLegend(id){
            centersData.forEach(c => {
                const el = document.getElementById(`legend_center_${c.id}`);
                if (el) el.style.fontWeight = (String(c.id) === String(id)) ? '700' : '400';
            });
        }
    }

    function toggle() {
        if (!sel || !wrap) return;
        const show = String(sel.value) === '1';
        wrap.style.display = show ? '' : 'none';
        if (show) initCentersMap();
    }
    sel?.addEventListener('change', toggle);
    toggle();
});
</script>
@endif