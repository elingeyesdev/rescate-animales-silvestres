@extends('adminlte::page')

@section('template_title')
    Transfers
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Transfers') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('transfers.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                              </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <ul class="nav nav-pills mb-3" id="transferTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ ($tab ?? 'first')==='first' ? 'active' : '' }}" id="first-tab" href="{{ route('transfers.index', ['tab' => 'first']) }}" role="tab">{{ __('Primer traslado') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($tab ?? 'first')==='internal' ? 'active' : '' }}" id="internal-tab" href="{{ route('transfers.index', ['tab' => 'internal']) }}" role="tab">{{ __('Traslado entre centros') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab">{{ __('Historial') }}</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            @if(($tab ?? 'first')==='first')
                            <div class="tab-pane fade show active" id="first" role="tabpanel" aria-labelledby="first-tab">
                                <div class="row">
                                    <div class="col-md-6" id="reports_list">
                                    @forelse(($reportsFirst ?? []) as $report)
                                        <div class="first-transfer-card" data-report-id="{{ $report->id }}">
                                            <div class="card card-outline card-secondary mb-3">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h3 class="card-title mb-0">{{ __('Hallazgo aprobado') }} #{{ $report->id }}</h3>
                                                    <span class="small text-muted">{{ optional($report->created_at)->format('d/m/Y') }}</span>
                                                </div>
                                                <div class="card-body">
                                                    @php
                                                        $dirRaw = $report->direccion ?? '';
                                                        $dirNoCountry = preg_replace('/,?\\s*Santa\\s*Cruz,?\\s*Bolivia$/i', '', $dirRaw);
                                                        $prov = null;
                                                        if (preg_match('/Provincia\\s+([^,]+)/i', $dirRaw, $m)) { $prov = $m[1]; }
                                                    @endphp
                                                    @if($report->imagen_url)
                                                        <div class="mb-2">
                                                            <img src="{{ asset('storage/' . $report->imagen_url) }}" alt="img" style="width:100%; max-height:220px; object-fit:cover; border-radius:4px;">
                                                        </div>
                                                    @endif
                                                    <div class="mb-2">
                                                        <strong>{{ __('Dirección') }}:</strong> {{ $dirNoCountry ?: '-' }}
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>{{ __('Provincia') }}:</strong> {{ $prov ?: '-' }}
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>{{ __('Condición') }}:</strong> {{ $report->condicionInicial?->nombre ?? '-' }}
                                                    </div>
                                                    <form method="POST" action="{{ route('transfers.store') }}">
                                                        @csrf
                                                        <input type="hidden" name="report_id" value="{{ $report->id }}">
                                                        <div class="form-group mb-2 mb20">
                                                            <label for="persona_r{{ $report->id }}" class="form-label">{{ __('Persona que traslada') }}</label>
                                                            <select name="persona_id" id="persona_r{{ $report->id }}" class="form-control js-person-select" data-report-id="{{ $report->id }}">
                                                                <option value="">{{ __('Seleccione') }}</option>
                                                                @foreach(($people ?? []) as $p)
                                                                    <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary mt-2">
                                                            {{ __('Enviar a centro') }}
                                                        </button>
                                                        <input type="hidden" name="centro_id" id="centro_r{{ $report->id }}">
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="text-muted">{{ __('No hay hallazgos pendientes de primer traslado.') }}</div>
                                        </div>
                                    @endforelse
                                    </div>
                                    <div class="col-md-6 d-none text-center" id="first_map_panel">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <button type="button" class="btn btn-secondary btn-sm" id="first_back_btn"><i class="fa fa-arrow-left"></i> {{ __('Volver a hallazgos aprobados') }}</button>
                                        </div>
                                        <label class="form-label d-block mb-1">{{ __('Seleccione el centro de destino en el mapa') }}</label>
                                        <small class="text-muted d-block mb-2">{{ __('Haga clic en un centro o en su nombre en la lista para seleccionarlo') }}</small>
                                        <div id="centers_map_first" style="height: 360px; border-radius: 4px; margin-bottom: 8px;"></div>
                                        <div id="centers_legend_first" class="small text-muted"></div>
                                    </div>
                                </div>
                            </div>
                            @elseif(($tab ?? 'first')==='internal')
                            <div class="tab-pane fade show active" id="internal" role="tabpanel" aria-labelledby="internal-tab">
                                <div class="row">
                                    <div class="col-md-6" id="internal_list">
                                        @forelse(($animalFiles ?? []) as $af)
                                            <div class="card card-outline card-secondary mb-3 internal-af-card" data-animal-id="{{ $af->animal_id }}" data-af-id="{{ $af->id }}">
                                                <div class="card-header">
                                                    <h3 class="card-title mb-0">#{{ $af->id }} {{ $af->animal?->nombre ?? '-' }}</h3>
                                                </div>
                                                <div class="card-body">
                                                    @if($af->imagen_url)
                                                        <div class="mb-2"><img src="{{ asset('storage/' . $af->imagen_url) }}" alt="img" style="max-height:160px; border-radius:4px;"></div>
                                                    @endif
                                                    <div class="text-muted small">{{ __('Click para seleccionar') }}</div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-muted">{{ __('No hay hojas de vida disponibles.') }}</div>
                                        @endforelse
                                    </div>
                                    <div class="col-md-6 d-none" id="internal_map_panel">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <button type="button" class="btn btn-secondary btn-sm" id="internal_back_btn"><i class="fa fa-arrow-left"></i> {{ __('Volver a hojas de vida') }}</button>
                                        </div>
                                        <form method="POST" action="{{ route('transfers.store') }}">
                                            @csrf
                                            <input type="hidden" name="animal_file_id" id="internal_af_hidden">
                                            <input type="hidden" name="animal_id" id="internal_animal_hidden">
                                            <input type="hidden" name="centro_id" id="centro_internal">
                                            
                                            <div class="form-group mb-2 mb20">
                                                <label for="persona_internal" class="form-label">{{ __('Persona que traslada') }}</label>
                                                <select name="persona_id" id="persona_internal" class="form-control">
                                                    <option value="">{{ __('Seleccione') }}</option>
                                                    @foreach(($people ?? []) as $p)
                                                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <label class="form-label">{{ __('Seleccione el centro de destino en el mapa') }}</label>
                                            <div id="centers_map_internal" style="height: 360px; border-radius: 4px; margin-bottom: 8px;"></div>
                                            <div id="centers_legend_internal" class="small text-muted"></div>
                                            <button type="submit" class="btn btn-primary mt-2">{{ __('Enviar a centro') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="thead">
                                            <tr>
                                                <th>No</th>
                                                <th>{{ __('Persona') }}</th>
                                                <th>{{ __('Centro') }}</th>
                                                <th>{{ __('Observaciones') }}</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transfers as $transfer)
                                                <tr>
                                                    <td>{{ ++$i }}</td>
                                                    <td>{{ $transfer->person?->nombre ?? '-' }}</td>
                                                    <td>{{ $transfer->center?->nombre ?? $transfer->center?->id }}</td>
                                                    <td>{{ $transfer->observaciones }}</td>
                                                    <td>
                                                        <form action="{{ route('transfers.destroy', $transfer->id) }}" method="POST">
                                                            <a class="btn btn-sm btn-primary" href="{{ route('transfers.show', $transfer->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                            <a class="btn btn-sm btn-success" href="{{ route('transfers.edit', $transfer->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {!! $transfers->withQueryString()->links() !!}
            </div>
        </div>
    </div>
    @include('partials.leaflet')
    <style>
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
    .leaflet-marker-icon.selected {
      filter: hue-rotate(500deg) saturate(1.6) brightness(1.15);
    }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializar mapas por cada hallazgo en primer traslado
        const centersData = @json($centers ?? []);
        function buildMap(containerId, legendId, hiddenInputId, centersArr) {
            const el = document.getElementById(containerId);
            const legend = document.getElementById(legendId);
            const data = Array.isArray(centersArr) ? centersArr : centersData;
            // hidden input id can change depending the selected report; use a dynamic getter
            function getHidden() {
                const dynamicId = window.firstMapTargetHiddenId || hiddenInputId;
                return document.getElementById(dynamicId);
            }
            if (!el) return null;
            const map = L.map(containerId).setView([ -17.7833, -63.1821 ], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
            // Ensure map sizes correctly when container was hidden
            setTimeout(() => { try { map.invalidateSize(); } catch(e) {} }, 0);
            const markers = [];
            data.forEach((c) => {
                if (c.latitud && c.longitud) {
                    const m = L.marker([c.latitud, c.longitud]).addTo(map);
                    m.bindTooltip(`${c.nombre}`, { permanent: true, direction: 'top', className: 'center-tooltip', offset: [0,-10] });
                    m.on('click', () => {
                        const hidden = getHidden();
                        if (hidden) hidden.value = c.id;
                        highlightLegend(c.id);
                        markers.forEach(obj => {
                            const iconEl = obj.marker?._icon;
                            if (!iconEl) return;
                            iconEl.classList.remove('selected');
                        });
                        if (m && m._icon) m._icon.classList.add('selected');
                    });
                    markers.push({ id: c.id, marker: m });
                }
            });
            function highlightLegend(id){
                if (!legend) return;
                const children = legend.querySelectorAll('[data-center-id]');
                children.forEach(ch => {
                    ch.style.fontWeight = (String(ch.getAttribute('data-center-id')) === String(id)) ? '700' : '400';
                });
            }
            if (legend) {
                legend.innerHTML = '';
                data.forEach(c => {
                    const span = document.createElement('span');
                    span.textContent = `#${c.id} ${c.nombre}`;
                    span.style.cursor = 'pointer';
                    span.style.display = 'inline-block';
                    span.style.marginRight = '10px';
                    span.setAttribute('data-center-id', c.id);
                    span.onclick = () => {
                        map.setView([c.latitud, c.longitud], 15);
                        const hidden = getHidden();
                        if (hidden) hidden.value = c.id;
                        highlightLegend(c.id);
                        markers.forEach(obj => {
                            const iconEl = obj.marker?._icon;
                            if (!iconEl) return;
                            iconEl.classList.remove('selected');
                        });
                        const sel = markers.find(obj => String(obj.id) === String(c.id));
                        if (sel && sel.marker && sel.marker._icon) sel.marker._icon.classList.add('selected');
                    };
                    legend.appendChild(span);
                });
            }
            return map;
        }
        // Mostrar mapa a la derecha y ocultar otras cards cuando se elige persona en cada card
        @if(($tab ?? 'first')==='first')
        (function initFirstTransferMaps(){
            const peopleSelects = document.querySelectorAll('[id^="persona_r"]');
            peopleSelects.forEach(sel => {
                const id = sel.id.replace('persona_r','');
                const card = sel.closest('.first-transfer-card');
                const mapPanel = document.getElementById('first_map_panel');
                const backBtn = document.getElementById('first_back_btn');
                let initialized = false;
                sel.addEventListener('change', function(){
                    if (this.value) {
                        // Show right panel
                        mapPanel.classList.remove('d-none');
                        // Target hidden input of this report
                        window.firstMapTargetHiddenId = `centro_r${id}`;
                        if (!initialized) {
                            buildMap(`centers_map_first`, `centers_legend_first`, window.firstMapTargetHiddenId, centersData);
                            initialized = true;
                        } else {
                            // map already built; only retarget hidden input
                            window.firstMapTargetHiddenId = `centro_r${id}`;
                        }
                        // Ocultar otras cards
                        document.querySelectorAll('.first-transfer-card').forEach(c => {
                            if (c !== card) c.style.display = 'none';
                        });
                        // Scroll into view
                        mapPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        // Hide panel if deselected
                        mapPanel.classList.add('d-none');
                        // Mostrar todas las cards si se deselecciona
                        document.querySelectorAll('.first-transfer-card').forEach(c => c.style.display = '');
                    }
                });
                // Volver a listado
                document.getElementById('first_back_btn')?.addEventListener('click', function(e){
                    e.preventDefault();
                    // Reset UI
                    mapPanel.classList.add('d-none');
                    sel.value = '';
                    document.getElementById(`centro_r${id}`).value = '';
                    document.querySelectorAll('.first-transfer-card').forEach(c => c.style.display = '');
                    window.firstMapTargetHiddenId = null;
                });
            });
        })();
        @endif

        // Interacciones para traslado entre centros
        @if(($tab ?? 'first')==='internal')
        (function initInternalTransfer(){
            const list = document.getElementById('internal_list');
            const panel = document.getElementById('internal_map_panel');
            const backBtn = document.getElementById('internal_back_btn');
            const currentCenterEl = document.getElementById('internal_current_center');
            const hiddenAf = document.getElementById('internal_af_hidden');
            const hiddenAnimal = document.getElementById('internal_animal_hidden');

            function renderInternalMap(animalId) {
                const url = new URL('{{ route('transfers.index') }}', window.location.origin);
                url.searchParams.set('current_center', '1');
                url.searchParams.set('animal_id', animalId);
                fetch(url.toString())
                    .then(r => r.json())
                    .then(data => {
                        const cur = data.current;
                        if (cur) {
                            currentCenterEl.textContent = `#${cur.id} ${cur.nombre}`;
                        } else {
                            currentCenterEl.textContent = '-';
                        }
                        window._internalMapInstance = buildMap('centers_map_internal', 'centers_legend_internal', 'centro_internal', (data.destinations || []));
                        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    })
                    .catch(() => {
                        currentCenterEl.textContent = '-';
                        window._internalMapInstance = buildMap('centers_map_internal', 'centers_legend_internal', 'centro_internal', []);
                    });
            }

            document.querySelectorAll('.internal-af-card').forEach(card => {
                card.addEventListener('click', function(){
                    const afId = this.getAttribute('data-af-id');
                    const animalId = this.getAttribute('data-animal-id');
                    if (hiddenAf) hiddenAf.value = afId || '';
                    if (hiddenAnimal) hiddenAnimal.value = animalId || '';
                    // Hide other cards
                    document.querySelectorAll('.internal-af-card').forEach(c => { if (c !== this) c.style.display = 'none'; });
                    // Show right panel
                    panel.classList.remove('d-none');
                    renderInternalMap(animalId);
                    // fix leaflet sizing after reveal
                    setTimeout(() => {
                        if (window._internalMapInstance && window._internalMapInstance.invalidateSize) {
                            try { window._internalMapInstance.invalidateSize(); } catch(e) {}
                        }
                    }, 50);
                });
            });

            backBtn?.addEventListener('click', function(e){
                e.preventDefault();
                // Reset UI
                panel.classList.add('d-none');
                document.getElementById('centro_internal').value = '';
                currentCenterEl.textContent = '-';
                document.querySelectorAll('.internal-af-card').forEach(c => c.style.display = '');
            });
        })();
        @endif

        // Navegación server-side por pestañas: no necesitamos resets adicionales
    });
    </script>
    @include('partials.page-pad')
@endsection
