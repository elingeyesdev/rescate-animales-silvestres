@extends('adminlte::page')

@section('template_title')
    {{ __('Historial de Animal ') . $animalHistory->animal_file_id }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">
                            {{ __('Detalle de Historial') }}
                            @if($animalHistory->animalFile?->animal?->nombre)
                                {{ ' ' . __('de') . ' ' . $animalHistory->animalFile->animal->nombre }}
                            @endif
                        </span>
                    </div>
                    <div class="card-body bg-white">
                        @php
                            $af = $animalHistory->animalFile;
                            $animal = $af?->animal;
                            $statusName = $af?->animalStatus?->nombre ?? '-';
                            $animalName = $animal?->nombre ?? '-';
                            $report = $animal?->report ?? null;
                            $reportDate = optional($report?->created_at)->format('d/m/Y');
                            $arrivalImg = $report?->imagen_url;
                        @endphp
                        <div class="sticky-summary d-flex align-items-center mb-3 p-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <h4 class="mb-0 mr-3">{{ $animalName !== '-' ? $animalName : __('Detalle de Historial') }}</h4>
                                    @if($statusName && $statusName !== '-')
                                        <span class="badge badge-info" style="font-size:0.95rem;">{{ $statusName }}</span>
                                    @endif
                                </div>
                                <div class="text-muted small mt-1">
                                    @if($reportDate)
                                        {{ __('Hallazgo') }}: {{ $reportDate }}
                                    @endif
                                </div>
                            </div>
                            @if($arrivalImg)
                                <div class="ml-3">
                                    <img src="{{ asset('storage/' . $arrivalImg) }}" alt="Llegada" style="max-height:96px; border-radius:6px;">
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <div class="btn-group" role="group" aria-label="{{ __('Vista de historial') }}">
                                <button type="button" class="btn btn-outline-primary btn-sm active" id="btnTimelineView">
                                    {{ __('Línea de tiempo') }}
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btnMapView">
                                    {{ __('Mapa de traslados') }}
                                </button>
                            </div>
                        </div>

                        <div id="timelineContainer">
                        <div class="timeline">
                            @php $currentDate = null; @endphp
                            @foreach(($timeline ?? []) as $t)
                                @php
                                    $datetime = trim($t['changed_at'] ?? '');
                                    $date = $datetime ? explode(' ', $datetime)[0] : '';
                                    $time = $datetime && strpos($datetime, ' ') !== false ? trim(substr($datetime, strpos($datetime, ' '))) : '';
                                    $title = $t['title'] ?? 'Actualización';
                                    $icon = 'far fa-clock';
                                    $bg = 'bg-gray';
                                    switch ($title) {
                                        case 'Reporte de hallazgo': $icon='fas fa-flag'; $bg='bg-success'; break;
                                        case 'Traslado': $icon='fas fa-truck'; $bg='bg-warning'; break;
                                        case 'Evaluación Médica': $icon='fas fa-stethoscope'; $bg='bg-danger'; break;
                                        case 'Cuidado': $icon='fas fa-hand-holding-heart'; $bg='bg-purple'; break;
                                        case 'Alimentación': $icon='fas fa-utensils'; $bg='bg-teal'; break;
                                        case 'Cambio de estado': $icon='fas fa-exchange-alt'; $bg='bg-info'; break;
                                        case 'Creación de Hoja de Vida': $icon='fas fa-file-medical'; $bg='bg-primary'; break;
                                        case 'Animal': $icon='fas fa-paw'; $bg='bg-primary'; break;
                                    }
                                @endphp
                                @if($date && $date !== $currentDate)
                                    <div class="time-label">
                                        <span class="bg-navy">{{ $date }}</span>
                                    </div>
                                    @php $currentDate = $date; @endphp
                                @endif
                                <div>
                                    <i class="{{ $icon }} {{ $bg }}"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $time }} @if($date)&nbsp;&nbsp;<i class="fas fa-calendar-alt"></i> {{ $date }}@endif</span>
                                        <h3 class="timeline-header">{{ $title }}</h3>
                                        <div class="timeline-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    @forelse(($t['details'] ?? []) as $d)
                                                        <div class="mb-2">
                                                            <span class="text-muted">{{ $d['label'] }}:</span>
                                                            <span>{{ $d['value'] }}</span>
                                                        </div>
                                                    @empty
                                                        <div class="text-muted">{{ __('Sin detalles') }}</div>
                                                    @endforelse
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    @if(!empty($t['image_url']))
                                                        <img src="{{ asset('storage/' . $t['image_url']) }}"
                                                             data-full="{{ asset('storage/' . $t['image_url']) }}"
                                                             alt="Imagen"
                                                             class="history-thumb"
                                                             style="max-height: 200px; max-width: 100%; height: auto; width: auto; cursor: zoom-in;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @php $hasLink = false; @endphp
                                        @if($hasLink)
                                            <div class="timeline-footer">
                                                <a class="btn btn-primary btn-sm" href="#">{{ __('Ver') }}</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            {{-- Punto final de la línea de tiempo eliminado para evitar icono de reloj adicional --}}
                        </div>
                        </div>

                        <div id="mapContainer" style="display:none;">
                            @php $points = $mapRoute['points'] ?? []; @endphp
                            @if(!empty($points))
                                <div id="animalRouteMap" style="height: 380px; border-radius: 6px; overflow: hidden;"></div>
                                <div class="mt-2 small text-muted">
                                    <strong>{{ __('Leyenda') }}:</strong>
                                    <span class="ml-2">
                                        <span class="legend-dot legend-dot-hallazgo"></span> {{ __('Hallazgo') }}
                                    </span>
                                    <span class="ml-3">
                                        <span class="legend-dot legend-dot-transfer"></span> {{ __('Traslado / Centro') }}
                                    </span>
                                    <span class="ml-3">
                                        <span class="legend-dot legend-dot-release"></span> {{ __('Liberación') }}
                                    </span>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    {{ __('No hay datos de ubicación geográfica registrados para este animal.') }}
                                </div>
                            @endif
                        </div>
                        <div id="imageOverlay" style="display:none; position:fixed; left:0; top:0; right:0; bottom:0; background:rgba(0,0,0,.85); z-index:1050; align-items:center; justify-content:center;">
                            <button id="overlayClose" type="button" style="position:absolute; top:16px; right:16px; background:rgba(0,0,0,.4); border:0; color:#fff; padding:8px 12px; border-radius:4px; cursor:pointer;">
                                ✕ {{ __('Cerrar') }}
                            </button>
                            <img id="overlayImg" src="" alt="Imagen" style="max-width:90%; max-height:90%; border-radius:4px; box-shadow:0 6px 24px rgba(0,0,0,.35);">
                        </div>

                        <a href="{{ route('animal-histories.index') }}" class="btn btn-secondary mt-3">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.leaflet')
    @include('partials.page-pad')
    <style>
        .sticky-summary{
            position: sticky;
            top: 0;
            z-index: 1020; /* above body content */
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            box-shadow: 0 2px 6px rgba(0,0,0,.05);
        }
        .legend-dot{
            display:inline-block;
            width:10px;
            height:10px;
            border-radius:50%;
            margin-right:4px;
        }
        .legend-dot-hallazgo{ background-color:#16a34a; }
        .legend-dot-transfer{ background-color:#2563eb; }
        .legend-dot-release{ background-color:#f59e0b; }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const overlay = document.getElementById('imageOverlay');
        const overlayImg = document.getElementById('overlayImg');
        const closeBtn = document.getElementById('overlayClose');
        document.querySelectorAll('.history-thumb').forEach(function(el){
            el.addEventListener('click', function(){
                const full = this.getAttribute('data-full') || this.src;
                if (overlay && overlayImg) {
                    overlayImg.src = full;
                    overlay.style.display = 'flex';
                }
            });
        });
        function hideOverlay(){
            if (overlay) {
                overlay.style.display = 'none';
                if (overlayImg) overlayImg.src = '';
            }
        }
        closeBtn?.addEventListener('click', hideOverlay);
        overlay?.addEventListener('click', function(e){ if (e.target === overlay) hideOverlay(); });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hideOverlay(); });

        // Toggle entre Timeline y Mapa
        const btnTimeline = document.getElementById('btnTimelineView');
        const btnMap = document.getElementById('btnMapView');
        const timelineContainer = document.getElementById('timelineContainer');
        const mapContainer = document.getElementById('mapContainer');
        let routeMap = null;

        function activateTimeline() {
            if (!timelineContainer || !mapContainer) return;
            timelineContainer.style.display = '';
            mapContainer.style.display = 'none';
            btnTimeline?.classList.add('active');
            btnMap?.classList.remove('active');
        }

        function activateMap() {
            if (!timelineContainer || !mapContainer) return;
            timelineContainer.style.display = 'none';
            mapContainer.style.display = '';
            btnMap?.classList.add('active');
            btnTimeline?.classList.remove('active');
            if (!routeMap) {
                initRouteMap();
            }
        }

        btnTimeline?.addEventListener('click', function (e) {
            e.preventDefault();
            activateTimeline();
        });
        btnMap?.addEventListener('click', function (e) {
            e.preventDefault();
            activateMap();
        });

        function initRouteMap() {
            if (routeMap) return;
            const mapEl = document.getElementById('animalRouteMap');
            if (!mapEl || !window.L) return;

            const data = @json($mapRoute ?? ['points' => []]);
            const points = Array.isArray(data.points) ? data.points : [];
            if (!points.length) return;

            routeMap = L.map('animalRouteMap');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(routeMap);

            const latlngs = [];
            points.forEach(function (p) {
                const lat = parseFloat(p.lat);
                const lon = parseFloat(p.lon);
                if (!Number.isFinite(lat) || !Number.isFinite(lon)) return;
                const latlng = [lat, lon];
                latlngs.push(latlng);

                let color = '#2563eb'; // traslado por defecto
                if (p.type === 'report') color = '#16a34a';
                if (p.type === 'release') color = '#f59e0b';

                const marker = L.circleMarker(latlng, {
                    radius: 7,
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.9
                }).addTo(routeMap);

                let popup = '';
                if (p.label) popup += '<strong>' + p.label + '</strong>';
                if (p.center_name) popup += '<br>Centro: ' + p.center_name;
                if (p.address) popup += '<br>Dirección: ' + p.address;
                if (p.date) popup += '<br>Fecha: ' + p.date;
                if (p.observaciones) popup += '<br>Obs: ' + p.observaciones;
                if (popup) marker.bindPopup(popup);
            });

            if (latlngs.length >= 2) {
                const poly = L.polyline(latlngs, {
                    color: '#2563eb',
                    weight: 4,
                    opacity: 0.8
                }).addTo(routeMap);
                routeMap.fitBounds(poly.getBounds().pad(0.2));
            } else if (latlngs.length === 1) {
                routeMap.setView(latlngs[0], 15);
            }
        }
    });
    </script>
@endsection


