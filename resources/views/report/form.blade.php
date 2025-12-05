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
        {!! $errors->first('general', '<div class="alert alert-danger" role="alert"><strong>:message</strong></div>') !!}

        <div class="form-group mb-2 mb20">
            <label for="imagen" class="form-label">{{ __('Imagen') }}</label>
            <div class="custom-file">
                <input type="file" accept="image/jpeg,image/jpg,image/png" name="imagen" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen">
                <label class="custom-file-label" for="imagen" data-browse="Subir">Subir la imagen del animal</label>
            </div>
            {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
            @if(!empty($report?->imagen_url))
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $report->imagen_url) }}" alt="Imagen del reporte" style="max-height: 120px;"/>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-2 mb20">
                    <label for="condicion_inicial_id" class="form-label">{{ __('Estado inicial del animal') }}</label>
                    <select name="condicion_inicial_id" id="condicion_inicial_id" class="form-control @error('condicion_inicial_id') is-invalid @enderror">
                        <option value="">{{ __('Seleccione...') }}</option>
                        @foreach(($conditions ?? []) as $c)
                            @php($condLabel = $c->nombre === 'Desconocido' ? ($c->nombre.' (especificar en Observaciones)') : $c->nombre)
                            <option value="{{ $c->id }}"
                                {{ (string)old('condicion_inicial_id', $report?->condicion_inicial_id) === (string)$c->id
                                    || (!old('condicion_inicial_id', $report?->condicion_inicial_id) && empty($report?->id) && $loop->first)
                                        ? 'selected' : '' }}>
                                {{ $condLabel }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('condicion_inicial_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-2 mb20">
                    <label for="tipo_incidente_id" class="form-label">{{ __('Tipo de incidente') }}</label>
                    <select name="tipo_incidente_id" id="tipo_incidente_id" class="form-control @error('tipo_incidente_id') is-invalid @enderror">
                        <option value="">{{ __('Seleccione...') }}</option>
                        @foreach(($incidentTypes ?? []) as $it)
                            @php($incLabel = $it->nombre === 'Otro' ? ($it->nombre.' (especificar en Observaciones)') : $it->nombre)
                            <option value="{{ $it->id }}"
                                {{ (string)old('tipo_incidente_id', $report?->tipo_incidente_id) === (string)$it->id
                                    || (!old('tipo_incidente_id', $report?->tipo_incidente_id) && empty($report?->id) && $loop->first)
                                        ? 'selected' : '' }}>
                                {{ $incLabel }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('tipo_incidente_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-2 mb20">
                    <label class="form-label d-block">{{ __('Tamaño del animal') }}</label>
                    @php($tam = old('tamano', $report?->tamano ?? 'mediano'))
                    <div class="icheck-primary d-inline mr-3">
                        <input type="radio" id="tamano_peq" name="tamano" value="pequeno" {{ $tam==='pequeno' ? 'checked' : '' }}>
                        <label for="tamano_peq">{{ __('Pequeño') }}</label>
                    </div>
                    <div class="icheck-primary d-inline mr-3">
                        <input type="radio" id="tamano_med" name="tamano" value="mediano" {{ $tam==='mediano' ? 'checked' : '' }}>
                        <label for="tamano_med">{{ __('Mediano') }}</label>
                    </div>
                    <div class="icheck-primary d-inline">
                        <input type="radio" id="tamano_gra" name="tamano" value="grande" {{ $tam==='grande' ? 'checked' : '' }}>
                        <label for="tamano_gra">{{ __('Grande') }}</label>
                    </div>
                    {!! $errors->first('tamano', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-2 mb20">
                    <label class="form-label d-block">{{ __('¿Puede moverse?') }}</label>
                    @php($pm = old('puede_moverse', is_null($report?->puede_moverse) ? '0' : (int)$report->puede_moverse))
                    <div class="icheck-primary d-inline mr-3">
                        <input type="radio" id="moverse_si" name="puede_moverse" value="1" {{ (string)$pm === '1' ? 'checked' : '' }}>
                        <label for="moverse_si">{{ __('Sí') }}</label>
                    </div>
                    <div class="icheck-primary d-inline">
                        <input type="radio" id="moverse_no" name="puede_moverse" value="0" {{ (string)$pm === '0' ? 'checked' : '' }}>
                        <label for="moverse_no">{{ __('No') }}</label>
                    </div>
                    {!! $errors->first('puede_moverse', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
        </div>

        <div class="form-group mb-2 mb20">
            <label for="observaciones" class="form-label">{{ __('Observaciones') }}</label>
            <div class="mb-2">
                <div class="d-flex flex-wrap" style="gap: 0.4rem;">
                    <button type="button" class="btn btn-sm btn-outline-secondary preset-obs-btn" data-text="Tiene la pata dañada.">
                        <i class="fas fa-paw"></i> Pata dañada
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary preset-obs-btn" data-text="Se encuentra tendido en el suelo.">
                        <i class="fas fa-bed"></i> Tendido en suelo
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary preset-obs-btn" data-text="No se puede observar a detalle.">
                        <i class="fas fa-eye-slash"></i> No observable
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary preset-obs-btn" data-text="Parece estar deshidratado.">
                        <i class="fas fa-tint"></i> Deshidratado
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary preset-obs-btn" data-text="Tiene heridas visibles.">
                        <i class="fas fa-band-aid"></i> Heridas visibles
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary preset-obs-btn" data-text="Se encuentra en zona de riesgo.">
                        <i class="fas fa-exclamation-triangle"></i> Zona de riesgo
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary preset-obs-btn" data-text="No responde a estímulos.">
                        <i class="fas fa-heartbeat"></i> No responde
                    </button>
                </div>
            </div>
            <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" rows="4" maxlength="500" aria-describedby="observaciones_help" placeholder="Observaciones">{{ old('observaciones', $report?->observaciones) }}</textarea>
            <small id="observaciones_help" class="form-text text-muted">{{ __('Máximo 500 caracteres') }} · <span id="obs_counter">0</span>/500</small>
            {!! $errors->first('observaciones', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        @if(empty($report?->id))
        <div class="form-group mb-2 mb20">
            <label class="form-label">{{ __('Selecciona la ubicación en el mapa') }}</label>
            <div class="row">
                <div class="col-12">
                    <div id="mapid" style="height: 360px; width: 100%; border-radius: 4px;"></div>
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
                <div class="col-12">
                    <div id="centers_map" style="height: 320px; width: 100%; border-radius: 4px; margin-bottom: 8px;"></div>
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
    const fileName = file ? file.name : 'Subir';
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
    if (file && file.type && file.type.startsWith('image/') && file.type !== 'image/webp') {
      preview.src = URL.createObjectURL(file);
      preview.style.display = 'block';
    } else if (preview) {
      preview.style.display = 'none';
      if (file && file.type === 'image/webp') {
        alert('El formato de imagen .webp no está permitido. Por favor, usa JPG, JPEG o PNG.');
        input.value = '';
      }
    }
  });
  const obs = document.getElementById('observaciones');
  const counter = document.getElementById('obs_counter');
  function updateCounter(){ if (obs && counter) counter.textContent = String(obs.value.length); }
  obs?.addEventListener('input', updateCounter);
  updateCounter();

  // Función para actualizar el estado visual de los botones
  function updateButtonStates() {
    if (!obs) return;
    const currentText = obs.value;
    document.querySelectorAll('.preset-obs-btn').forEach(btn => {
      const text = btn.getAttribute('data-text');
      const isActive = currentText.includes(text);
      if (isActive) {
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-secondary');
      } else {
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-outline-secondary');
      }
    });
  }

  // Mensajes predeterminados para observaciones (toggle)
  document.querySelectorAll('.preset-obs-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const text = this.getAttribute('data-text');
      if (!obs) return;
      let currentText = obs.value;
      const isActive = currentText.includes(text);
      
      if (isActive) {
        // Si el texto ya está, eliminarlo
        // Remover el texto y espacios extra alrededor
        currentText = currentText.replace(new RegExp('\\s*' + text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\s*', 'g'), ' ').trim();
        obs.value = currentText;
        this.classList.remove('btn-secondary');
        this.classList.add('btn-outline-secondary');
      } else {
        // Si el texto no está, agregarlo
        const newText = currentText.trim() ? currentText.trim() + ' ' + text : text;
        if (newText.length <= 500) {
          obs.value = newText;
          this.classList.remove('btn-outline-secondary');
          this.classList.add('btn-secondary');
        } else {
          alert('El texto excedería el límite de 500 caracteres.');
          return;
        }
      }
      
      updateCounter();
      obs.focus();
      // Mover el cursor al final
      obs.setSelectionRange(obs.value.length, obs.value.length);
    });
  });

  // Actualizar estado de botones cuando cambia el texto manualmente
  obs?.addEventListener('input', function() {
    updateCounter();
    updateButtonStates();
  });

  // Actualizar estado inicial de los botones
  updateButtonStates();

  // Mostrar aviso cuando selección requiera Observaciones
  const condSel = document.getElementById('condicion_inicial_id');
  const incSel = document.getElementById('tipo_incidente_id');
  let warnEl = document.getElementById('obs_required_warn');
  if (!warnEl && obs) {
    warnEl = document.createElement('div');
    warnEl.id = 'obs_required_warn';
    warnEl.className = 'small text-warning mt-1';
    warnEl.style.display = 'none';
    obs.parentElement?.appendChild(warnEl);
  }
  function toggleObsWarn(){
    const condText = condSel?.options?.[condSel.selectedIndex || 0]?.text || '';
    const incText = incSel?.options?.[incSel.selectedIndex || 0]?.text || '';
    const must = (condText.includes('Desconocido')) || (incText.startsWith('Otro'));
    
  }
  condSel?.addEventListener('change', toggleObsWarn);
  incSel?.addEventListener('change', toggleObsWarn);
  toggleObsWarn();
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
    const latIn = document.getElementById('latitud');
    const lonIn = document.getElementById('longitud');
    if (latIn && lonIn && (!latIn.value || !lonIn.value)) {
      latIn.value = '-17.7833';
      lonIn.value = '-63.1821';
    }

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
                m.bindTooltip(`${c.nombre}`, {
                    permanent: true,
                    direction: 'top',
                    offset: [0, -10],
                    opacity: 0.9,
                    className: 'center-tooltip'
                });
                m.on('click', () => {
                    selectedInput.value = c.id;
                    highlightLegend(c.id);
                    highlightMarker(c.id);
                });
                centersMarkers.push({ id: c.id, marker: m });
            }
        });
        // build legend with names only
        /*if (legend) {
            legend.innerHTML = '';
            centersData.forEach(c => {
                const item = document.createElement('div');
                item.className = 'center-legend-item';
                item.style.cursor = 'pointer';
                item.style.display = 'inline-flex';
                item.style.alignItems = 'center';
                item.style.marginRight = '12px';
                const dot = document.createElement('span');
                dot.style.backgroundColor = '#3388ff';
                dot.style.width = '10px';
                dot.style.height = '10px';
                dot.style.borderRadius = '50%';
                dot.style.display = 'inline-block';
                dot.style.marginRight = '6px';
                const text = document.createElement('span');
                text.textContent = c.nombre;
                item.appendChild(dot);
                item.appendChild(text);
                item.onclick = () => {
                    if (c.latitud && c.longitud) {
                        centersMap.setView([c.latitud, c.longitud], 15);
                        document.getElementById('centro_id').value = c.id;
                        highlightLegend(c.id);
                    }
                };
                item.id = `legend_center_${c.id}`;
                legend.appendChild(item);
            });
            // preselect if a center was already chosen (old input)
            const pre = selectedInput && selectedInput.value ? selectedInput.value : null;
            if (pre) {
                highlightLegend(pre);
                highlightMarker(pre);
                const chosen = centersData.find(cc => String(cc.id) === String(pre));
                if (chosen && chosen.latitud && chosen.longitud) {
                    centersMap.setView([chosen.latitud, chosen.longitud], 15);
                }
            } else {
                // choose nearest center to reported location if available
                const rptLatEl = document.getElementById('latitud');
                const rptLonEl = document.getElementById('longitud');
                const rptLat = rptLatEl && rptLatEl.value ? parseFloat(rptLatEl.value) : null;
                const rptLon = rptLonEl && rptLonEl.value ? parseFloat(rptLonEl.value) : null;
                let candidate = null;
                if (!isNaN(rptLat) && !isNaN(rptLon)) {
                    let bestD = Infinity;
                    centersData.forEach(cc => {
                        if (cc.latitud && cc.longitud) {
                            const dLat = rptLat - parseFloat(cc.latitud);
                            const dLon = rptLon - parseFloat(cc.longitud);
                            const d2 = dLat*dLat + dLon*dLon;
                            if (d2 < bestD) { bestD = d2; candidate = cc; }
                        }
                    });
                }
                if (!candidate) {
                    candidate = centersData.find(cc => cc.latitud && cc.longitud) || null;
                }
                if (candidate) {
                    selectedInput.value = candidate.id;
                    highlightLegend(candidate.id);
                    highlightMarker(candidate.id);
                    centersMap.setView([candidate.latitud, candidate.longitud], 15);
                }
            }
        }*/
        function highlightLegend(id){
            const all = document.querySelectorAll('#centers_legend .center-legend-item');
            all.forEach(el => el.classList.remove('active'));
            const el = document.getElementById(`legend_center_${id}`);
            if (el) el.classList.add('active');
        }
        function highlightMarker(id){
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

<style>
.custom-file-label::after { content: 'Subir'; }
#centers_legend .center-legend-item {
  padding: 4px 8px;
  border-radius: 4px;
  border: 1px solid transparent;
  margin-bottom: 6px;
}
#centers_legend .center-legend-item.active {
  background-color: #e9f2ff;
  border-color: #0d6efd;
  color: #0d6efd;
  font-weight: 700;
}
.leaflet-marker-icon.selected {
  filter: hue-rotate(900deg) saturate(1.6) brightness(1.3);
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
</style>