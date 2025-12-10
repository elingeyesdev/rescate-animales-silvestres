<!-- Gráfico de Eficacia Mensual -->
<div class="card mt-3">
    <div class="card-header bg-info text-white">
        <h3 class="card-title mb-0">
            <i class="fas fa-{{ $icon }} mr-2"></i>{{ $title }}
        </h3>
    </div>
    <div class="card-body py-2">
        <div class="row align-items-center">
            <div class="col-md-5 d-flex justify-content-center">
                <canvas id="eficaciaMensualChart{{ $management_subtab }}" style="max-height: 180px; max-width: 180px;"></canvas>
            </div>
            <div class="col-md-7 d-flex align-items-center justify-content-center">
                <div class="text-center">
                    <h2 class="mb-0" style="font-size: 2.5rem; font-weight: bold; color: #17a2b8;">
                        {{ $eficaciaMensual }}%
                    </h2>
                    <p class="text-muted mb-0">Eficacia de los Últimos 30 días</p>
                    <small class="text-muted">
                        {{ $valor230Dias }} {{ $label2 }} / {{ $valor130Dias }} {{ $label1 }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtro y Tabla de Datos Diarios -->
<div class="card mt-3">
    <div class="card-header bg-info text-white">
        <h3 class="card-title mb-0">
            <i class="fas fa-table mr-2"></i>Eficacia Diaria
        </h3>
    </div>
    <div class="card-body py-2">
        <!-- Filtro -->
        <form method="GET" action="{{ route('reportes.index') }}" class="mb-3">
            <input type="hidden" name="tab" value="management">
            <input type="hidden" name="management_subtab" value="{{ $management_subtab }}">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="text-muted"><i class="fas fa-filter mr-1"></i>Buscar por:</span>
                </div>
                <div class="col-auto">
                    <select name="filtro" id="filtro{{ $management_subtab }}" class="form-control" onchange="toggleFechaInputs{{ $management_subtab }}()">
                        <option value="semana" {{ $filtro === 'semana' ? 'selected' : '' }}>Última Semana</option>
                        <option value="mes" {{ $filtro === 'mes' ? 'selected' : '' }}>Último Mes</option>
                        <option value="rango" {{ $filtro === 'rango' ? 'selected' : '' }}>Rango entre Fechas</option>
                    </select>
                </div>
                <div class="col-auto" id="rangoFechas{{ $management_subtab }}" style="display: {{ $filtro === 'rango' ? 'block' : 'none' }};">
                    <label for="fecha_desde{{ $management_subtab }}" class="form-label mb-0 mr-2">
                        <i class="fas fa-calendar-alt mr-1"></i>Desde
                    </label>
                    <input type="date" 
                           class="form-control d-inline-block" 
                           style="width: auto;"
                           id="fecha_desde{{ $management_subtab }}" 
                           name="fecha_desde" 
                           value="{{ $fechaDesde ?? '' }}"
                           max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                </div>
                <div class="col-auto" id="rangoFechasHasta{{ $management_subtab }}" style="display: {{ $filtro === 'rango' ? 'block' : 'none' }};">
                    <label for="fecha_hasta{{ $management_subtab }}" class="form-label mb-0 mr-2">
                        <i class="fas fa-calendar-alt mr-1"></i>Hasta
                    </label>
                    <input type="date" 
                           class="form-control d-inline-block" 
                           style="width: auto;"
                           id="fecha_hasta{{ $management_subtab }}" 
                           name="fecha_hasta" 
                           value="{{ $fechaHasta ?? '' }}"
                           max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-filter mr-1"></i>Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="bg-info text-white">
                    <tr>
                        @foreach($columnas as $columna)
                            <th style="width: {{ 100 / count($columnas) }}%;">{{ $columna['nombre'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($datosDiarios))
                        @foreach($datosDiarios as $dato)
                            <tr>
                                @foreach($columnas as $columna)
                                    <td>
                                        @if($columna['campo'] === 'fecha')
                                            {{ $dato['fecha']->format('d/m/Y') }}
                                        @elseif($columna['campo'] === 'eficacia')
                                            {{ number_format($dato['eficacia'], 2) }}%
                                        @elseif($columna['campo'] === 'color')
                                            @if($dato['color'] === 'verde')
                                                <span class="badge badge-success" style="background-color: #28a745; padding: 8px 12px; font-size: 0.9rem;">100%</span>
                                            @elseif($dato['color'] === 'amarillo')
                                                <span class="badge badge-warning" style="background-color: #ffc107; padding: 8px 12px; font-size: 0.9rem;">> 50%</span>
                                            @elseif($dato['color'] === 'azul')
                                                <span class="badge badge-info" style="background-color: #17a2b8; padding: 8px 12px; font-size: 0.9rem;">> 100%</span>
                                            @else
                                                <span class="badge badge-danger" style="background-color: #dc3545; padding: 8px 12px; font-size: 0.9rem;">≤ 50%</span>
                                            @endif
                                        @else
                                            {{ $dato[$columna['campo']] ?? 0 }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ count($columnas) }}" class="text-center text-muted py-4">
                                No hay datos disponibles para el período seleccionado
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Eficacia Mensual
    const eficaciaCtx{{ $management_subtab }} = document.getElementById('eficaciaMensualChart{{ $management_subtab }}');
    if (eficaciaCtx{{ $management_subtab }}) {
        new Chart(eficaciaCtx{{ $management_subtab }}, {
            type: 'doughnut',
            data: {
                labels: ['Eficacia', 'Restante'],
                datasets: [{
                    data: [{{ $eficaciaMensual }}, {{ max(0, 100 - $eficaciaMensual) }}],
                    backgroundColor: ['#17a2b8', '#e9ecef'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    }
});

// Función para mostrar/ocultar campos de fecha según el filtro
function toggleFechaInputs{{ $management_subtab }}() {
    const filtro = document.getElementById('filtro{{ $management_subtab }}').value;
    const rangoFechas = document.getElementById('rangoFechas{{ $management_subtab }}');
    const rangoFechasHasta = document.getElementById('rangoFechasHasta{{ $management_subtab }}');
    
    if (filtro === 'rango') {
        rangoFechas.style.display = 'block';
        rangoFechasHasta.style.display = 'block';
    } else {
        rangoFechas.style.display = 'none';
        rangoFechasHasta.style.display = 'none';
    }
}
</script>

