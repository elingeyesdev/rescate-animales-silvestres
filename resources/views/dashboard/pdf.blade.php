<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Panel de Control</title>
    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.4;
            background: #f5f5f5;
        }

        /* Banner profesional */
        .banner {
            background: #1e3c72;
            color: white;
            padding: 15px 30px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .banner-content {
            display: table;
            width: 100%;
        }

        .banner-left {
            display: table-cell;
            vertical-align: middle;
            width: 70%;
        }

        .banner-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 30%;
        }

        .banner h1 {
            font-size: 22pt;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .banner .subtitle {
            font-size: 9pt;
            opacity: 0.95;
            font-weight: 300;
        }

        .banner .meta {
            font-size: 8pt;
            opacity: 0.9;
            margin-top: 3px;
        }

        .banner .date {
            font-size: 10pt;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .banner .user {
            font-size: 9pt;
            opacity: 0.9;
        }

        /* Contenedor principal */
        .container {
            padding: 0 30px 20px 30px;
        }

        /* Secciones */
        .section {
            background: white;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            page-break-inside: avoid;
        }

        .section.first-section {
            margin-top: 0;
            padding-top: 15px;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #2a5298;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            font-size: 18pt;
        }

        /* KPIs Grid */
        .kpi-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 12px;
        }

        .kpi-item {
            display: table-cell;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            text-align: center;
            border-left: 3px solid #2a5298;
            vertical-align: top;
        }

        .kpi-item .label {
            font-size: 8pt;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .kpi-item .value {
            font-size: 20pt;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 3px;
        }

        .kpi-item .sub-value {
            font-size: 7pt;
            color: #999;
        }

        /* Cards de KPIs */
        .kpi-cards {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 12px;
        }

        .kpi-card {
            display: table-cell;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 12px;
            border-radius: 4px;
            border-top: 3px solid #2a5298;
            vertical-align: top;
        }

        .kpi-card .card-header {
            font-size: 9pt;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .kpi-card .card-body {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }

        .kpi-card .card-body-item {
            display: table-cell;
            text-align: center;
            padding: 6px;
        }

        .kpi-card .card-body-item .number {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 2px;
        }

        .kpi-card .card-body-item .label {
            font-size: 7pt;
            color: #666;
        }

        /* Tablas */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 8pt;
        }

        .data-table th {
            background: #1e3c72;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
        }

        .data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .data-table tr:hover {
            background: #e9ecef;
        }

        /* Estadísticas */
        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px;
        }

        .stats-item {
            display: table-cell;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #28a745;
            vertical-align: top;
        }

        .stats-item .title {
            font-size: 9pt;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .stats-item .content {
            font-size: 8pt;
            color: #666;
        }

        /* Nota del mapa */
        .map-note {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 15px;
            margin-top: 15px;
            font-size: 9pt;
            color: #856404;
        }

        .map-note strong {
            display: block;
            margin-bottom: 5px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            font-size: 8pt;
            color: #999;
        }

        /* Utilidades */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mt-20 {
            margin-top: 20px;
        }

        /* Colores de badges */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .badge-primary {
            background: #2a5298;
            color: white;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #333;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }

        .badge-danger {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Banner Profesional -->
    <div class="banner">
        <div class="banner-content">
            <div class="banner-left">
                <h1>Panel de Control</h1>
                <div class="subtitle">Sistema de Rescate de Animales</div>
                <div class="meta">Reporte Administrativo Completo</div>
            </div>
            <div class="banner-right">
                <div class="date">{{ $fechaGeneracion }}</div>
                <div class="user">Usuario: {{ $usuario->name ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <div class="container">
        @php
            $isAdmin = $usuario->hasAnyRole(['admin', 'encargado']);
        @endphp
        @if($isAdmin)
            <!-- SECCIÓN 1: RESUMEN -->
            <div class="section first-section">
                <div class="section-title">
                    <span>RESUMEN EJECUTIVO</span>
                </div>

                <!-- KPIs Principales -->
                <div class="kpi-grid">
                    <div class="kpi-item">
                        <div class="label">Hallazgos Pendientes</div>
                        <div class="value">{{ $pendingReportsCount ?? 0 }}</div>
                        <div class="sub-value">
                            @php 
                                $total = $totalReports ?? 0; 
                                $pending = $pendingReportsCount ?? 0; 
                                $pct = $total > 0 ? intval(($pending / $total) * 100) : 0; 
                            @endphp
                            {{ $pct }}% del total reportado
                        </div>
                    </div>
                    <div class="kpi-item">
                        <div class="label">Solicitudes Pendientes</div>
                        <div class="value">{{ ($pendingRescuersCount ?? 0) + ($pendingVeterinariansCount ?? 0) + ($pendingCaregiversCount ?? 0) }}</div>
                        <div class="sub-value">Revisión requerida</div>
                    </div>
                    <div class="kpi-item">
                        <div class="label">Animales en Sistema</div>
                        <div class="value">{{ $totalAnimals ?? 0 }}</div>
                        <div class="sub-value">Total registrados</div>
                    </div>
                    <div class="kpi-item">
                        <div class="label">Mensajes Nuevos</div>
                        <div class="value">{{ $unreadMessagesCount ?? 0 }}</div>
                        <div class="sub-value">Bandeja de entrada</div>
                    </div>
                </div>

                <!-- KPIs de Actividad y Eficacia -->
                <div class="kpi-cards">
                    <div class="kpi-card">
                        <div class="card-header">Actividad</div>
                        <div class="card-body">
                            <div class="card-body-item">
                                <div class="number" style="color: #17a2b8;">{{ $animalsBeingRescued ?? 0 }}</div>
                                <div class="label">Rescatados</div>
                            </div>
                            <div class="card-body-item">
                                <div class="number" style="color: #ffc107;">{{ $animalsBeingTransferred ?? 0 }}</div>
                                <div class="label">Trasladados</div>
                            </div>
                            <div class="card-body-item">
                                <div class="number" style="color: #28a745;">{{ $animalsBeingTreated ?? 0 }}</div>
                                <div class="label">Tratados</div>
                            </div>
                        </div>
                    </div>
                    <div class="kpi-card">
                        <div class="card-header">Eficacia</div>
                        <div class="card-body">
                            <div class="card-body-item">
                                <div class="number">{{ $efficiencyAttendedRescued['percentage'] ?? 0 }}%</div>
                                <div class="label">Atendidos/Rescatados</div>
                                <div class="sub-value" style="font-size: 7pt; margin-top: 5px;">
                                    {{ $efficiencyAttendedRescued['attended'] ?? 0 }} / {{ $efficiencyAttendedRescued['rescued'] ?? 0 }}
                                </div>
                            </div>
                            <div class="card-body-item">
                                <div class="number">{{ $efficiencyReadyAttended['percentage'] ?? 0 }}%</div>
                                <div class="label">Listos/Atendidos</div>
                                <div class="sub-value" style="font-size: 7pt; margin-top: 5px;">
                                    {{ $efficiencyReadyAttended['ready'] ?? 0 }} / {{ $efficiencyReadyAttended['attended'] ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI de Efectividad (debajo) -->
                <div class="kpi-cards" style="margin-top: 10px;">
                    <div class="kpi-card" style="width: 100%;">
                        <div class="card-header">Efectividad</div>
                        <div class="card-body">
                            <div class="card-body-item" style="width: 100%; text-align: center;">
                                <div class="number" style="font-size: 28pt;">{{ $effectivenessReleasedRescued['percentage'] ?? 0 }}%</div>
                                <div class="label" style="font-size: 9pt; margin-top: 6px;">Liberados/Rescatados</div>
                                <div class="sub-value" style="font-size: 8pt; margin-top: 5px;">
                                    {{ $effectivenessReleasedRescued['released'] ?? 0 }} / {{ $effectivenessReleasedRescued['rescued'] ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: ANÁLISIS -->
            <div class="section">
                <div class="section-title">
                    <span>ANÁLISIS Y ESTADÍSTICAS</span>
                </div>

                <!-- Estadísticas por Mes -->
                <div class="stats-grid">
                    <div class="stats-item">
                        <div class="title">Reportes (últimos 6 meses)</div>
                        <div class="content">
                            Total: {{ array_sum($reportsByMonth ?? []) }}<br>
                            Promedio mensual: {{ count($reportsByMonth ?? []) > 0 ? round(array_sum($reportsByMonth ?? []) / count($reportsByMonth ?? [])) : 0 }}
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="title">Traslados (últimos 6 meses)</div>
                        <div class="content">
                            Total: {{ array_sum($transfersByMonth ?? []) }}<br>
                            Promedio mensual: {{ count($transfersByMonth ?? []) > 0 ? round(array_sum($transfersByMonth ?? []) / count($transfersByMonth ?? [])) : 0 }}
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="title">Liberaciones (últimos 6 meses)</div>
                        <div class="content">
                            Total: {{ array_sum($releasesByMonth ?? []) }}<br>
                            Promedio mensual: {{ count($releasesByMonth ?? []) > 0 ? round(array_sum($releasesByMonth ?? []) / count($releasesByMonth ?? [])) : 0 }}
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="title">Hojas de Animal (últimos 6 meses)</div>
                        <div class="content">
                            Total: {{ array_sum($animalFilesByMonth ?? []) }}<br>
                            Promedio mensual: {{ count($animalFilesByMonth ?? []) > 0 ? round(array_sum($animalFilesByMonth ?? []) / count($animalFilesByMonth ?? [])) : 0 }}
                        </div>
                    </div>
                </div>

                <!-- Animales por Estado -->
                @if(isset($animalsByStatus) && count($animalsByStatus) > 0)
                <div class="mt-20">
                    <div class="title" style="font-size: 10pt; font-weight: bold; color: #1e3c72; margin-bottom: 10px;">
                        Animales por Estado
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th class="text-center">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($animalsByStatus as $status => $count)
                                <tr>
                                    <td>{{ $status }}</td>
                                    <td class="text-center">{{ $count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                <!-- Top 5 Voluntarios -->
                @if(isset($topVolunteers) && count($topVolunteers) > 0)
                <div class="mt-20">
                    <div class="title" style="font-size: 10pt; font-weight: bold; color: #1e3c72; margin-bottom: 10px;">
                        Top 5 Voluntarios Más Activos
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Voluntario</th>
                                <th class="text-center">Total</th>
                                <th>Desglose</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topVolunteers as $index => $volunteer)
                                <tr>
                                    <td>
                                        @if($index === 0)
                                            🥇
                                        @elseif($index === 1)
                                            🥈
                                        @elseif($index === 2)
                                            🥉
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $volunteer['nombre'] }}</strong><br>
                                        <small style="color: #999;">{{ $volunteer['email'] }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $volunteer['total'] }}</span>
                                    </td>
                                    <td>
                                        @if($volunteer['reports'] > 0)
                                            <span class="badge badge-info">{{ $volunteer['reports'] }} Hallazgos</span>
                                        @endif
                                        @if($volunteer['transfers'] > 0)
                                            <span class="badge badge-success">{{ $volunteer['transfers'] }} Traslados</span>
                                        @endif
                                        @if($volunteer['evaluations'] > 0)
                                            <span class="badge badge-warning">{{ $volunteer['evaluations'] }} Evaluaciones</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                <!-- Solicitudes de Voluntariado -->
                @if(isset($applicationsByType))
                <div class="mt-20">
                    <div class="title" style="font-size: 10pt; font-weight: bold; color: #1e3c72; margin-bottom: 10px;">
                        Solicitudes de Voluntariado
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th class="text-center">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applicationsByType as $type => $count)
                                <tr>
                                    <td>{{ $type }}</td>
                                    <td class="text-center">{{ $count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <!-- SECCIÓN 3: MAPA DE CAMPO -->
            <div class="section">
                <div class="section-title">
                    <span>MAPA DE CAMPO</span>
                </div>

                <div class="stats-grid">
                    <div class="stats-item">
                        <div class="title">Hallazgos Aprobados</div>
                        <div class="content">
                            @php
                                $reportsCount = isset($reports) ? (is_countable($reports) ? count($reports) : 0) : 0;
                                $reportsWithCoords = 0;
                                if (isset($reports) && is_countable($reports)) {
                                    foreach ($reports as $report) {
                                        if (isset($report['latitud']) && isset($report['longitud']) && $report['latitud'] && $report['longitud']) {
                                            $reportsWithCoords++;
                                        }
                                    }
                                }
                            @endphp
                            Total: {{ $reportsCount }}<br>
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="title">Animales Liberados</div>
                        <div class="content">
                            @php
                                $releasesCount = isset($releases) ? (is_countable($releases) ? count($releases) : 0) : 0;
                                $releasesWithCoords = 0;
                                if (isset($releases) && is_countable($releases)) {
                                    foreach ($releases as $release) {
                                        if (isset($release['latitud']) && isset($release['longitud']) && $release['latitud'] && $release['longitud']) {
                                            $releasesWithCoords++;
                                        }
                                    }
                                }
                            @endphp
                            Total: {{ $releasesCount }}<br>
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="title">Focos de Calor</div>
                        <div class="content">
                            @php
                                $focosCount = isset($focosCalorFormatted) ? (is_countable($focosCalorFormatted) ? count($focosCalorFormatted) : 0) : 0;
                            @endphp
                            Total recientes: {{ $focosCount }}<br>
                            Monitoreo activo
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="title">Especies Registradas</div>
                        <div class="content">
                            @php
                                $speciesCount = isset($species) ? (is_countable($species) ? count($species) : 0) : 0;
                            @endphp
                            Total: {{ $speciesCount }}<br>
                            En liberaciones
                        </div>
                    </div>
                </div>

                <div class="map-note">
                    <strong>Nota:</strong>
                    El mapa interactivo no puede ser exportado en formato PDF. 
                    Los datos mostrados arriba representan un resumen de la información geográfica disponible en el sistema.
                    Para visualizar el mapa completo con todas las ubicaciones, favor acceder al sistema web.
                </div>
            </div>
        @else
            <!-- Vista para otros roles -->
            <div class="section">
                <div class="section-title">
                    <span>RESUMEN</span>
                </div>

                <div class="kpi-grid">
                    <div class="kpi-item">
                        <div class="label">Animales Rescatados</div>
                        <div class="value">{{ $totalAnimals ?? 0 }}</div>
                    </div>
                    <div class="kpi-item">
                        <div class="label">Devueltos al Hábitat</div>
                        <div class="value">{{ $releasedAnimals ?? 0 }}</div>
                        @php 
                            $totalA = $totalAnimals ?? 0; 
                            $released = $releasedAnimals ?? 0; 
                            $rpct = $totalA > 0 ? intval(($released / $totalA) * 100) : 0; 
                        @endphp
                        <div class="sub-value">{{ $rpct }}% tasa de éxito</div>
                    </div>
                    <div class="kpi-item">
                        <div class="label">Hallazgos Recibidos</div>
                        <div class="value">{{ $totalReports ?? 0 }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div>Generado el {{ $fechaGeneracion }} | Sistema de Rescate de Animales</div>
            <div style="margin-top: 5px;">Este es un documento generado automáticamente. Para más información, consulte el sistema web.</div>
        </div>
    </div>
</body>
</html>

