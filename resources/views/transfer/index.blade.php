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
                            <!--<li class="nav-item">
                                <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab">{{ __('Historial') }}</a>
                            </li>-->
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
                                                    <h3 class="card-title mb-0">{{ __('Hallazgo aprobado') }} N°{{ $report->id }}</h3>
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
                                            <div class="card card-outline card-secondary mb-3 internal-af-card"
                                                 data-animal-id="{{ $af->animal_id }}"
                                                 data-af-id="{{ $af->id }}"
                                                 data-center-id="{{ $af->centro_id }}"
                                                 data-center-name="{{ $af->center?->nombre }}">
                                                <div class="card-header">
                                                    <h3 class="card-title mb-0">{{ $af->animal?->nombre ?? '-' }}</h3>
                                                </div>
                                                <div class="card-body">
                                                    @if($af->imagen_url)
                                                        <img class="internal-af-img" src="{{ asset('storage/' . $af->imagen_url) }}" alt="imagen animal">
                                                    @endif
                                                    <div class="text-muted small mt-2">{{ __('Click para seleccionar') }}</div>
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
                                            <div class="card card-outline card-secondary mb-2">
                                                <div class="card-body p-2 d-flex align-items-center">
                                                    <div style="width:84px; height:84px; border-radius:4px; overflow:hidden; background:#f4f6f9;" class="mr-2">
                                                        <img id="internal_animal_info_img" src="" alt="img" style="width:100%; height:100%; object-fit:cover; display:none;">
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="small text-muted mb-1">{{ __('Animal seleccionado') }}</div>
                                                        <div id="internal_animal_info_name" class="font-weight-bold">-</div>
                                                        <div class="small text-muted">{{ __('Centro actual:') }} <span id="internal_current_center">-</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group mb-2 mb20">
                                                <label for="persona_internal" class="form-label">{{ __('Persona que traslada') }}</label>
                                                <select name="persona_id" id="persona_internal" class="form-control">
                                                    <option value="">{{ __('Seleccione') }}</option>
                                                    @foreach(($people ?? []) as $p)
                                                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group mb-2 mb20">
                                                <label for="centro_internal_select" class="form-label">{{ __('Centro de destino') }}</label>
                                                <select name="centro_id" id="centro_internal_select" class="form-control">
                                                    <option value="">{{ __('Seleccione') }}</option>
                                                    @foreach(($centers ?? []) as $c)
                                                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
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
    .internal-af-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      background: #f4f6f9;
      border-radius: 4px;
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
            const centerSelect = document.getElementById('centro_internal_select');
            const infoName = document.getElementById('internal_animal_info_name');
            const infoImg = document.getElementById('internal_animal_info_img');

            document.querySelectorAll('.internal-af-card').forEach(card => {
                card.addEventListener('click', function(){
                    const afId = this.getAttribute('data-af-id');
                    const animalId = this.getAttribute('data-animal-id');
                    const centerId = this.getAttribute('data-center-id');
                    const centerName = this.getAttribute('data-center-name');
                    if (hiddenAf) hiddenAf.value = afId || '';
                    if (hiddenAnimal) hiddenAnimal.value = animalId || '';
                    // Copiar info al panel derecho
                    var name = this.querySelector('.card-header .card-title')?.textContent || ('#'+afId);
                    if (infoName) infoName.textContent = (name || '').trim();
                    var leftImg = this.querySelector('img');
                    if (infoImg) {
                        if (leftImg && leftImg.getAttribute('src')) {
                            infoImg.src = leftImg.getAttribute('src');
                            infoImg.style.display = '';
                        } else {
                            infoImg.removeAttribute('src');
                            infoImg.style.display = 'none';
                        }
                    }
                    // Hide other cards
                    document.querySelectorAll('.internal-af-card').forEach(c => { if (c !== this) c.style.display = 'none'; });
                    // Show right panel
                    panel.classList.remove('d-none');
                    // Mostrar centro actual desde la hoja de vida
                    if (centerId && centerName) {
                        currentCenterEl.textContent = centerName;
                    } else {
                        currentCenterEl.textContent = '-';
                    }
                    // En el combo de destino, ocultar / deshabilitar el centro actual
                    if (centerSelect) {
                        Array.from(centerSelect.options).forEach(opt => {
                            if (!opt.value) {
                                opt.disabled = false;
                                opt.hidden = false;
                                return;
                            }
                            if (centerId && String(opt.value) === String(centerId)) {
                                opt.disabled = true;
                                opt.hidden = true;
                            } else {
                                opt.disabled = false;
                                opt.hidden = false;
                            }
                        });
                        centerSelect.value = '';
                    }
                    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });

            backBtn?.addEventListener('click', function(e){
                e.preventDefault();
                // Reset UI
                panel.classList.add('d-none');
                if (centerSelect) centerSelect.value = '';
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
