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
                                    {{ __('Volver') }}
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
                                    @if(!empty($externalFireReportsFormatted) && count($externalFireReportsFormatted) > 0)
                                    <div class="form-check form-check-inline mb-1" id="toggleExternalFireReportsContainer">
                                        <input class="form-check-input" type="checkbox" id="toggleExternalFireReports" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="toggleExternalFireReports" style="font-size: 11px;">
                                            <i class="fas fa-fire-alt"></i> {{ __('Reportes Externos') }}
                                        </label>
                                    </div>
                                    @endif
                                </div>
                                
                                @if(!empty($externalFireReportsFormatted) && count($externalFireReportsFormatted) > 0)
                                <!-- Botón para cargar predicciones -->
                                <div class="mt-2" id="predictionsButtonContainer">
                                    <button type="button" id="btnLoadPredictions" class="btn btn-warning btn-sm btn-block" style="font-size: 11px;">
                                        <i class="fas fa-fire"></i> {{ __('Ver Predicciones') }}
                                    </button>
                                    <div id="predictionsLoading" style="display: none; text-align: center; margin-top: 5px; font-size: 10px; color: #6c757d;">
                                        <i class="fas fa-spinner fa-spin"></i> Cargando predicciones...
                                    </div>
                                </div>
                                
                                <!-- Toggle para mostrar/ocultar predicciones cargadas -->
                                <div class="mt-2" id="togglePredictionsContainer" style="display: none;">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="togglePredictions" checked style="margin-top: 0.25rem;">
                                        <label class="form-check-label" for="togglePredictions" style="font-size: 11px;">
                                            <i class="fas fa-eye"></i> {{ __('Mostrar Predicciones') }}
                                        </label>
                                    </div>
                                </div>
                                @endif
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
                                    <div style="padding: 6px; background-color: #e7f3ff; border-radius: 4px; font-size: 9px; color: #0066cc; margin-bottom: 6px;" hidden>
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
                                <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #dee2e6;">
                                    <strong>{{ __('Reportes Externos') }}:</strong>
                                    <div style="margin-top: 4px;">
                                        <span style="display: inline-block; width: 12px; height: 12px; background-color: #dc3545; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                        <span style="margin-left: 4px; font-size: 10px;">{{ __('Fuera de control') }}</span>
                                    </div>
                                    <div style="margin-top: 2px;">
                                        <span style="display: inline-block; width: 12px; height: 12px; background-color: #ff8800; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                        <span style="margin-left: 4px; font-size: 10px;">{{ __('Activo') }}</span>
                                    </div>
                                    <div style="margin-top: 2px;">
                                        <span style="display: inline-block; width: 12px; height: 12px; background-color: #ffc107; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                        <span style="margin-left: 4px; font-size: 10px;">{{ __('Contenido') }}</span>
                                    </div>
                                    <div style="margin-top: 2px;">
                                        <span style="display: inline-block; width: 12px; height: 12px; background-color: #28a745; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3); vertical-align: middle;"></span>
                                        <span style="margin-left: 4px; font-size: 10px;">{{ __('Controlado') }}</span>
                                    </div>
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
        let externalFireReportsMarkers = [];
        let loadedPredictions = new Set(); // Para evitar cargar predicciones duplicadas
        let showReports = true;
        let showReleases = true;
        let showPredictions = true;
        let showFocosCalor = true;
        let showExternalFireReports = true;
        let selectedSpeciesId = null;
        let reportStatusFilter = 'all'; // 'all', 'with_file', 'without_file'
        let weatherRequestInProgress = false; // Para evitar múltiples peticiones simultáneas

        const reportsData = @json($reports ?? []);
        const releasesData = @json($releases ?? []);
        const focosCalorData = @json($focosCalorFormatted ?? []);
        const externalFireReportsData = @json($externalFireReportsFormatted ?? []);

        // Área de exclusión de predicciones (ciudad)
        // Rectángulo definido por dos puntos diagonales
        const cityExclusionArea = {
            latMin: -17.83014885201047,  // Sur (latitud mínima)
            latMax: -17.722261562205507, // Norte (latitud máxima)
            lngMin: -63.21929667333384,  // Oeste (longitud mínima)
            lngMax: -63.114926559251174  // Este (longitud máxima)
        };

        /**
         * Verificar si un punto está dentro del área de exclusión de la ciudad
         * @param {number} lat - Latitud del punto
         * @param {number} lng - Longitud del punto
         * @returns {boolean} - true si está dentro del área de exclusión
         */
        function isInsideCityExclusionArea(lat, lng) {
            return lat >= cityExclusionArea.latMin && 
                   lat <= cityExclusionArea.latMax && 
                   lng >= cityExclusionArea.lngMin && 
                   lng <= cityExclusionArea.lngMax;
        }

        // Ocultar controles de reportes externos y predicciones si no hay datos
        (function() {
            if (!externalFireReportsData || externalFireReportsData.length === 0) {
                const toggleContainer = document.getElementById('toggleExternalFireReportsContainer');
                const predictionsButtonContainer = document.getElementById('predictionsButtonContainer');
                
                if (toggleContainer) {
                    toggleContainer.style.display = 'none';
                }
                if (predictionsButtonContainer) {
                    predictionsButtonContainer.style.display = 'none';
                }
                
                console.log('[Mapa] No hay reportes externos disponibles, ocultando controles relacionados');
            } else {
                console.log(`[Mapa] ${externalFireReportsData.length} reportes externos disponibles`);
            }
        })();

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
            
            // Agregar marcadores de focos de calor (intenta primero API de integración, luego FIRMS desde BD)
            addFocosCalorMarkers();
            
            // Agregar marcadores de reportes externos de incendios
            addExternalFireReportsMarkers();

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
            
            // Handler del botón "Ver Predicciones"
            const btnLoadPredictions = document.getElementById('btnLoadPredictions');
            if (btnLoadPredictions) {
                btnLoadPredictions.addEventListener('click', function() {
                    console.log('[Predicciones] Botón presionado, iniciando carga de predicciones');
                    loadAllPredictions();
                });
            } else {
                console.warn('[Predicciones] Botón btnLoadPredictions no encontrado');
            }

            // Toggle de focos de calor (NASA FIRMS)
            const toggleFocosCalor = document.getElementById('toggleFocosCalor');
            if (toggleFocosCalor) {
                toggleFocosCalor.addEventListener('change', function() {
                    showFocosCalor = this.checked;
                    updateFocosCalorMarkers();
                });
            }

            // Toggle de reportes externos de incendios
            const toggleExternalFireReports = document.getElementById('toggleExternalFireReports');
            if (toggleExternalFireReports) {
                toggleExternalFireReports.addEventListener('change', function() {
                    showExternalFireReports = this.checked;
                    updateExternalFireReportsMarkers();
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

            // Ajustar vista para mostrar todos los marcadores visibles después de agregar todos
            setTimeout(function() {
                const allVisibleMarkers = [
                    ...markers.filter(m => {
                        if (!showReports) return false;
                        if (reportStatusFilter === 'all') return true;
                        if (reportStatusFilter === 'with_file' && m.report.tiene_hoja_vida) return true;
                        if (reportStatusFilter === 'without_file' && !m.report.tiene_hoja_vida) return true;
                        return false;
                    }).map(m => m.marker),
                    ...releaseMarkers.filter(m => showReleases && (!selectedSpeciesId || m.release.especie_id == selectedSpeciesId)).map(m => m.marker),
                    ...focosCalorMarkers.filter(() => showFocosCalor)
                ];
                
                if (allVisibleMarkers.length > 0) {
                    const group = new L.featureGroup(allVisibleMarkers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }, 500);
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

        function addExternalFireReportsMarkers() {
            if (!map) return;
            
            if (!externalFireReportsData || externalFireReportsData.length === 0) {
                console.log('[Reportes Externos] No hay datos de reportes externos para mostrar');
                return;
            }
            
            console.log(`[Reportes Externos] Agregando ${externalFireReportsData.length} reportes externos al mapa`);
            
            externalFireReportsData.forEach(function(report) {
                if (!report.lat || !report.lng) return;
                
                const hasLocalReports = report.has_local_reports || false;
                const localReportsCount = report.local_reports_count || 0;
                const isSimulated = report.simulado === true;
                
                // Si tiene hallazgos locales, usar ícono de animal con efecto de titileo
                // Si no, usar ícono de fuego normal
                const iconClass = hasLocalReports ? 'fa-paw' : 'fa-fire';
                const iconColor = report.color || '#6c757d';
                const blinkClass = hasLocalReports ? 'external-fire-report-blink' : '';
                
                const iconHtml = `
                    <div class="external-fire-report-icon ${blinkClass}" style="
                        background-color: ${iconColor};
                        width: 28px;
                        height: 28px;
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.4);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        position: relative;
                    ">
                        <i class="fas ${iconClass}" style="color: white; font-size: 14px;"></i>
                        ${hasLocalReports ? `
                        <div style="
                            position: absolute;
                            top: -4px;
                            right: -4px;
                            background-color: #dc3545;
                            color: white;
                            border-radius: 50%;
                            width: 18px;
                            height: 18px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 10px;
                            font-weight: bold;
                            border: 2px solid white;
                        ">${localReportsCount}</div>
                        ` : ''}
                        ${isSimulated ? `
                        <div style="
                            position: absolute;
                            bottom: -2px;
                            left: -2px;
                            background-color: #007bff;
                            color: white;
                            border-radius: 50%;
                            width: 14px;
                            height: 14px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 8px;
                            border: 2px solid white;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
                        " title="Punto Simulado">
                            <i class="fas fa-flask" style="font-size: 8px;"></i>
                        </div>
                        ` : ''}
                    </div>
                `;
                
                const icon = L.divIcon({
                    className: 'custom-external-fire-report-marker',
                    html: iconHtml,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14],
                });
                
                const marker = L.marker([report.lat, report.lng], { icon: icon });
                
                if (showExternalFireReports) {
                    marker.addTo(map);
                }
                
                const nivelGravedad = report.nivel_gravedad || 'Desconocido';
                const fechaHora = report.fecha_hora ? new Date(report.fecha_hora).toLocaleString('es-BO') : 'N/A';
                const creado = report.creado ? new Date(report.creado).toLocaleString('es-BO') : 'N/A';
                
                const popupContent = `
                    <div style="min-width: 250px;">
                        <h6 style="margin: 0 0 8px 0; font-weight: bold; color: ${report.color || '#6c757d'};">
                            <i class="fas ${iconClass}"></i> {{ __('Reporte de Incendio Externo') }}
                            ${hasLocalReports ? '<span style="color: #dc3545; margin-left: 8px;"><i class="fas fa-exclamation-triangle"></i> {{ __('Animales en Peligro') }}</span>' : ''}
                        </h6>
                        ${isSimulated ? `
                        <div style="font-size: 11px; margin-bottom: 8px; padding: 6px; background-color: #e7f3ff; border-left: 3px solid #007bff; border-radius: 4px;">
                            <strong><i class="fas fa-flask"></i> {{ __('Punto Simulado') }}</strong>
                            
                        </div>
                        ` : ''}
                        ${hasLocalReports ? `
                        <div style="font-size: 11px; margin-bottom: 8px; padding: 6px; background-color: #fff3cd; border-left: 3px solid #dc3545; border-radius: 4px;">
                            <strong><i class="fas fa-paw"></i> ${localReportsCount} ${localReportsCount === 1 ? '{{ __('hallazgo local') }}' : '{{ __('hallazgos locales') }}'} relacionado${localReportsCount === 1 ? '' : 's'}</strong>
                        </div>
                        ` : ''}
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>{{ __('Nivel de Gravedad') }}:</strong> 
                            <span style="color: ${report.color || '#6c757d'}; font-weight: bold;">
                                ${nivelGravedad}
                            </span>
                        </div>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>{{ __('Reportante') }}:</strong> ${report.nombre_reportante || 'N/A'}
                        </div>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>{{ __('Teléfono') }}:</strong> ${report.telefono_contacto || 'N/A'}
                        </div>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>{{ __('Ubicación') }}:</strong> ${report.nombre_lugar || 'N/A'}
                        </div>
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            <strong>{{ __('Fecha/Hora') }}:</strong> ${fechaHora}
                        </div>
                        ${report.comentario_adicional ? `
                        <div style="font-size: 11px; margin-top: 8px; padding: 6px; background-color: #f8f9fa; border-radius: 4px;">
                            <strong>{{ __('Comentario') }}:</strong><br>
                            ${report.comentario_adicional}
                        </div>
                        ` : ''}
                        <div style="font-size: 10px; color: #6c757d; margin-top: 8px;">
                            <i class="fas fa-info-circle"></i> {{ __('Creado') }}: ${creado}
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent);
                
                marker.on('click', function(e) {
                    if (e.originalEvent) {
                        e.originalEvent.stopPropagation();
                    }
                    getWeatherData(report.lat, report.lng);
                });
                
                externalFireReportsMarkers.push(marker);
            });
        }

        function updateExternalFireReportsMarkers() {
            externalFireReportsMarkers.forEach(function(marker) {
                if (showExternalFireReports) {
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

            // Las predicciones se cargan solo cuando se presiona el botón "Ver Predicciones"
        }

        function loadAllPredictions() {
            const btn = document.getElementById('btnLoadPredictions');
            const loadingDiv = document.getElementById('predictionsLoading');
            const toggleContainer = document.getElementById('togglePredictionsContainer');
            
            if (!btn || !loadingDiv) {
                console.error('[Predicciones] Botón o loading div no encontrado');
                return;
            }
            
            console.log('[Predicciones] ===== INICIANDO CARGA DE PREDICCIONES =====');
            console.log('[Predicciones] Total de reportes externos disponibles:', externalFireReportsData ? externalFireReportsData.length : 0);
            console.log('[Predicciones] Datos de reportes externos:', externalFireReportsData);
            
            // Obtener todos los reportes externos de incendios con latitud y longitud
            // Excluir los que están dentro del área de la ciudad
            const externalReportsWithLocation = externalFireReportsData.filter(function(report) {
                const hasLocation = report.lat != null && report.lng != null;
                if (!hasLocation) {
                    console.warn('[Predicciones] Reporte sin ubicación válida:', report);
                    return false;
                }
                
                // Verificar si está dentro del área de exclusión de la ciudad
                if (isInsideCityExclusionArea(report.lat, report.lng)) {
                    console.log(`[Predicciones] Reporte en lat=${report.lat}, lng=${report.lng} está dentro del área de la ciudad, excluyendo de predicciones`);
                    return false;
                }
                
                return true;
            });
            
            console.log(`[Predicciones] Reportes con ubicación válida: ${externalReportsWithLocation.length}`);
            console.log('[Predicciones] Reportes con ubicación:', externalReportsWithLocation);
            
            if (externalReportsWithLocation.length === 0) {
                alert('No hay reportes externos de incendios con ubicación para cargar predicciones');
                console.warn('[Predicciones] No hay reportes con ubicación para procesar');
                return;
            }
            
            console.log(`[Predicciones] Cargando predicciones para ${externalReportsWithLocation.length} reportes externos de incendios`);
            
            // Deshabilitar botón y mostrar loading
            btn.disabled = true;
            loadingDiv.style.display = 'block';
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
            
            let completed = 0;
            const total = externalReportsWithLocation.length;
            
            // Cargar predicciones una por una
            externalReportsWithLocation.forEach(function(report, index) {
                const key = `${report.lat}_${report.lng}`;
                
                console.log(`[Predicciones] Procesando reporte ${index + 1}/${total}: lat=${report.lat}, lng=${report.lng}`);
                
                // Evitar cargar la misma predicción dos veces
                if (loadedPredictions.has(key)) {
                    console.log(`[Predicciones] Predicción para ${report.lat}, ${report.lng} ya cargada, omitiendo`);
                    completed++;
                    if (completed === total) {
                        finishLoadingPredictions(btn, loadingDiv, toggleContainer);
                    }
                    return;
                }
                
                // Pequeño delay entre peticiones para no sobrecargar el servidor
                setTimeout(function() {
                    loadFirePrediction(report.lat, report.lng, function() {
                        completed++;
                        console.log(`[Predicciones] Completado ${completed}/${total}`);
                        if (completed === total) {
                            finishLoadingPredictions(btn, loadingDiv, toggleContainer);
                        }
                    });
                }, index * 200); // 200ms de delay entre cada petición
            });
        }

        function finishLoadingPredictions(btn, loadingDiv, toggleContainer) {
            btn.disabled = false;
            loadingDiv.style.display = 'none';
            btn.innerHTML = '<i class="fas fa-fire"></i> {{ __('Ver Predicciones') }}';
            
            if (predictionLayers.length > 0 && toggleContainer) {
                toggleContainer.style.display = 'block';
            }
        }

        function loadFirePrediction(lat, lng, callback) {
            // Verificar si está dentro del área de exclusión de la ciudad
            if (isInsideCityExclusionArea(lat, lng)) {
                console.log(`[Predicciones] Punto en lat=${lat}, lng=${lng} está dentro del área de la ciudad, no se cargará predicción`);
                if (callback) callback();
                return;
            }
            
            const key = `${lat}_${lng}`;
            
            // Evitar cargar la misma predicción dos veces
            if (loadedPredictions.has(key)) {
                console.log(`[Predicciones] Predicción para ${lat}, ${lng} ya cargada, omitiendo`);
                if (callback) callback();
                return;
            }
            
            loadedPredictions.add(key);
            console.log(`[Predicciones] Solicitando predicción para lat: ${lat}, lng: ${lng}`);
            
            // Usar el endpoint de lookup con GET (según la documentación del endpoint)
            const apiUrl = '{{ config("services.fire_predictions_lookup.api_url") }}';
            const url = `${apiUrl}?lat=${parseFloat(lat)}&lng=${parseFloat(lng)}&hours=6`;
            
            console.log(`[Predicciones] URL de petición: ${url}`);
            
            // Enviar por GET con query parameters
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    console.log(`[Predicciones] Respuesta HTTP recibida, status: ${response.status}`);
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error(`[Predicciones] Error HTTP ${response.status}:`, text);
                            throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(`[Predicciones] Respuesta JSON recibida para ${lat}, ${lng}:`, data);
                    
                    if (data && data.success && data.data && data.data.path && Array.isArray(data.data.path)) {
                        console.log(`[Predicciones] Predicción válida recibida para ${lat}, ${lng}`);
                        console.log(`[Predicciones] Path points: ${data.data.path.length}`);
                        console.log(`[Predicciones] Primer punto del path:`, data.data.path[0]);
                        drawFirePrediction(data.data, lat, lng);
                    } else {
                        console.warn(`[Predicciones] Respuesta inválida o sin path para ${lat}, ${lng}`, data);
                        if (data && data.data) {
                            console.warn(`[Predicciones] Estructura de data:`, Object.keys(data.data));
                        }
                        loadedPredictions.delete(key);
                    }
                    if (callback) callback();
                })
                .catch(error => {
                    console.error(`[Predicciones] Error al cargar predicción para ${lat}, ${lng}:`, error);
                    console.error(`[Predicciones] Stack trace:`, error.stack);
                    loadedPredictions.delete(key);
                    if (callback) callback();
                });
        }

        function drawFirePrediction(predictionData, originalLat, originalLng) {
            console.log('[Predicciones] ===== INICIANDO DIBUJO DE PREDICCIÓN =====');
            console.log('[Predicciones] originalLat:', originalLat, 'originalLng:', originalLng);
            console.log('[Predicciones] predictionData:', predictionData);
            
            if (!map) {
                console.error('[Predicciones] El mapa no está inicializado');
                return;
            }
            
            if (!predictionData || !predictionData.path || !Array.isArray(predictionData.path)) {
                console.warn('[Predicciones] Datos de predicción inválidos:', predictionData);
                return;
            }

            const path = predictionData.path;
            console.log(`[Predicciones] Dibujando predicción con ${path.length} puntos de path`);
            console.log('[Predicciones] Path data completo:', JSON.stringify(path, null, 2));
            
            if (path.length === 0) {
                console.warn('[Predicciones] El path está vacío, no hay nada que dibujar');
                return;
            }
            
            const circles = [];
            let polyline = null;
            
            // Usar el ID de la predicción o crear una clave única
            const predictionId = predictionData.id || `${originalLat}_${originalLng}`;
            console.log(`[Predicciones] ID de predicción: ${predictionId}`);

            // Primero crear la línea punteada (para que quede debajo de los círculos)
            if (path.length > 1) {
                const latlngs = path
                    .filter(p => p.lat != null && p.lng != null)
                    .map(p => [parseFloat(p.lat), parseFloat(p.lng)]);

                console.log(`[Predicciones] Puntos válidos para línea: ${latlngs.length} de ${path.length}`);

                if (latlngs.length > 1) {
                    polyline = L.polyline(latlngs, {
                        color: '#ff0000',
                        weight: 4,
                        opacity: 0.8,
                        dashArray: '10, 5',
                        zIndexOffset: -100, // Asegurar que esté debajo
                        predictionId: predictionId // Guardar ID para evitar duplicados
                    });
                    
                    if (showPredictions) {
                        polyline.addTo(map);
                        console.log('[Predicciones] Línea de propagación agregada al mapa');
                    }
                    predictionLayers.push(polyline);
                } else {
                    console.warn('[Predicciones] No hay suficientes puntos válidos para dibujar la línea');
                }
            }

            // Luego crear los círculos (quedarán encima de la línea)
            path.forEach(function(point, index) {
                if (point.lat == null || point.lng == null) {
                    console.warn(`[Predicciones] Punto ${index} sin coordenadas válidas:`, point);
                    return;
                }

                // El nuevo formato usa perimeter_radius_m (en metros)
                const radius = parseFloat(point.perimeter_radius_m) || 0; // ya está en metros
                const intensity = parseFloat(point.intensity) || 0;
                
                console.log(`[Predicciones] Punto ${index}: lat=${point.lat}, lng=${point.lng}, radius=${radius}m, intensity=${intensity}`);

                // Calcular horas desde el inicio
                let hoursFromStart = 0;
                if (point.t && path[0] && path[0].t) {
                    const startTime = new Date(path[0].t);
                    const pointTime = new Date(point.t);
                    hoursFromStart = (pointTime - startTime) / (1000 * 60 * 60); // convertir a horas
                }

                // Color según intensidad (0-1) - más visibles
                let color = '#ff8800';
                let borderColor = '#ff6600';
                let opacity = 0.4;
                let borderWidth = 3;
                
                if (intensity >= 0.7) {
                    color = '#ff0000';
                    borderColor = '#cc0000';
                    opacity = 0.6;
                    borderWidth = 4;
                } else if (intensity >= 0.5) {
                    color = '#ff4400';
                    borderColor = '#ff2200';
                    opacity = 0.5;
                    borderWidth = 3;
                } else if (intensity >= 0.3) {
                    color = '#ff8800';
                    borderColor = '#ff6600';
                    opacity = 0.4;
                    borderWidth = 2;
                }

                // Crear círculo de propagación
                // Si el radio es muy pequeño, usar un mínimo para que sea visible
                const minRadius = 50; // mínimo 50 metros para que sea visible
                const finalRadius = Math.max(radius, minRadius);
                
                const circle = L.circle([parseFloat(point.lat), parseFloat(point.lng)], {
                    radius: finalRadius,
                    color: borderColor,
                    fillColor: color,
                    fillOpacity: opacity,
                    weight: borderWidth,
                    zIndexOffset: 100, // Asegurar que esté encima de la línea
                    predictionId: predictionId // Guardar ID para evitar duplicados
                });

                if (showPredictions) {
                    circle.addTo(map);
                    console.log(`[Predicciones] Círculo ${index} agregado: radio=${finalRadius}m, intensidad=${intensity}`);
                }

                circles.push(circle);
                
                // Agregar marcador en el centro del círculo para mejor visibilidad
                if (index === 0 || intensity >= 0.7) {
                    const marker = L.marker([parseFloat(point.lat), parseFloat(point.lng)], {
                        icon: L.divIcon({
                            className: 'prediction-marker',
                            html: `<div style="
                                width: 12px;
                                height: 12px;
                                background-color: ${color};
                                border: 2px solid ${borderColor};
                                border-radius: 50%;
                                box-shadow: 0 0 8px ${color};
                            "></div>`,
                            iconSize: [12, 12],
                            iconAnchor: [6, 6]
                        }),
                        predictionId: predictionId
                    });
                    
                    if (showPredictions) {
                        marker.addTo(map);
                    }
                    predictionLayers.push(marker);
                }

                // Agregar popup al círculo
                const radiusKm = (radius / 1000).toFixed(2);
                const areaKm2 = (Math.PI * Math.pow(radius / 1000, 2)).toFixed(2);
                const perimeterKm = (2 * Math.PI * radius / 1000).toFixed(2);
                
                const popupContent = `
                    <div style="min-width: 180px;">
                        <h6 style="margin: 0 0 8px 0;">
                            <i class="fas fa-fire"></i> Predicción - +${hoursFromStart.toFixed(1)}h
                        </h6>
                        <div style="font-size: 12px;">
                            <div><strong>Intensidad:</strong> ${intensity.toFixed(2)}</div>
                            <div><strong>Radio:</strong> ${radiusKm} km</div>
                            <div><strong>Área afectada:</strong> ${areaKm2} km²</div>
                            <div><strong>Perímetro:</strong> ${perimeterKm} km</div>
                            ${point.t ? `<div style="font-size: 10px; color: #6c757d; margin-top: 4px;">${new Date(point.t).toLocaleString()}</div>` : ''}
                        </div>
                    </div>
                `;
                circle.bindPopup(popupContent);
            });

            predictionLayers.push(...circles);
            
            console.log(`[Predicciones] ===== PREDICCIÓN DIBUJADA COMPLETAMENTE =====`);
            console.log(`[Predicciones] Total de capas agregadas: ${predictionLayers.length}`);
            console.log(`[Predicciones] - Línea: ${polyline ? 'Sí' : 'No'}`);
            console.log(`[Predicciones] - Círculos: ${circles.length}`);
            console.log(`[Predicciones] showPredictions está: ${showPredictions}`);
        }

        function updatePredictionLayers() {
            // Solo actualizar capas existentes (mostrar/ocultar)
            // No cargar nuevas predicciones automáticamente
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
    
    /* Efecto de titileo para reportes externos con animales en peligro */
    .external-fire-report-blink {
        animation: externalFireReportBlink 1.5s ease-in-out infinite;
    }
    
    @keyframes externalFireReportBlink {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }
        50% {
            opacity: 0.7;
            transform: scale(1.15);
            box-shadow: 0 4px 16px rgba(220, 53, 69, 0.6);
        }
    }
    
    /* Asegurar que el marcador personalizado no tenga fondo por defecto */
    .custom-external-fire-report-marker {
        background: transparent !important;
        border: none !important;
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

