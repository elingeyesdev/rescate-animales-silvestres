@extends('adminlte::page')

@section('template_title')
    {{ __('Mapa de Campo') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                <i class="fas fa-map-marked-alt"></i> {{ __('Mapa de Campo') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm float-right">
                                    {{ __('Volver a Hallazgos') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <!-- Checkbox de predicciones -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="togglePredictions" checked>
                                <label class="form-check-label" for="togglePredictions">
                                    <i class="fas fa-fire"></i> {{ __('Mostrar predicciones de incendios') }}
                                </label>
                            </div>
                        </div>

                        <!-- Leyenda -->
                        <div class="mb-3 p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <strong>{{ __('Hallazgos por Urgencia:') }}</strong>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-danger mr-2" style="font-size: 14px; padding: 6px 10px;">
                                            <i class="fas fa-exclamation-circle"></i> {{ __('Alta (4-5)') }}
                                        </span>
                                        <span class="badge badge-warning mr-2" style="font-size: 14px; padding: 6px 10px;">
                                            <i class="fas fa-exclamation-triangle"></i> {{ __('Media (3)') }}
                                        </span>
                                        <span class="badge badge-info mr-2" style="font-size: 14px; padding: 6px 10px;">
                                            <i class="fas fa-info-circle"></i> {{ __('Baja (1-2)') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <strong>{{ __('Incendios:') }}</strong>
                                    </div>
                                    <div class="mb-1">
                                        <span style="display: inline-block; width: 20px; height: 20px; background-color: #ff4444; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                        <span class="ml-2">{{ __('Hallazgo en incendio') }}</span>
                                    </div>
                                    <div class="mb-1">
                                        <span style="display: inline-block; width: 20px; height: 20px; background-color: #ff8800; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                        <span class="ml-2">{{ __('Predicción de propagación') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="mapaCampo" style="height: 600px; width: 100%; border-radius: 6px; border: 1px solid #dee2e6;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
    @include('partials.leaflet')

    <script>
    (function() {
        let map = null;
        let markers = [];
        let predictionLayers = [];
        let showPredictions = true;

        const reportsData = @json($reports ?? []);

        function initMap() {
            if (typeof L === 'undefined') {
                setTimeout(initMap, 100);
                return;
            }

            const mapEl = document.getElementById('mapaCampo');
            if (!mapEl) return;

            // Inicializar mapa centrado en Santa Cruz
            map = L.map('mapaCampo').setView([-17.7833, -63.1821], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
                maxZoom: 19 
            }).addTo(map);

            // Agregar marcadores de hallazgos
            addReportsMarkers();

            // Toggle de predicciones
            const togglePredictions = document.getElementById('togglePredictions');
            if (togglePredictions) {
                togglePredictions.addEventListener('change', function() {
                    showPredictions = this.checked;
                    updatePredictionLayers();
                });
            }
        }

        function addReportsMarkers() {
            if (!map) return;

            reportsData.forEach(function(report) {
                if (!report.latitud || !report.longitud) return;

                const lat = parseFloat(report.latitud);
                const lng = parseFloat(report.longitud);
                
                if (isNaN(lat) || isNaN(lng)) return;

                // Determinar color según urgencia
                const urgencia = report.urgencia;
                let color = '#6c757d'; // gris por defecto
                let iconClass = 'fa-map-marker-alt';

                if (urgencia !== null && urgencia !== undefined) {
                    if (urgencia >= 4) {
                        color = '#dc3545'; // rojo - alta
                    } else if (urgencia === 3) {
                        color = '#ffc107'; // amarillo - media
                    } else if (urgencia <= 2) {
                        color = '#17a2b8'; // azul - baja
                    }
                }

                // Si tiene incendio_id, usar un marcador especial
                if (report.incendio_id) {
                    color = '#ff4444'; // rojo más intenso para incendios
                    iconClass = 'fa-fire';
                }

                // Crear icono personalizado
                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: ${color}; width: 28px; height: 28px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;">
                        <i class="fas ${iconClass}" style="color: white; font-size: 14px;"></i>
                    </div>`,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14],
                });

                const marker = L.marker([lat, lng], { icon: icon }).addTo(map);

                // Popup con información del hallazgo
                const condicionNombre = report.condicion_inicial && report.condicion_inicial.nombre 
                    ? report.condicion_inicial.nombre 
                    : 'Hallazgo';
                const incidenteNombre = report.incident_type && report.incident_type.nombre 
                    ? report.incident_type.nombre 
                    : null;
                
                let popupContent = `
                    <div style="min-width: 200px;">
                        <h6 style="margin: 0 0 8px 0; font-weight: bold;">
                            <i class="fas fa-clipboard-list"></i> ${condicionNombre}
                        </h6>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Urgencia:</strong> 
                            <span class="badge badge-${urgencia >= 4 ? 'danger' : (urgencia === 3 ? 'warning' : 'info')}">
                                ${urgencia || 'N/A'}
                            </span>
                        </div>
                        ${report.incendio_id ? `
                            <div style="font-size: 12px; margin-bottom: 4px; color: #ff4444;">
                                <i class="fas fa-fire"></i> <strong>Hallazgo en incendio</strong>
                            </div>
                        ` : ''}
                        ${incidenteNombre ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <strong>Incidente:</strong> ${incidenteNombre}
                            </div>
                        ` : ''}
                        ${report.direccion ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <i class="fas fa-map-pin"></i> ${report.direccion}
                            </div>
                        ` : ''}
                        <div style="font-size: 11px; color: #6c757d; margin-top: 8px;">
                            ${report.id !== 'simulado' ? `
                                <a href="${window.location.origin}/reports/${report.id}" class="btn btn-sm btn-primary" target="_blank" style="color: white;">
                                    <i class="fas fa-eye"></i> Ver detalles
                                </a>
                            ` : `
                                <span class="badge badge-info">Reporte simulado para demostración</span>
                            `}
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);

                markers.push({
                    report: report,
                    marker: marker
                });

                // Si tiene incendio_id, cargar predicción
                if (report.incendio_id && showPredictions) {
                    loadFirePrediction(report.incendio_id);
                }
            });

            // Ajustar vista para mostrar todos los marcadores
            if (markers.length > 0) {
                const group = new L.featureGroup(markers.map(m => m.marker));
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        function loadFirePrediction(focoIncendioId) {
            fetch(`/api/fire-predictions?foco_incendio_id=${focoIncendioId}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.data && data.data.length > 0) {
                        const prediction = data.data[0];
                        drawFirePrediction(prediction);
                    }
                })
                .catch(error => {
                    console.error('Error al cargar predicción:', error);
                });
        }

        function drawFirePrediction(prediction) {
            if (!map || !prediction.path || !Array.isArray(prediction.path)) return;

            const path = prediction.path;
            const circles = [];
            let polyline = null;

            // Primero crear la línea punteada (para que quede debajo de los círculos)
            if (path.length > 1 && showPredictions) {
                const latlngs = path
                    .filter(p => p.lat && p.lng)
                    .map(p => [p.lat, p.lng]);

                if (latlngs.length > 1) {
                    polyline = L.polyline(latlngs, {
                        color: '#ff0000',
                        weight: 4,
                        opacity: 0.8,
                        dashArray: '10, 5',
                        zIndexOffset: -100 // Asegurar que esté debajo
                    });
                    
                    if (showPredictions) {
                        polyline.addTo(map);
                    }
                    predictionLayers.push(polyline);
                }
            }

            // Luego crear los círculos (quedarán encima de la línea)
            path.forEach(function(point, index) {
                if (!point.lat || !point.lng) return;

                const radius = (point.spread_radius_km || 0) * 1000; // convertir km a metros
                const intensity = point.intensity || 0;

                // Color según intensidad (0-10) - más visibles
                let color = '#ff8800';
                let borderColor = '#ff6600';
                let opacity = 0.4;
                let borderWidth = 3;
                
                if (intensity >= 7) {
                    color = '#ff0000';
                    borderColor = '#cc0000';
                    opacity = 0.6;
                    borderWidth = 4;
                } else if (intensity >= 5) {
                    color = '#ff4400';
                    borderColor = '#ff2200';
                    opacity = 0.5;
                    borderWidth = 3;
                } else if (intensity >= 3) {
                    color = '#ff8800';
                    borderColor = '#ff6600';
                    opacity = 0.4;
                    borderWidth = 2;
                }

                // Crear círculo de propagación
                const circle = L.circle([point.lat, point.lng], {
                    radius: radius,
                    color: borderColor,
                    fillColor: color,
                    fillOpacity: opacity,
                    weight: borderWidth,
                    zIndexOffset: 100 // Asegurar que esté encima de la línea
                });

                if (showPredictions) {
                    circle.addTo(map);
                }

                circles.push(circle);

                // Agregar popup al círculo
                const popupContent = `
                    <div style="min-width: 180px;">
                        <h6 style="margin: 0 0 8px 0;">
                            <i class="fas fa-fire"></i> Predicción - Hora ${point.hour}
                        </h6>
                        <div style="font-size: 12px;">
                            <div><strong>Intensidad:</strong> ${intensity.toFixed(2)}</div>
                            <div><strong>Radio:</strong> ${point.spread_radius_km?.toFixed(2) || 0} km</div>
                            <div><strong>Área afectada:</strong> ${point.affected_area_km2?.toFixed(2) || 0} km²</div>
                            <div><strong>Perímetro:</strong> ${point.perimeter_km?.toFixed(2) || 0} km</div>
                        </div>
                    </div>
                `;
                circle.bindPopup(popupContent);
            });

            predictionLayers.push(...circles);
        }

        function updatePredictionLayers() {
            predictionLayers.forEach(function(layer) {
                if (showPredictions) {
                    if (!map.hasLayer(layer)) {
                        map.addLayer(layer);
                    }
                } else {
                    if (map.hasLayer(layer)) {
                        map.removeLayer(layer);
                    }
                }
            });
        }

        // Inicializar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMap);
        } else {
            initMap();
        }
    })();
    </script>

    <style>
    .custom-marker {
        background: transparent;
        border: none;
    }
    .prediction-marker {
        background: transparent;
        border: none;
    }
    .prediction-marker div {
        animation: pulse-prediction 2s infinite;
    }
    @keyframes pulse-prediction {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.8;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    </style>
@endsection

