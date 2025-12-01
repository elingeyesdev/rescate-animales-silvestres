{{-- Partial reutilizable para mapa de selección de centros --}}
{{-- Parámetros esperados: $centers (array de centros), $mapId (string), $inputId (string), $selectedCenterId (int|null) --}}
@php
    $mapId = $mapId ?? 'centers_map';
    $inputId = $inputId ?? 'cuidador_center_id';
    $selectedCenterId = $selectedCenterId ?? null;
@endphp

<div class="form-group mb-3">
    <label class="form-label">{{ __('Selecciona un centro') }}</label>
    <div class="row">
        <div class="col-12">
            <div id="{{ $mapId }}" style="height: 360px; width: 100%; border-radius: 4px;"></div>
        </div>
    </div>
    <input type="hidden" name="{{ $inputId }}" id="{{ $inputId }}" value="{{ old($inputId, $selectedCenterId) }}">
    <small class="text-muted">Haz clic en un marcador del mapa para seleccionar el centro al que deseas acudir.</small>
</div>

<script>
(function() {
    // Esperar a que Leaflet esté disponible
    function initCentersMap() {
        if (typeof L === 'undefined') {
            setTimeout(initCentersMap, 100);
            return;
        }
        
        const mapEl = document.getElementById('{{ $mapId }}');
        if (!mapEl) return;
        
        // Evitar inicializar múltiples veces
        if (window['centersMap_{{ $mapId }}']) return;
        
        const centersData = @json($centers ?? []);
        const centersMap = L.map('{{ $mapId }}').setView([-17.7833, -63.1821], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(centersMap);
        
        // Guardar referencia global
        window['centersMap_{{ $mapId }}'] = centersMap;
        
        const selectedInput = document.getElementById('{{ $inputId }}');
        const centersMarkers = [];
        
        centersData.forEach((c) => {
            if (c.latitud && c.longitud) {
                const m = L.marker([c.latitud, c.longitud]).addTo(centersMap);
                m.bindTooltip(`${c.nombre}`, {
                    permanent: true,
                    direction: 'top',
                    offset: [0, -10],
                    opacity: 0.9,
                    className: 'center-tooltip'
                });
                m.on('click', () => {
                    selectedInput.value = c.id;
                    highlightMarker(c.id);
                });
                centersMarkers.push({ id: c.id, marker: m });
            }
        });

        function highlightMarker(id) {
            centersMarkers.forEach(obj => {
                const iconEl = obj.marker?._icon;
                if (!iconEl) return;
                iconEl.classList.remove('selected');
            });
            const selected = centersMarkers.find(obj => String(obj.id) === String(id));
            if (selected && selected.marker && selected.marker._icon) {
                selected.marker._icon.classList.add('selected');
            }
        }

        // Preseleccionar si hay un centro ya seleccionado
        const pre = selectedInput && selectedInput.value ? selectedInput.value : null;
        if (pre) {
            highlightMarker(pre);
            const chosen = centersData.find(cc => String(cc.id) === String(pre));
            if (chosen && chosen.latitud && chosen.longitud) {
                centersMap.setView([chosen.latitud, chosen.longitud], 15);
            }
        }
        
        // Guardar markers para acceso externo
        window['centersMarkers_{{ $mapId }}'] = centersMarkers;
    }

    // Inicializar cuando el DOM esté listo y el elemento sea visible
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initCentersMap, 300);
        });
    } else {
        setTimeout(initCentersMap, 300);
    }
    
    // Re-inicializar si el mapa se muestra después (cuando se marca el checkbox)
    const compromisoCheckbox = document.getElementById('compromiso_cuidador');
    if (compromisoCheckbox) {
        compromisoCheckbox.addEventListener('change', function() {
            if (this.checked) {
                setTimeout(function() {
                    const map = window['centersMap_{{ $mapId }}'];
                    if (map) {
                        map.invalidateSize();
                    } else {
                        initCentersMap();
                    }
                }, 200);
            }
        });
    }
})();
</script>

<style>
.leaflet-marker-icon.selected {
    filter: hue-rotate(90deg) saturate(1.6) brightness(1.3);
}
.leaflet-tooltip.center-tooltip {
    background: #ffffff;
    color: #333;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 3px;
    padding: 2px 6px;
    box-shadow: 0 1px 2px rgba(0,0,0,.1);
    font-size: 12px;
    font-weight: 600;
}
</style>

