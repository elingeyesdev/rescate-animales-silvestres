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

                        <!-- Contenedor del mapa con posición relativa para controles flotantes -->
                        <div style="position: relative; width: 100%;">
                            <div id="mapaCampo" style="height: 600px; width: 100%; border-radius: 6px; border: 1px solid #dee2e6;"></div>
                            
                            <!-- Controles flotantes dentro del mapa -->
                            <div class="map-controls" style="position: absolute; top: 10px; left: 10px; z-index: 1000; background: white; padding: 10px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); max-width: 280px;">
                                <!-- Filtro por especie -->
                                <div class="mb-2">
                                    <label for="filterSpecies" class="form-label" style="font-size: 12px; font-weight: bold; margin-bottom: 4px;">
                                        <i class="fas fa-filter"></i> {{ __('Especie') }}
                                    </label>
                                    <select class="form-control form-control-sm" id="filterSpecies" style="font-size: 12px;">
                                        <option value="">{{ __('Todas') }}</option>
                                        @foreach($species ?? [] as $specie)
                                            <option value="{{ $specie->id }}">{{ $specie->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Toggles compactos -->
                                <div style="font-size: 11px;">
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="toggleReports" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="toggleReports" style="font-size: 11px;">
                                            <i class="fas fa-clipboard-list"></i> {{ __('Hallazgos') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="toggleAnimals" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="toggleAnimals" style="font-size: 11px;">
                                            <i class="fas fa-paw"></i> {{ __('Animales') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="toggleReleases" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="toggleReleases" style="font-size: 11px;">
                                            <i class="fas fa-dove"></i> {{ __('Liberaciones') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="toggleCenters" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="toggleCenters" style="font-size: 11px;">
                                            <i class="fas fa-home"></i> {{ __('Centros') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="togglePredictions" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="togglePredictions" style="font-size: 11px;">
                                            <i class="fas fa-fire"></i> {{ __('Predicciones') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="toggleFocosCalor" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="toggleFocosCalor" style="font-size: 11px;">
                                            <i class="fas fa-satellite"></i> {{ __('Focos') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Leyenda flotante en la esquina inferior derecha -->
                            <div class="map-legend" style="position: absolute; bottom: 10px; right: 10px; z-index: 1000; background: white; padding: 8px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); max-width: 220px; font-size: 11px;">
                                <div style="font-weight: bold; margin-bottom: 6px; font-size: 12px;">
                                    <i class="fas fa-info-circle"></i> {{ __('Leyenda') }}
                                </div>
                                <div style="margin-bottom: 4px;">
                                    <span style="display: inline-block; width: 14px; height: 14px; background-color: #dc3545; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                    <span style="margin-left: 4px;">{{ __('Alta') }}</span>
                                    <span style="display: inline-block; width: 14px; height: 14px; background-color: #ffc107; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle; margin-left: 8px;"></span>
                                    <span style="margin-left: 4px;">{{ __('Media') }}</span>
                                    <span style="display: inline-block; width: 14px; height: 14px; background-color: #17a2b8; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle; margin-left: 8px;"></span>
                                    <span style="margin-left: 4px;">{{ __('Baja') }}</span>
                                </div>
                                <div style="margin-bottom: 4px;">
                                    <span style="display: inline-block; width: 14px; height: 14px; background-color: #28a745; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                    <span style="margin-left: 4px;">{{ __('Animal') }}</span>
                                    <span style="display: inline-block; width: 14px; height: 14px; background-color: #007bff; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle; margin-left: 8px;"></span>
                                    <span style="margin-left: 4px;">{{ __('Liberación') }}</span>
                                </div>
                                <div style="margin-bottom: 4px;">
                                    <span style="display: inline-block; width: 14px; height: 14px; background-color: #6f42c1; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                    <span style="margin-left: 4px;">{{ __('Centro') }}</span>
                                    <span style="display: inline-block; width: 10px; height: 10px; background-color: #ff0000; border: 1px solid #fff; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.3); vertical-align: middle; margin-left: 8px;"></span>
                                    <span style="margin-left: 4px;">{{ __('Foco') }}</span>
                                </div>
                            </div>
                        </div>
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
        let animalMarkers = [];
        let releaseMarkers = [];
        let centerMarkers = [];
        let predictionLayers = [];
        let focosCalorMarkers = [];
        let loadedPredictions = new Set(); // Para evitar cargar predicciones duplicadas
        let showReports = true;
        let showAnimals = true;
        let showReleases = true;
        let showCenters = true;
        let showPredictions = true;
        let showFocosCalor = true;
        let selectedSpeciesId = null;

        const reportsData = @json($reports ?? []);
        const animalsData = @json($animals ?? []);
        const releasesData = @json($releases ?? []);
        const centersData = @json($centers ?? []);
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
            
            // Agregar marcadores de animales
            addAnimalMarkers();
            
            // Agregar marcadores de liberaciones
            addReleaseMarkers();
            
            // Agregar marcadores de centros
            addCenterMarkers();
            
            // Agregar marcadores de focos de calor (desde BD, no API)
            addFocosCalorMarkers();

            // Toggle de reportes/hallazgos
            const toggleReports = document.getElementById('toggleReports');
            if (toggleReports) {
                toggleReports.addEventListener('change', function() {
                    showReports = this.checked;
                    updateReportsMarkers();
                });
            }

            // Toggle de animales
            const toggleAnimals = document.getElementById('toggleAnimals');
            if (toggleAnimals) {
                toggleAnimals.addEventListener('change', function() {
                    showAnimals = this.checked;
                    updateAnimalMarkers();
                });
            }

            // Toggle de liberaciones
            const toggleReleases = document.getElementById('toggleReleases');
            if (toggleReleases) {
                toggleReleases.addEventListener('change', function() {
                    showReleases = this.checked;
                    updateReleaseMarkers();
                });
            }

            // Toggle de centros
            const toggleCenters = document.getElementById('toggleCenters');
            if (toggleCenters) {
                toggleCenters.addEventListener('change', function() {
                    showCenters = this.checked;
                    updateCenterMarkers();
                });
            }

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

            // Filtro por especie
            const filterSpecies = document.getElementById('filterSpecies');
            if (filterSpecies) {
                filterSpecies.addEventListener('change', function() {
                    selectedSpeciesId = this.value ? parseInt(this.value) : null;
                    updateAnimalMarkers();
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
                            <i class="fas fa-satellite"></i> Foco de Calor
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
                        ${foco.frp ? `
                            <!--<div style="font-size: 12px; margin-bottom: 4px;">
                                <strong>FRP:</strong> ${foco.frp.toFixed(2)} MW
                            </div>-->
                        ` : ''}
                        ${foco.brightness ? `
                            <!--<div style="font-size: 12px; margin-bottom: 4px;">
                                <strong>Brillo:</strong> ${foco.brightness.toFixed(2)} K
                            </div>-->
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

        function addAnimalMarkers() {
            if (!map) return;

            console.log(`[Animales] Agregando ${animalsData.length} animales al mapa`);

            animalsData.forEach(function(animal) {
                if (!animal.latitud || !animal.longitud) return;

                const lat = parseFloat(animal.latitud);
                const lng = parseFloat(animal.longitud);
                
                if (isNaN(lat) || isNaN(lng)) return;

                // Crear icono personalizado para animales (verde)
                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: #28a745; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-paw" style="color: white; font-size: 12px;"></i>
                    </div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
                });

                const marker = L.marker([lat, lng], { icon: icon });
                
                if (showAnimals && (!selectedSpeciesId || animal.especie_id == selectedSpeciesId)) {
                    marker.addTo(map);
                }

                // Popup con información del animal
                const especieNombre = animal.especie && animal.especie.nombre 
                    ? animal.especie.nombre 
                    : 'Especie no identificada';
                const estadoNombre = animal.estado && animal.estado.nombre 
                    ? animal.estado.nombre 
                    : 'Estado no disponible';
                
                let popupContent = `
                    <div style="min-width: 200px;">
                        <h6 style="margin: 0 0 8px 0; font-weight: bold;">
                            <i class="fas fa-paw"></i> ${animal.nombre || 'Animal #' + animal.id}
                        </h6>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Especie:</strong> ${especieNombre}
                        </div>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Estado:</strong> ${estadoNombre}
                        </div>
                        ${animal.direccion ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <i class="fas fa-map-pin"></i> ${animal.direccion}
                            </div>
                        ` : ''}
                        <div style="font-size: 11px; color: #6c757d; margin-top: 8px;">
                            ${animal.reporte_id ? `
                                <a href="${window.location.origin}/reports/${animal.reporte_id}" class="btn btn-sm btn-primary" target="_blank" style="color: white;">
                                    <i class="fas fa-eye"></i> Ver reporte
                                </a>
                            ` : ''}
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);

                animalMarkers.push({
                    animal: animal,
                    marker: marker
                });
            });
        }

        function updateAnimalMarkers() {
            animalMarkers.forEach(function(item) {
                const animal = item.animal;
                const marker = item.marker;
                
                const shouldShow = showAnimals && (!selectedSpeciesId || animal.especie_id == selectedSpeciesId);
                
                if (shouldShow) {
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

        function addReleaseMarkers() {
            if (!map) return;

            console.log(`[Liberaciones] Agregando ${releasesData.length} liberaciones al mapa`);

            releasesData.forEach(function(release) {
                if (!release.latitud || !release.longitud) return;

                const lat = parseFloat(release.latitud);
                const lng = parseFloat(release.longitud);
                
                if (isNaN(lat) || isNaN(lng)) return;

                // Crear icono personalizado para liberaciones (azul)
                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: #007bff; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-dove" style="color: white; font-size: 12px;"></i>
                    </div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
                });

                const marker = L.marker([lat, lng], { icon: icon });
                
                if (showReleases) {
                    marker.addTo(map);
                }

                // Popup con información de la liberación
                const especieNombre = release.especie && release.especie.nombre 
                    ? release.especie.nombre 
                    : 'Especie no identificada';
                const animalNombre = release.animal && release.animal.nombre 
                    ? release.animal.nombre 
                    : 'Animal #' + (release.animal ? release.animal.id : 'N/A');
                
                let popupContent = `
                    <div style="min-width: 200px;">
                        <h6 style="margin: 0 0 8px 0; font-weight: bold;">
                            <i class="fas fa-dove"></i> Liberación
                        </h6>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Animal:</strong> ${animalNombre}
                        </div>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>Especie:</strong> ${especieNombre}
                        </div>
                        ${release.fecha ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <strong>Fecha:</strong> ${release.fecha}
                            </div>
                        ` : ''}
                        ${release.direccion ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <i class="fas fa-map-pin"></i> ${release.direccion}
                            </div>
                        ` : ''}
                        ${release.detalle ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <strong>Detalle:</strong> ${release.detalle}
                            </div>
                        ` : ''}
                    </div>
                `;

                marker.bindPopup(popupContent);

                releaseMarkers.push({
                    release: release,
                    marker: marker
                });
            });
        }

        function updateReleaseMarkers() {
            releaseMarkers.forEach(function(item) {
                const marker = item.marker;
                if (showReleases) {
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

        function addCenterMarkers() {
            if (!map) return;

            console.log(`[Centros] Agregando ${centersData.length} centros al mapa`);

            centersData.forEach(function(center) {
                if (!center.latitud || !center.longitud) return;

                const lat = parseFloat(center.latitud);
                const lng = parseFloat(center.longitud);
                
                if (isNaN(lat) || isNaN(lng)) return;

                // Crear icono personalizado para centros (morado)
                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: #6f42c1; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-home" style="color: white; font-size: 12px;"></i>
                    </div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
                });

                const marker = L.marker([lat, lng], { icon: icon });
                
                if (showCenters) {
                    marker.addTo(map);
                }

                // Popup con información del centro
                let popupContent = `
                    <div style="min-width: 200px;">
                        <h6 style="margin: 0 0 8px 0; font-weight: bold;">
                            <i class="fas fa-home"></i> ${center.nombre}
                        </h6>
                        ${center.direccion ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <i class="fas fa-map-pin"></i> ${center.direccion}
                            </div>
                        ` : ''}
                        ${center.contacto ? `
                            <div style="font-size: 12px; margin-bottom: 4px;">
                                <i class="fas fa-phone"></i> ${center.contacto}
                            </div>
                        ` : ''}
                    </div>
                `;

                marker.bindPopup(popupContent);

                centerMarkers.push({
                    center: center,
                    marker: marker
                });
            });
        }

        function updateCenterMarkers() {
            centerMarkers.forEach(function(item) {
                const marker = item.marker;
                if (showCenters) {
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

        function updateReportsMarkers() {
            markers.forEach(function(item) {
                const marker = item.marker;
                if (showReports) {
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

                const marker = L.marker([lat, lng], { icon: icon });
                
                if (showReports) {
                    marker.addTo(map);
                }

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
                                <!--<div style="margin-top: 4px; font-size: 10px; color: #6c757d;">
                                    Este reporte muestra la funcionalidad de predicción de incendios
                                </div>-->
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

            // Ajustar vista para mostrar todos los marcadores visibles
            const allVisibleMarkers = [
                ...markers.filter(m => showReports).map(m => m.marker),
                ...animalMarkers.filter(m => showAnimals && (!selectedSpeciesId || m.animal.especie_id == selectedSpeciesId)).map(m => m.marker),
                ...releaseMarkers.filter(m => showReleases).map(m => m.marker),
                ...centerMarkers.filter(m => showCenters).map(m => m.marker)
            ];
            
            if (allVisibleMarkers.length > 0) {
                const group = new L.featureGroup(allVisibleMarkers);
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
    .map-controls {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .map-controls .form-check-inline {
        display: block;
        margin-right: 0;
    }
    .map-controls .form-check-label {
        cursor: pointer;
        user-select: none;
    }
    .map-controls .form-check-label:hover {
        color: #007bff;
    }
    .map-legend {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    @media (max-width: 768px) {
        .map-controls {
            max-width: 240px;
            padding: 8px;
            font-size: 10px;
        }
        .map-controls .form-control-sm {
            font-size: 11px;
        }
        .map-legend {
            max-width: 180px;
            font-size: 10px;
            padding: 6px;
        }
    }
    </style>
@endsection

