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
                        <!-- Toggles independientes para cada módulo -->
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="togglePredictions" checked>
                                        <label class="form-check-label" for="togglePredictions">
                                            <i class="fas fa-fire"></i> {{ __('Predicciones de incendios (simulación)') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleFocosCalor" checked>
                                        <label class="form-check-label" for="toggleFocosCalor">
                                            <i class="fas fa-satellite"></i> {{ __('Focos de Calor NASA FIRMS') }}
                                        </label>
                                    </div>
                                </div>
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
                                        <strong>{{ __('Focos de Calor NASA FIRMS:') }}</strong>
                                    </div>
                                    <div class="mb-1">
                                        <span style="display: inline-block; width: 12px; height: 12px; background-color: #ff0000; border: 1px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.5); vertical-align: middle;"></span>
                                        <span class="ml-2">{{ __('Punto rojo - Foco de calor detectado') }}</span>
                                    </div>
                                    <div class="mb-2 mt-3">
                                        <strong>{{ __('Predicciones (Simulación):') }}</strong>
                                    </div>
                                    <div class="mb-1">
                                        <span style="display: inline-block; width: 20px; height: 20px; background-color: #ff8800; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                        <span class="ml-2">{{ __('Círculo - Predicción de propagación') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="mapaCampo" style="height: 400px; width: 100%; border-radius: 6px; border: 1px solid #dee2e6;"></div>
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
        let focosCalorMarkers = [];
        let loadedPredictions = new Set(); // Para evitar cargar predicciones duplicadas
        let showPredictions = true;
        let showFocosCalor = true;

        const reportsData = @json($reports ?? []);
        const focosCalorData = @json($focosCalorFormatted ?? []);

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
            
            // Agregar marcadores de focos de calor (desde BD, no API)
            addFocosCalorMarkers();

            // Toggle de predicciones (simulación)
            const togglePredictions = document.getElementById('togglePredictions');
            if (togglePredictions) {
                togglePredictions.addEventListener('change', function() {
                    showPredictions = this.checked;
                    updatePredictionLayers();
                });
            }

            // Toggle de focos de calor (NASA FIRMS)
            const toggleFocosCalor = document.getElementById('toggleFocosCalor');
            if (toggleFocosCalor) {
                toggleFocosCalor.addEventListener('change', function() {
                    showFocosCalor = this.checked;
                    updateFocosCalorMarkers();
                });
            }
        }
        
        function addFocosCalorMarkers() {
            if (!map) return;
            
            // Módulo independiente: Focos de Calor NASA FIRMS
            // Si no hay datos, simplemente no mostrar nada (no es error)
            if (!focosCalorData || focosCalorData.length === 0) {
                console.log('[Focos Calor] No hay datos de NASA FIRMS para mostrar');
                return;
            }
            
            console.log(`[Focos Calor] Agregando ${focosCalorData.length} focos de calor al mapa`);
            
            focosCalorData.forEach(function(foco) {
                if (!foco.lat || !foco.lng) return;
                
                // Crear punto rojo simple (no círculo)
                const point = L.circleMarker([foco.lat, foco.lng], {
                    radius: 6,
                    fillColor: '#ff0000',
                    color: '#ffffff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.9
                });
                
                // Solo agregar al mapa si el toggle está activado
                if (showFocosCalor) {
                    point.addTo(map);
                }
                
                // Popup con información del foco
                const confidence = foco.confidence || 0;
                const popupContent = `
                    <div style="min-width: 200px;">
                        <h6 style="margin: 0 0 8px 0; font-weight: bold;">
                            <i class="fas fa-satellite"></i> Foco de Calor NASA FIRMS
                        </h6>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Confianza:</strong> 
                            <span class="badge badge-${confidence >= 70 ? 'danger' : (confidence >= 30 ? 'warning' : 'secondary')}">
                                ${confidence}%
                            </span>
                        </div>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Fecha:</strong> ${foco.date}
                        </div>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Hora:</strong> ${foco.time}
                        </div>
                        ${foco.frp ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <strong>FRP:</strong> ${foco.frp.toFixed(2)} MW
                            </div>
                        ` : ''}
                        ${foco.brightness ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <strong>Brillo:</strong> ${foco.brightness.toFixed(2)} K
                            </div>
                        ` : ''}
                        <div style="font-size: 11px; color: #6c757d; margin-top: 8px;">
                            <i class="fas fa-info-circle"></i> Datos de NASA FIRMS
                        </div>
                    </div>
                `;
                
                point.bindPopup(popupContent);
                focosCalorMarkers.push(point);
            });
        }

        function updateFocosCalorMarkers() {
            focosCalorMarkers.forEach(function(marker) {
                if (showFocosCalor) {
                    if (!map.hasLayer(marker)) {
                        marker.addTo(map);
                    }
                } else {
                    if (map.hasLayer(marker)) {
                        map.removeLayer(marker);
                    }
                }
            });
        }

        function addReportsMarkers() {
            if (!map) return;

            console.log(`[Reportes] Agregando ${reportsData.length} reportes al mapa`);

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

                // Si tiene incendio_id, usar un marcador especial (simulación o reporte real con incendio)
                if (report.incendio_id) {
                    color = '#ff4444'; // rojo más intenso para incendios
                    iconClass = 'fa-fire';
                }
                
                // Marcar reporte simulado
                const isSimulado = report.id === 'simulado';

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
                            ${isSimulado ? `
                                <span class="badge badge-warning">
                                    <i class="fas fa-flask"></i> Simulación de demostración
                                </span>
                                <div style="margin-top: 4px; font-size: 10px; color: #6c757d;">
                                    Este reporte muestra la funcionalidad de predicción de incendios
                                </div>
                            ` : `
                                <a href="${window.location.origin}/reports/${report.id}" class="btn btn-sm btn-primary" target="_blank" style="color: white;">
                                    <i class="fas fa-eye"></i> Ver detalles
                                </a>
                            `}
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);

                markers.push({
                    report: report,
                    marker: marker
                });
            });

            // Módulo independiente: Predicciones de incendios (simulación)
            // Cargar predicciones para todos los reportes con incendio_id
            // Esto se hace después de agregar todos los marcadores
            if (showPredictions) {
                reportsData.forEach(function(report) {
                    if (report.incendio_id) {
                        console.log(`[Predicciones] Cargando predicción para reporte ${report.id} (incendio_id: ${report.incendio_id})`);
                        loadFirePrediction(report.incendio_id);
                    }
                });
            }

            // Ajustar vista para mostrar todos los marcadores
            if (markers.length > 0) {
                const group = new L.featureGroup(markers.map(m => m.marker));
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        function loadFirePrediction(focoIncendioId) {
            // Evitar cargar la misma predicción dos veces
            if (loadedPredictions.has(focoIncendioId)) {
                console.log(`[Predicciones] Predicción ${focoIncendioId} ya cargada, omitiendo`);
                return;
            }
            
            loadedPredictions.add(focoIncendioId);
            console.log(`[Predicciones] Solicitando predicción para foco_incendio_id: ${focoIncendioId}`);
            
            fetch(`/api/fire-predictions?foco_incendio_id=${focoIncendioId}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.data && data.data.length > 0) {
                        const prediction = data.data[0];
                        console.log(`[Predicciones] Predicción recibida, dibujando en mapa`);
                        drawFirePrediction(prediction);
                    } else {
                        console.warn(`[Predicciones] No se encontraron datos de predicción para foco_incendio_id: ${focoIncendioId}`);
                        // Si no hay datos, remover del set para permitir reintento
                        loadedPredictions.delete(focoIncendioId);
                    }
                })
                .catch(error => {
                    console.error(`[Predicciones] Error al cargar predicción para foco_incendio_id ${focoIncendioId}:`, error);
                    // Remover del set en caso de error para permitir reintento
                    loadedPredictions.delete(focoIncendioId);
                });
        }

        function drawFirePrediction(prediction) {
            if (!map || !prediction.path || !Array.isArray(prediction.path)) return;

            const path = prediction.path;
            const circles = [];
            let polyline = null;
            
            // Guardar el foco_incendio_id en las opciones para evitar duplicados
            const focoIncendioId = prediction.foco_incendio_id || prediction.id || null;

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
                        zIndexOffset: -100, // Asegurar que esté debajo
                        focoIncendioId: focoIncendioId // Guardar ID para evitar duplicados
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
                    zIndexOffset: 100, // Asegurar que esté encima de la línea
                    focoIncendioId: focoIncendioId // Guardar ID para evitar duplicados
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
            // Actualizar capas existentes
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

            // Si se activó el toggle y hay reportes con incendio_id, cargar predicciones
            if (showPredictions) {
                reportsData.forEach(function(report) {
                    if (report.incendio_id && !loadedPredictions.has(report.incendio_id)) {
                        loadFirePrediction(report.incendio_id);
                    }
                });
            }
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

