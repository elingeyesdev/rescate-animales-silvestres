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
                        <!-- Leyenda de urgencia fuera del mapa -->
                        <div class="mb-3">
                            <div class="mb-2">
                                <strong><i class="fas fa-exclamation-triangle"></i> {{ __('Hallazgos por Urgencia:') }}</strong>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-danger mr-2" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fas fa-exclamation-circle"></i> {{ __('Alta (4-5)') }}
                                </span>
                                <span class="badge badge-warning mr-2" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fas fa-exclamation-triangle"></i> {{ __('Media (3)') }}
                                </span>
                                <span class="badge badge-info mr-2" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fas fa-info-circle"></i> {{ __('Baja (1-2)') }}
                                </span>
                            </div>
                        </div>

                        <!-- Contenedor del mapa con posición relativa para controles flotantes -->
                        <div style="position: relative; width: 100%; min-height: 500px;">
                            <div id="mapaCampo" style="height: 500px; width: 100%; border-radius: 6px; border: 1px solid #dee2e6; background-color: #f0f0f0;"></div>
                            
                            <!-- Controles flotantes dentro del mapa -->
                            <div class="map-controls" style="position: absolute; top: 10px; left: 10px; z-index: 1000; background: white; padding: 10px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); max-width: 280px;">
                                <!-- Filtro de estado de hallazgos -->
                                <div class="mb-2">
                                    <label for="filterReportStatus" class="form-label" style="font-size: 12px; font-weight: bold; margin-bottom: 4px;">
                                        <i class="fas fa-filter"></i> {{ __('Estado Hallazgos') }}
                                    </label>
                                    <select class="form-control form-control-sm" id="filterReportStatus" style="font-size: 12px;">
                                        <option value="all">{{ __('Hallazgos aprobados') }}</option>
                                        <option value="with_file">{{ __('Tienen hoja de animal') }}</option>
                                        <option value="without_file">{{ __('Pendientes') }}</option>
                                    </select>
                                </div>
                                
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
                                            <i class="fas fa-clipboard-list"></i> {{ __('Hallazgos Aprobados') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="toggleReleases" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="toggleReleases" style="font-size: 11px;">
                                            <i class="fas fa-dove"></i> {{ __('Animales Liberados') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="toggleFocosCalor" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="toggleFocosCalor" style="font-size: 11px;">
                                            <i class="fas fa-satellite"></i> {{ __('Focos') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox" id="togglePredictions" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="togglePredictions" style="font-size: 11px;">
                                            <i class="fas fa-fire"></i> {{ __('Predicciones') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Panel de clima en la esquina superior derecha -->
                            <div id="weatherPanel" class="weather-panel" style="position: absolute; top: 10px; right: 10px; z-index: 1000; background: white; padding: 0; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); max-width: 200px; min-width: 170px; display: none;">
                                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 8px; border-radius: 6px 6px 0 0;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                        <h6 style="margin: 0; font-weight: bold; color: white; font-size: 12px;">
                                            <i class="fas fa-cloud-sun"></i> Clima
                                        </h6>
                                        <button id="closeWeatherPanel" style="background: rgba(255,255,255,0.2); border: none; color: white; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div id="weatherLoading" style="text-align: center; color: white; padding: 12px;">
                                        <i class="fas fa-spinner fa-spin" style="font-size: 18px; margin-bottom: 6px;"></i>
                                        <div style="font-size: 10px;">Cargando...</div>
                                    </div>
                                    <div id="weatherContent" style="display: none;">
                                        <div style="text-align: center; font-size: 24px; font-weight: bold; color: white; margin-bottom: 2px;">
                                            <span id="weatherTemp">--</span><span style="font-size: 16px;">°C</span>
                                        </div>
                                        <div style="text-align: center; font-size: 10px; opacity: 0.9; color: white;">
                                            <span id="weatherDesc">--</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="weatherDetails" style="padding: 8px; display: none;">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; font-size: 10px; margin-bottom: 8px;">
                                        <div style="padding: 6px; background-color: #f8f9fa; border-radius: 4px;">
                                            <div style="color: #6c757d; margin-bottom: 2px; font-size: 9px;">
                                                <i class="fas fa-tint"></i> Humedad
                                            </div>
                                            <div style="font-weight: bold; font-size: 12px;">
                                                <span id="weatherHumidity">--</span>%
                                            </div>
                                        </div>
                                        <div style="padding: 6px; background-color: #f8f9fa; border-radius: 4px;">
                                            <div style="color: #6c757d; margin-bottom: 2px; font-size: 9px;">
                                                <i class="fas fa-wind"></i> Viento
                                            </div>
                                            <div style="font-weight: bold; font-size: 12px;">
                                                <span id="weatherWindSpeed">--</span> km/h
                                            </div>
                                        </div>
                                        <div style="padding: 6px; background-color: #f8f9fa; border-radius: 4px;">
                                            <div style="color: #6c757d; margin-bottom: 2px; font-size: 9px;">
                                                <i class="fas fa-compass"></i> Dirección
                                            </div>
                                            <div style="font-weight: bold; font-size: 12px;">
                                                <span id="weatherWindDir">--</span>
                                            </div>
                                        </div>
                                        <div style="padding: 6px; background-color: #f8f9fa; border-radius: 4px;">
                                            <div style="color: #6c757d; margin-bottom: 2px; font-size: 9px;">
                                                <i class="fas fa-cloud-rain"></i> Precip.
                                            </div>
                                            <div style="font-weight: bold; font-size: 12px;">
                                                <span id="weatherPrecip">--</span> mm
                                            </div>
                                        </div>
                                    </div>
                                    <div style="padding: 6px; background-color: #e7f3ff; border-radius: 4px; font-size: 9px; color: #0066cc; margin-bottom: 6px;">
                                        <i class="fas fa-map-marker-alt"></i> 
                                        <span id="weatherCoords">--</span>
                                    </div>
                                    <div style="text-align: center; font-size: 9px; color: #6c757d;">
                                        <i class="fas fa-info-circle"></i> OpenMeteo
                                    </div>
                                </div>
                                <div id="weatherError" style="padding: 8px; display: none; text-align: center;">
                                    <div style="color: #dc3545; margin-bottom: 6px;">
                                        <i class="fas fa-exclamation-triangle" style="font-size: 18px;"></i>
                                    </div>
                                    <div style="font-size: 10px; color: #6c757d;">
                                        No se pudieron obtener los datos.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Leyenda flotante en la esquina inferior derecha -->
                            <div class="map-legend" style="position: absolute; bottom: 10px; right: 10px; z-index: 1000; background: white; padding: 8px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); max-width: 240px; font-size: 11px;">
                                
                                <div style="margin-bottom: 4px;">
                                    <strong>{{ __('Hallazgos:') }}</strong>
                                    <div style="margin-top: 2px;">
                                        <span style="display: inline-block; width: 14px; height: 14px; background-color: #dc3545; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                        <span style="margin-left: 4px;">{{ __('Alta') }}</span>
                                        <span style="display: inline-block; width: 14px; height: 14px; background-color: #ffc107; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle; margin-left: 8px;"></span>
                                        <span style="margin-left: 4px;">{{ __('Media') }}</span>
                                        <span style="display: inline-block; width: 14px; height: 14px; background-color: #17a2b8; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle; margin-left: 8px;"></span>
                                        <span style="margin-left: 4px;">{{ __('Baja') }}</span>
                                    </div>
                                    <div style="margin-top: 4px; font-size: 10px;">
                                        <span style="display: inline-block; width: 14px; height: 14px; background-color: #dc3545; border: 3px solid #28a745; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle; position: relative;">
                                            <i class="fas fa-check" style="position: absolute; bottom: -3px; right: -3px; color: #28a745; background: white; border-radius: 50%; font-size: 8px; width: 10px; height: 10px; display: flex; align-items: center; justify-content: center;"></i>
                                        </span>
                                        <span style="margin-left: 4px;">{{ __('Con hoja de vida') }}</span>
                                    </div>
                                </div>
                                <div style="margin-bottom: 4px;">
                                    <span style="display: inline-block; width: 14px; height: 14px; background-color: #007bff; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                    <span style="margin-left: 4px;">{{ __('Animal Liberado') }}</span>
                                </div>
                                <div style="margin-bottom: 4px;">
                                    <span style="display: inline-block; width: 10px; height: 10px; background-color: #ff0000; border: 1px solid #fff; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                    <span style="margin-left: 4px;">{{ __('Foco de Calor') }}</span>
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
        let releaseMarkers = [];
        let predictionLayers = [];
        let focosCalorMarkers = [];
        let loadedPredictions = new Set(); // Para evitar cargar predicciones duplicadas
        let showReports = true;
        let showReleases = true;
        let showPredictions = true;
        let showFocosCalor = true;
        let selectedSpeciesId = null;
        let reportStatusFilter = 'all'; // 'all', 'with_file', 'without_file'
        let weatherRequestInProgress = false; // Para evitar múltiples peticiones simultáneas

        const reportsData = @json($reports ?? []);
        const releasesData = @json($releases ?? []);
        const focosCalorData = @json($focosCalorFormatted ?? []);

        function initMap() {
            if (typeof L === 'undefined') {
                console.log('[Mapa] Esperando a que Leaflet se cargue...');
                setTimeout(initMap, 100);
                return;
            }

            const mapEl = document.getElementById('mapaCampo');
            if (!mapEl) {
                console.error('[Mapa] No se encontró el elemento #mapaCampo');
                return;
            }

            // Verificar que el elemento tenga dimensiones
            if (mapEl.offsetWidth === 0 || mapEl.offsetHeight === 0) {
                console.warn('[Mapa] El contenedor del mapa no tiene dimensiones, reintentando...');
                setTimeout(initMap, 200);
                return;
            }

            console.log('[Mapa] Inicializando mapa...');
            
            // Inicializar mapa centrado en Santa Cruz
            try {
                map = L.map('mapaCampo').setView([-17.7833, -63.1821], 11);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
                    maxZoom: 19 
                }).addTo(map);
                
                console.log('[Mapa] Mapa inicializado correctamente');
            } catch (error) {
                console.error('[Mapa] Error al inicializar el mapa:', error);
                return;
            }

            // Agregar marcadores de hallazgos aprobados
            addReportsMarkers();
            
            // Agregar marcadores de animales liberados
            addReleaseMarkers();
            
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

            // Toggle de liberaciones
            const toggleReleases = document.getElementById('toggleReleases');
            if (toggleReleases) {
                toggleReleases.addEventListener('change', function() {
                    showReleases = this.checked;
                    updateReleaseMarkers();
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

            // Filtro de estado de hallazgos
            const filterReportStatus = document.getElementById('filterReportStatus');
            if (filterReportStatus) {
                filterReportStatus.addEventListener('change', function() {
                    reportStatusFilter = this.value;
                    updateReportsMarkers();
                });
            }

            // Filtro por especie (solo para liberaciones)
            const filterSpecies = document.getElementById('filterSpecies');
            if (filterSpecies) {
                filterSpecies.addEventListener('change', function() {
                    selectedSpeciesId = this.value ? parseInt(this.value) : null;
                    updateReleaseMarkers();
                });
            }

            // Listener para obtener datos meteorológicos al hacer clic en el mapa (áreas vacías)
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                console.log('[Clima] Obteniendo datos meteorológicos para:', lat, lng);
                getWeatherData(lat, lng);
            });
        }

        /**
         * Obtener datos meteorológicos para una ubicación y actualizar el panel
         */
        function getWeatherData(latitude, longitude) {
            // Evitar múltiples peticiones simultáneas
            if (weatherRequestInProgress) {
                console.log('[Clima] Ya hay una petición en curso, omitiendo...');
                return;
            }

            const weatherPanel = document.getElementById('weatherPanel');
            const weatherLoading = document.getElementById('weatherLoading');
            const weatherContent = document.getElementById('weatherContent');
            const weatherDetails = document.getElementById('weatherDetails');
            const weatherError = document.getElementById('weatherError');

            // Mostrar panel y estado de carga
            weatherPanel.style.display = 'block';
            weatherLoading.style.display = 'block';
            weatherContent.style.display = 'none';
            weatherDetails.style.display = 'none';
            weatherError.style.display = 'none';

            weatherRequestInProgress = true;

            // Realizar petición a la API
            fetch(`/api/weather?latitude=${latitude}&longitude=${longitude}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('[Clima] Datos recibidos:', data);
                    
                    // Ocultar loading y mostrar contenido
                    weatherLoading.style.display = 'none';
                    weatherContent.style.display = 'block';
                    weatherDetails.style.display = 'block';
                    weatherError.style.display = 'none';

                    // Actualizar datos en el panel
                    const weatherDescription = getWeatherDescription(data.weatherCode);
                    const windDirectionName = getWindDirectionName(data.windDirection);

                    document.getElementById('weatherTemp').textContent = data.temperature;
                    document.getElementById('weatherDesc').textContent = weatherDescription;
                    document.getElementById('weatherHumidity').textContent = data.humidity;
                    document.getElementById('weatherWindSpeed').textContent = data.windSpeed;
                    document.getElementById('weatherWindDir').textContent = windDirectionName;
                    document.getElementById('weatherPrecip').textContent = data.precipitation;
                    document.getElementById('weatherCoords').textContent = `Lat: ${latitude.toFixed(6)}, Lng: ${longitude.toFixed(6)}`;
                    
                    weatherRequestInProgress = false;
                })
                .catch(error => {
                    console.error('[Clima] Error al obtener datos meteorológicos:', error);
                    
                    // Ocultar loading y mostrar error
                    weatherLoading.style.display = 'none';
                    weatherContent.style.display = 'none';
                    weatherDetails.style.display = 'none';
                    weatherError.style.display = 'block';
                    
                    weatherRequestInProgress = false;
                });
        }

        // Botón para cerrar el panel de clima
        document.addEventListener('DOMContentLoaded', function() {
            const closeWeatherPanel = document.getElementById('closeWeatherPanel');
            if (closeWeatherPanel) {
                closeWeatherPanel.addEventListener('click', function() {
                    const weatherPanel = document.getElementById('weatherPanel');
                    if (weatherPanel) {
                        weatherPanel.style.display = 'none';
                    }
                });
            }
        });

        /**
         * Obtener descripción del código del clima
         */
        function getWeatherDescription(code) {
            const descriptions = {
                0: 'Despejado',
                1: 'Mayormente despejado',
                2: 'Parcialmente nublado',
                3: 'Nublado',
                45: 'Niebla',
                48: 'Niebla depositada',
                51: 'Llovizna ligera',
                53: 'Llovizna moderada',
                55: 'Llovizna densa',
                56: 'Llovizna helada ligera',
                57: 'Llovizna helada densa',
                61: 'Lluvia ligera',
                63: 'Lluvia moderada',
                65: 'Lluvia intensa',
                66: 'Lluvia helada ligera',
                67: 'Lluvia helada intensa',
                71: 'Nieve ligera',
                73: 'Nieve moderada',
                75: 'Nieve intensa',
                77: 'Granizo',
                80: 'Chubascos ligeros',
                81: 'Chubascos moderados',
                82: 'Chubascos intensos',
                85: 'Nevadas ligeras',
                86: 'Nevadas intensas',
                95: 'Tormenta',
                96: 'Tormenta con granizo',
                99: 'Tormenta intensa con granizo',
            };
            return descriptions[code] || 'Desconocido';
        }

        /**
         * Obtener nombre de la dirección del viento
         */
        function getWindDirectionName(degrees) {
            const directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
            const index = Math.round(degrees / 22.5) % 16;
            return directions[index] || 'N';
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
                
                // Agregar evento de clic para obtener datos meteorológicos
                point.on('click', function(e) {
                    if (e.originalEvent) {
                        e.originalEvent.stopPropagation();
                    }
                    getWeatherData(foco.lat, foco.lng);
                });
                
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
                
                if (showReleases && (!selectedSpeciesId || release.especie_id == selectedSpeciesId)) {
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

                // Agregar evento de clic para obtener datos meteorológicos
                marker.on('click', function(e) {
                    if (e.originalEvent) {
                        e.originalEvent.stopPropagation();
                    }
                    getWeatherData(lat, lng);
                });

                releaseMarkers.push({
                    release: release,
                    marker: marker
                });
            });
        }

        function updateReleaseMarkers() {
            releaseMarkers.forEach(function(item) {
                const release = item.release;
                const marker = item.marker;
                
                const shouldShow = showReleases && (!selectedSpeciesId || release.especie_id == selectedSpeciesId);
                
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


        function updateReportsMarkers() {
            markers.forEach(function(item) {
                const report = item.report;
                const marker = item.marker;
                
                // Aplicar filtro de estado
                let shouldShow = showReports;
                if (shouldShow && reportStatusFilter !== 'all') {
                    if (reportStatusFilter === 'with_file' && !report.tiene_hoja_vida) {
                        shouldShow = false;
                    } else if (reportStatusFilter === 'without_file' && report.tiene_hoja_vida) {
                        shouldShow = false;
                    }
                }
                
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

        function addReportsMarkers() {
            if (!map) {
                console.error('[Reportes] El mapa no está inicializado');
                return;
            }

            if (!reportsData || !Array.isArray(reportsData)) {
                console.error('[Reportes] No hay datos de reportes o no es un array');
                return;
            }

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

                // Si tiene hoja de vida, usar borde verde más grueso para indicar que ya fue rescatado
                const tieneHojaVida = report.tiene_hoja_vida || false;
                const borderColor = tieneHojaVida ? '#28a745' : 'white';
                const borderWidth = tieneHojaVida ? 4 : 3;
                const iconSize = tieneHojaVida ? 30 : 28;
                
                // Crear icono personalizado
                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: ${color}; width: ${iconSize}px; height: ${iconSize}px; border-radius: 50%; border: ${borderWidth}px solid ${borderColor}; box-shadow: 0 2px 6px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; position: relative;">
                        <i class="fas ${iconClass}" style="color: white; font-size: 14px;"></i>
                        ${tieneHojaVida ? '<i class="fas fa-check-circle" style="position: absolute; bottom: -2px; right: -2px; color: #28a745; background: white; border-radius: 50%; font-size: 10px; width: 14px; height: 14px; display: flex; align-items: center; justify-content: center;"></i>' : ''}
                    </div>`,
                    iconSize: [iconSize, iconSize],
                    iconAnchor: [iconSize/2, iconSize/2],
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
                        <div style="margin-bottom: 8px; padding: 8px; background-color: ${color}; border-radius: 4px; text-align: center;">
                            <div style="color: white; font-size: 24px; font-weight: bold; margin-bottom: 2px;">
                                ${urgencia !== null && urgencia !== undefined ? urgencia : 'N/A'}
                            </div>
                            <div style="color: white; font-size: 11px; text-transform: uppercase;">
                                ${urgencia >= 4 ? 'Alta Urgencia' : (urgencia === 3 ? 'Media Urgencia' : (urgencia <= 2 ? 'Baja Urgencia' : 'Sin Urgencia'))}
                            </div>
                        </div>
                        ${tieneHojaVida ? `
                            <div style="margin-bottom: 8px; padding: 6px; background-color: #28a745; border-radius: 4px; text-align: center;">
                                <i class="fas fa-check-circle" style="color: white; margin-right: 4px;"></i>
                                <span style="color: white; font-size: 12px; font-weight: bold;">Ya tiene hoja de vida</span>
                            </div>
                        ` : `
                            <div style="margin-bottom: 8px; padding: 6px; background-color: #ffc107; border-radius: 4px; text-align: center;">
                                <i class="fas fa-clock" style="color: white; margin-right: 4px;"></i>
                                <span style="color: white; font-size: 12px; font-weight: bold;">Pendiente de rescate</span>
                            </div>
                        `}
                        <h6 style="margin: 0 0 8px 0; font-weight: bold;">
                            <i class="fas fa-clipboard-list"></i> ${condicionNombre}
                        </h6>
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

                // Agregar evento de clic para obtener datos meteorológicos
                marker.on('click', function(e) {
                    if (e.originalEvent) {
                        e.originalEvent.stopPropagation();
                    }
                    getWeatherData(lat, lng);
                });

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
                ...markers.filter(m => {
                    if (!showReports) return false;
                    if (reportStatusFilter === 'all') return true;
                    if (reportStatusFilter === 'with_file' && m.report.tiene_hoja_vida) return true;
                    if (reportStatusFilter === 'without_file' && !m.report.tiene_hoja_vida) return true;
                    return false;
                }).map(m => m.marker),
                ...releaseMarkers.filter(m => showReleases && (!selectedSpeciesId || m.release.especie_id == selectedSpeciesId)).map(m => m.marker)
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

        // Inicializar cuando el DOM esté listo y Leaflet esté cargado
        function startInit() {
            if (typeof L === 'undefined') {
                console.log('[Mapa] Leaflet aún no está cargado, esperando...');
                setTimeout(startInit, 100);
                return;
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(initMap, 100);
                });
            } else {
                setTimeout(initMap, 100);
            }
        }
        
        startInit();
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
    .weather-panel {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        transition: all 0.3s ease;
    }
    .weather-panel button:hover {
        background: rgba(255,255,255,0.3) !important;
    }
    @media (max-width: 768px) {
        .weather-panel {
            max-width: 200px;
            min-width: 180px;
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

