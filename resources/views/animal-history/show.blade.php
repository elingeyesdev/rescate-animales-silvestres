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
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="card-title">
                                {{ __('Detalles del animal') }}
                                @if($animalHistory->animalFile?->animal?->nombre)
                                    {{ ' ' . __('de') . ' ' . $animalHistory->animalFile->animal->nombre }}
                                @endif
                            </span>
                            <a href="{{ route('animal-histories.index') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-arrow-left"></i> {{ __('Volver') }}
                            </a>
                        </div>
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

                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="btn-group" role="group" aria-label="{{ __('Vista de historial') }}">
                                <button type="button" class="btn btn-outline-primary btn-sm active" id="btnTimelineView">
                                    {{ __('Línea de tiempo') }}
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btnMapView">
                                    {{ __('Mapa de traslados') }}
                                </button>
                            </div>
                            <a href="{{ route('animal-histories.pdf', $animalHistory->animal_file_id) }}" 
                               class="btn btn-danger btn-sm" 
                               target="_blank">
                                <i class="fas fa-file-pdf"></i> {{ __('Imprimir PDF') }}
                            </a>
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
                                                        <div class="history-thumb-container">
                                                            <img src="{{ asset('storage/' . $t['image_url']) }}"
                                                                 data-full="{{ asset('storage/' . $t['image_url']) }}"
                                                                 alt="Imagen"
                                                                 class="history-thumb">
                                                        </div>
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
                                <div class="mt-3 p-3 bg-light border rounded">
                                    <strong class="d-block mb-2">{{ __('Eventos') }}:</strong>
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="legend-dot legend-dot-hallazgo mr-2"></span>
                                                <div>
                                                    <strong>{{ __('Hallazgo') }}</strong>
                                                    <div class="small text-muted">{{ __('Punto donde se encontró al animal') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="legend-dot legend-dot-transfer mr-2"></span>
                                                <div>
                                                    <strong>{{ __('Traslado / Centro') }}</strong>
                                                    <div class="small text-muted">{{ __('Centro de rescate o traslado') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="legend-dot legend-dot-release mr-2"></span>
                                                <div>
                                                    <strong>{{ __('Liberación') }}</strong>
                                                    <div class="small text-muted">{{ __('Punto de liberación del animal') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-map-marker-alt text-danger mr-2" style="font-size: 18px;"></i>
                                                <div>
                                                    <strong>{{ __('Ubicación Actual') }}</strong>
                                                    <div class="small text-muted">{{ __('Última ubicación registrada') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
        .custom-marker {
            background: transparent;
            border: none;
        }
        .current-location-marker {
            background: transparent;
            border: none;
        }
        /* Contenedor para imágenes de la línea de tiempo */
        .history-thumb-container {
            width: 200px;
            height: 200px;
            margin-left: auto;
            margin-right: 0;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Imágenes de la línea de tiempo con tamaño uniforme */
        .history-thumb {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            cursor: zoom-in;
            transition: transform 0.2s ease;
        }
        .history-thumb:hover {
            transform: scale(1.05);
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 3px 6px rgba(0,0,0,0.4), 0 0 0 0 rgba(220, 53, 69, 0.7);
            }
            50% {
                box-shadow: 0 3px 6px rgba(0,0,0,0.4), 0 0 0 10px rgba(220, 53, 69, 0);
            }
            100% {
                box-shadow: 0 3px 6px rgba(0,0,0,0.4), 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }
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
            const markers = [];
            let currentLocationMarker = null;

            // Procesar puntos y crear markers
            points.forEach(function (p, index) {
                const lat = parseFloat(p.lat);
                const lon = parseFloat(p.lon);
                if (!Number.isFinite(lat) || !Number.isFinite(lon)) return;
                const latlng = [lat, lon];
                latlngs.push(latlng);

                let color = '#2563eb'; // traslado por defecto
                let iconClass = 'fa-map-marker-alt';
                let eventType = 'Traslado';
                let eventDescription = 'Centro de rescate o traslado';
                if (p.type === 'report') {
                    color = '#16a34a';
                    iconClass = 'fa-flag';
                    eventType = 'Hallazgo';
                    eventDescription = 'Punto donde se encontró al animal';
                } else if (p.type === 'release') {
                    color = '#f59e0b';
                    iconClass = 'fa-dove';
                    eventType = 'Liberación';
                    eventDescription = 'Punto de liberación del animal';
                } else if (p.type === 'transfer') {
                    color = '#2563eb';
                    iconClass = 'fa-truck';
                    eventType = 'Traslado / Centro';
                    eventDescription = 'Centro de rescate o traslado';
                }

                // Crear marker con icono personalizado y tooltip
                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background-color: ' + color + '; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;" title="' + eventType + ': ' + eventDescription + '"><i class="fas ' + iconClass + '" style="color: white; font-size: 10px;"></i></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });

                const marker = L.marker(latlng, { icon: icon }).addTo(routeMap);
                markers.push(marker);
                
                // Agregar tooltip al marker para mostrar información al hacer hover
                let tooltipText = eventType;
                if (p.label) tooltipText += ': ' + p.label;
                if (p.center_name) tooltipText += ' - ' + p.center_name;
                if (p.date) tooltipText += ' (' + p.date + ')';
                marker.bindTooltip(tooltipText, {
                    permanent: false,
                    direction: 'top',
                    offset: [0, -10]
                });

                // Construir popup con más información
                let popup = '<div style="min-width: 200px;">';
                if (p.label) popup += '<strong style="color: ' + color + '; font-size: 14px;">' + p.label + '</strong>';
                if (p.type === 'report') {
                    popup += '<div class="mt-2"><i class="fas fa-info-circle"></i> <strong>Evento:</strong> Hallazgo del animal</div>';
                } else if (p.type === 'transfer') {
                    popup += '<div class="mt-2"><i class="fas fa-info-circle"></i> <strong>Evento:</strong> Traslado</div>';
                } else if (p.type === 'release') {
                    popup += '<div class="mt-2"><i class="fas fa-info-circle"></i> <strong>Evento:</strong> Liberación</div>';
                }
                if (p.center_name) popup += '<div class="mt-1"><i class="fas fa-building"></i> <strong>Centro:</strong> ' + p.center_name + '</div>';
                if (p.address) popup += '<div class="mt-1"><i class="fas fa-map-pin"></i> <strong>Dirección:</strong> ' + p.address + '</div>';
                if (p.date) popup += '<div class="mt-1"><i class="fas fa-calendar"></i> <strong>Fecha:</strong> ' + p.date + '</div>';
                if (p.observaciones) popup += '<div class="mt-1"><i class="fas fa-comment"></i> <strong>Observaciones:</strong> ' + p.observaciones + '</div>';
                popup += '</div>';
                
                marker.bindPopup(popup);

                // Marcar el último punto como ubicación actual
                if (index === points.length - 1) {
                    const currentIcon = L.divIcon({
                        className: 'current-location-marker',
                        html: '<div style="background-color: #dc3545; width: 28px; height: 28px; border-radius: 50%; border: 3px solid white; box-shadow: 0 3px 6px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; animation: pulse 2s infinite;"><i class="fas fa-map-marker-alt" style="color: white; font-size: 14px;"></i></div>',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    });
                    currentLocationMarker = L.marker(latlng, { icon: currentIcon, zIndexOffset: 1000 }).addTo(routeMap);
                    
                    // Construir popup para ubicación actual con información del tipo de evento
                    let currentPopup = '<div style="min-width: 200px;">';
                    currentPopup += '<strong style="color: #dc3545; font-size: 16px;">📍 Ubicación Actual</strong>';
                    currentPopup += '<div class="mt-2" style="font-weight: bold; color: ' + color + ';">';
                    if (p.type === 'report') {
                        currentPopup += '<i class="fas fa-flag"></i> Tipo: Hallazgo del animal';
                    } else if (p.type === 'transfer') {
                        currentPopup += '<i class="fas fa-truck"></i> Tipo: Traslado';
                    } else if (p.type === 'release') {
                        currentPopup += '<i class="fas fa-dove"></i> Tipo: Liberación';
                    } else {
                        currentPopup += '<i class="fas fa-map-marker-alt"></i> Tipo: ' + (p.label || 'Evento');
                    }
                    currentPopup += '</div>';
                    if (p.label) currentPopup += '<div class="mt-1"><i class="fas fa-tag"></i> <strong>Etiqueta:</strong> ' + p.label + '</div>';
                    if (p.center_name) currentPopup += '<div class="mt-1"><i class="fas fa-building"></i> <strong>Centro:</strong> ' + p.center_name + '</div>';
                    if (p.address) currentPopup += '<div class="mt-1"><i class="fas fa-map-pin"></i> <strong>Dirección:</strong> ' + p.address + '</div>';
                    if (p.date) currentPopup += '<div class="mt-1"><i class="fas fa-calendar"></i> <strong>Fecha:</strong> ' + p.date + '</div>';
                    if (p.observaciones) currentPopup += '<div class="mt-1"><i class="fas fa-comment"></i> <strong>Observaciones:</strong> ' + p.observaciones + '</div>';
                    currentPopup += '</div>';
                    
                    currentLocationMarker.bindPopup(currentPopup);
                    
                    // Agregar tooltip al marcador de ubicación actual
                    let currentTooltipText = '📍 Ubicación Actual - ' + eventType;
                    if (p.label) currentTooltipText += ': ' + p.label;
                    if (p.center_name) currentTooltipText += ' - ' + p.center_name;
                    if (p.date) currentTooltipText += ' (' + p.date + ')';
                    currentLocationMarker.bindTooltip(currentTooltipText, {
                        permanent: false,
                        direction: 'top',
                        offset: [0, -14]
                    });
                }
            });

            // Dibujar ruta con routing que respeta las calles
            if (latlngs.length >= 2) {
                // Usar OSRM para obtener ruta que respeta las calles
                const osrmUrl = 'https://router.project-osrm.org/route/v1/driving/';
                let waypoints = latlngs.map(p => p[1] + ',' + p[0]).join(';');
                
                fetch(osrmUrl + waypoints + '?overview=full&geometries=geojson')
                    .then(response => response.json())
                    .then(data => {
                        if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                            const route = data.routes[0];
                            const coordinates = route.geometry.coordinates.map(c => [c[1], c[0]]); // OSRM devuelve [lon, lat], Leaflet usa [lat, lon]
                            
                            const routePolyline = L.polyline(coordinates, {
                                color: '#2563eb',
                                weight: 5,
                                opacity: 0.7,
                                smoothFactor: 1
                            }).addTo(routeMap);
                            
                            // Ajustar vista para mostrar toda la ruta
                            const bounds = routePolyline.getBounds();
                            routeMap.fitBounds(bounds.pad(0.15));
                        } else {
                            // Fallback: polyline directo si OSRM falla
                            const fallbackPoly = L.polyline(latlngs, {
                                color: '#2563eb',
                                weight: 4,
                                opacity: 0.6,
                                dashArray: '10, 5'
                            }).addTo(routeMap);
                            routeMap.fitBounds(fallbackPoly.getBounds().pad(0.2));
                        }
                    })
                    .catch(error => {
                        console.warn('Error obteniendo ruta desde OSRM, usando polyline directo:', error);
                        // Fallback: polyline directo si hay error
                        const fallbackPoly = L.polyline(latlngs, {
                            color: '#2563eb',
                            weight: 4,
                            opacity: 0.6,
                            dashArray: '10, 5'
                        }).addTo(routeMap);
                        routeMap.fitBounds(fallbackPoly.getBounds().pad(0.2));
                    });
            } else if (latlngs.length === 1) {
                routeMap.setView(latlngs[0], 15);
            }
        }
    });
    </script>
@endsection


