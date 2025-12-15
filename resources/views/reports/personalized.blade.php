@php
    $availableColumns = $availableColumns ?? [];
    // Filtrar columnas no permitidas de selectedColumns
    $allowedColumns = ['animal_name', 'species', 'status', 'center', 'rescue_date', 'rescuer', 'veterinarian', 'release_date', 'condition', 'incident_type'];
    $defaultColumns = ['animal_name', 'species', 'status', 'center', 'rescue_date'];
    $selectedColumns = $selectedColumns ?? $defaultColumns;
    // Asegurar que selectedColumns solo contenga columnas permitidas
    $selectedColumns = array_filter($selectedColumns, function($col) use ($allowedColumns) {
        return in_array($col, $allowedColumns);
    });
    if (empty($selectedColumns)) {
        $selectedColumns = $defaultColumns;
    }
    $reportData = $reportData ?? [];
    $centers = $centers ?? collect();
    $rescuers = $rescuers ?? collect();
    $veterinarians = $veterinarians ?? collect();
    $animalName = $animalName ?? '';
    $centerId = $centerId ?? '';
    $dateFrom = $dateFrom ?? '';
    $dateTo = $dateTo ?? '';
    $groupBy = $groupBy ?? '';
    $isGrouped = $isGrouped ?? false;
    $groupedData = $groupedData ?? [];
@endphp


<div class="card mt-3">
    <div class="card-header bg-info text-white">
        <h3 class="card-title mb-0">
            <i class="fas fa-sliders-h mr-2"></i>Generador de Reportes Personalizados
        </h3>
    </div>
    <div class="card-body">
        <!-- Formulario de Configuración -->
        <form method="GET" action="{{ route('reportes.index') }}" id="personalizedReportForm">
            <input type="hidden" name="tab" value="personalized">
            
            <!-- Selección de Columnas - Colapsable -->
            <div class="row mb-3">
                <div class="col-12">
                    <h5 class="mb-3">
                        <a class="text-decoration-none" data-toggle="collapse" href="#columnsCollapse" role="button" aria-expanded="false" aria-controls="columnsCollapse">
                            <i class="fas fa-columns mr-2"></i>Selecciona las Columnas a Mostrar
                            <i class="fas fa-chevron-down ml-2" id="columnsIcon"></i>
                        </a>
                    </h5>
                </div>
            </div>
            
            <div class="collapse" id="columnsCollapse">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn" onclick="return selectAllColumns();">
                                <i class="fas fa-check-square mr-1"></i>Seleccionar Todas
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn" onclick="return deselectAllColumns();">
                                <i class="fas fa-square mr-1"></i>Deseleccionar Todas
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="row">
                            @foreach($availableColumns as $key => $label)
                                <div class="col-md-3 col-sm-4 col-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input column-checkbox" 
                                               type="checkbox" 
                                               name="columns[]" 
                                               value="{{ $key }}" 
                                               id="col_{{ $key }}"
                                               {{ in_array($key, $selectedColumns) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="col_{{ $key }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fin del Collapse de Columnas -->

            <hr>

            <!-- Filtros - Colapsable -->
            <div class="row mb-3">
                <div class="col-12">
                    <h5 class="mb-3">
                        <a class="text-decoration-none" data-toggle="collapse" href="#filtersCollapse" role="button" aria-expanded="false" aria-controls="filtersCollapse">
                            <i class="fas fa-filter mr-2"></i>Filtros
                            <i class="fas fa-chevron-down ml-2" id="filterIcon"></i>
                        </a>
                    </h5>
                </div>
            </div>
            
            <div class="collapse" id="filtersCollapse">

            <div class="row">
                <!-- Búsqueda por Nombre de Animal -->
                <div class="col-md-4 mb-3">
                    <label for="animal_name" class="form-label">
                        <i class="fas fa-paw mr-1"></i>Nombre del Animal
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="animal_name" 
                           name="animal_name" 
                           value="{{ $animalName ?? '' }}" 
                           placeholder="Buscar por nombre...">
                </div>

                <!-- Filtro por Centro -->
                <div class="col-md-4 mb-3">
                    <label for="center_id" class="form-label">
                        <i class="fas fa-clinic-medical mr-1"></i>Centro
                    </label>
                    <select class="form-control" id="center_id" name="center_id">
                        <option value="">Todos los Centros</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ ($centerId ?? '') == $center->id ? 'selected' : '' }}>
                                {{ $center->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Agrupación -->
                <div class="col-md-4 mb-3">
                    <label for="group_by" class="form-label">
                        <i class="fas fa-layer-group mr-1"></i>Agrupar Por
                    </label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option value="">Sin Agrupación</option>
                        <option value="veterinarian" {{ ($groupBy ?? '') == 'veterinarian' ? 'selected' : '' }}>Veterinario</option>
                        <option value="rescuer" {{ ($groupBy ?? '') == 'rescuer' ? 'selected' : '' }}>Rescatista</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <!-- Filtro por Fecha Desde -->
                <div class="col-md-4 mb-3">
                    <label for="date_from" class="form-label">
                        <i class="fas fa-calendar-alt mr-1"></i>Fecha Desde
                    </label>
                    <input type="date" 
                           class="form-control" 
                           id="date_from" 
                           name="date_from" 
                           value="{{ $dateFrom ?? '' }}">
                </div>

                <!-- Filtro por Fecha Hasta -->
                <div class="col-md-4 mb-3">
                    <label for="date_to" class="form-label">
                        <i class="fas fa-calendar-alt mr-1"></i>Fecha Hasta
                    </label>
                    <input type="date" 
                           class="form-control" 
                           id="date_to" 
                           name="date_to" 
                           value="{{ $dateTo ?? '' }}">
                </div>
            </div>
            </div>
            <!-- Fin del Collapse de Filtros -->

            <!-- Botones de Acción -->
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-2"></i>Generar Reporte
                    </button>
                    <a href="{{ route('reportes.index', ['tab' => 'personalized']) }}" class="btn btn-secondary">
                        <i class="fas fa-redo mr-2"></i>Limpiar Filtros
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Resultados -->
@if(isset($isGrouped) && $isGrouped && isset($groupedData) && count($groupedData) > 0)
    <!-- Vista Agrupada -->
    @foreach($groupedData as $group)
        <div class="card mt-3">
            <div class="card-header {{ $group['group_type'] === 'veterinarian' ? 'bg-success' : 'bg-warning' }} text-white">
                <h5 class="mb-0">
                    <i class="fas {{ $group['group_type'] === 'veterinarian' ? 'fa-user-md' : 'fa-user-injured' }} mr-2"></i>
                    {{ $group['person_name'] }}
                    <span class="badge badge-light ml-2">
                        @if($group['group_type'] === 'veterinarian')
                            {{ $group['total_evaluations'] }} Evaluaciones
                        @else
                            {{ $group['total_transfers'] }} Traslados
                        @endif
                    </span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped mb-0">
                        <thead class="bg-secondary text-white">
                            <tr>
                                @if($group['group_type'] === 'veterinarian')
                                    <th>Animal</th>
                                    <th>Diagnóstico</th>
                                    <th>Fecha Evaluación</th>
                                    <th>Tipo de Tratamiento</th>
                                    <th>Especie</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                @else
                                    <th>Animal</th>
                                    <th>Centro Destino</th>
                                    <th>Fecha Traslado</th>
                                    <th>Ubicación Rescate</th>
                                    <th>Fecha Rescate</th>
                                    <th>Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if($group['group_type'] === 'veterinarian')
                                @foreach($group['evaluations'] as $eval)
                                    <tr>
                                        <td>{{ $eval['animal_name'] }}</td>
                                        <td>{{ $eval['diagnostico'] }}</td>
                                        <td>{{ $eval['fecha'] }}</td>
                                        <td>{{ $eval['treatment_type'] }}</td>
                                        <td>{{ $eval['species'] }}</td>
                                        <td>{{ $eval['status'] }}</td>
                                        <td>
                                            @if($eval['animal_id'])
                                                <a href="{{ route('animal-histories.index', ['animal_file_id' => $eval['animal_id']]) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Ver Historial">
                                                    <i class="fas fa-history"></i> Historial
                                                </a>
                                                <a href="{{ route('animal-files.show', $eval['animal_id']) }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="Ver Detalles">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach($group['transfers'] as $transfer)
                                    <tr>
                                        <td>{{ $transfer['animal_name'] }}</td>
                                        <td>{{ $transfer['center_name'] }}</td>
                                        <td>{{ $transfer['fecha_traslado'] }}</td>
                                        <td>{{ $transfer['rescue_location'] }}</td>
                                        <td>{{ $transfer['rescue_date'] }}</td>
                                        <td>
                                            @if($transfer['report_id'])
                                                <a href="{{ route('reports.show', $transfer['report_id']) }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="Ver Reporte">
                                                    <i class="fas fa-eye"></i> Ver Reporte
                                                </a>
                                            @endif
                                            @if(isset($transfer['animal_file_id']) && $transfer['animal_file_id'])
                                                <a href="{{ route('animal-histories.index', ['animal_file_id' => $transfer['animal_file_id']]) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Ver Historial">
                                                    <i class="fas fa-history"></i> Historial
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
@elseif(isset($reportData) && count($reportData) > 0)
    <!-- Vista Normal -->
    <div class="card mt-3">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-table mr-2"></i>Resultados ({{ count($reportData) }} registros)
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            @foreach($selectedColumns as $col)
                                <th>{{ $availableColumns[$col] ?? $col }}</th>
                            @endforeach
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $row)
                            <tr>
                                @foreach($selectedColumns as $col)
                                    <td>{{ $row[$col] ?? 'N/A' }}</td>
                                @endforeach
                                <td>
                                    @if(isset($row['_animal_file_id']))
                                        <a href="{{ route('animal-histories.index', ['animal_file_id' => $row['_animal_file_id']]) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver Historial">
                                            <i class="fas fa-history"></i> Historial
                                        </a>
                                        <a href="{{ route('animal-files.show', $row['_animal_file_id']) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="Ver Detalles">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="card mt-3">
        <div class="card-body text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <p class="text-muted">No se encontraron resultados. Ajusta los filtros e intenta nuevamente.</p>
        </div>
    </div>
@endif

@push('scripts')
<script>

    // Funciones para seleccionar/deseleccionar todas las columnas
    window.selectAllColumns = function() {
        // Buscar todos los checkboxes de columnas usando múltiples métodos
        let checkboxes = [];
        
        // Método 1: Por clase
        checkboxes = Array.from(document.querySelectorAll('.column-checkbox'));
        
        // Método 2: Por nombre si el primero no encuentra nada
        if (checkboxes.length === 0) {
            checkboxes = Array.from(document.querySelectorAll('input[name="columns[]"]'));
        }
        
        // Método 3: Por tipo y clase form-check-input
        if (checkboxes.length === 0) {
            checkboxes = Array.from(document.querySelectorAll('input.form-check-input[type="checkbox"]'));
        }
        
        // Método 4: Todos los checkboxes dentro del formulario
        if (checkboxes.length === 0) {
            const form = document.getElementById('personalizedReportForm');
            if (form) {
                checkboxes = Array.from(form.querySelectorAll('input[type="checkbox"][name="columns[]"]'));
            }
        }
        
        // Aplicar la selección
        checkboxes.forEach(function(checkbox) {
            if (checkbox && checkbox.type === 'checkbox') {
                checkbox.checked = true;
            }
        });
        
        console.log('Seleccionadas ' + checkboxes.length + ' columnas');
        return false;
    };

    window.deselectAllColumns = function() {
        // Buscar todos los checkboxes de columnas usando múltiples métodos
        let checkboxes = [];
        
        // Método 1: Por clase
        checkboxes = Array.from(document.querySelectorAll('.column-checkbox'));
        
        // Método 2: Por nombre si el primero no encuentra nada
        if (checkboxes.length === 0) {
            checkboxes = Array.from(document.querySelectorAll('input[name="columns[]"]'));
        }
        
        // Método 3: Por tipo y clase form-check-input
        if (checkboxes.length === 0) {
            checkboxes = Array.from(document.querySelectorAll('input.form-check-input[type="checkbox"]'));
        }
        
        // Método 4: Todos los checkboxes dentro del formulario
        if (checkboxes.length === 0) {
            const form = document.getElementById('personalizedReportForm');
            if (form) {
                checkboxes = Array.from(form.querySelectorAll('input[type="checkbox"][name="columns[]"]'));
            }
        }
        
        // Aplicar la deselección
        checkboxes.forEach(function(checkbox) {
            if (checkbox && checkbox.type === 'checkbox') {
                checkbox.checked = false;
            }
        });
        
        console.log('Deseleccionadas ' + checkboxes.length + ' columnas');
        return false;
    };

    // Manejar el icono del collapse de columnas
    $(document).on('show.bs.collapse', '#columnsCollapse', function () {
        $('#columnsIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    
    $(document).on('hide.bs.collapse', '#columnsCollapse', function () {
        $('#columnsIcon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // Manejar el icono del collapse de filtros
    $(document).on('show.bs.collapse', '#filtersCollapse', function () {
        $('#filterIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    
    $(document).on('hide.bs.collapse', '#filtersCollapse', function () {
        $('#filterIcon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // Validar antes de enviar el formulario
    $('#personalizedReportForm').on('submit', function(e) {
        const checked = $('.column-checkbox:checked').length;
        
        if (checked === 0) {
            e.preventDefault();
            alert('Por favor, selecciona al menos una columna.');
            return false;
        }
    });

    // Inicializar al cargar la página
    $(document).ready(function() {
        // También vincular eventos con jQuery como respaldo
        $('#selectAllBtn').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (typeof selectAllColumns === 'function') {
                selectAllColumns();
            } else {
                // Fallback directo
                $('input.column-checkbox, input[name="columns[]"]').prop('checked', true);
            }
            return false;
        });
        
        $('#deselectAllBtn').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (typeof deselectAllColumns === 'function') {
                deselectAllColumns();
            } else {
                // Fallback directo
                $('input.column-checkbox, input[name="columns[]"]').prop('checked', false);
            }
            return false;
        });
    });
</script>
@endpush

