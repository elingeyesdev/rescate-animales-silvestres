<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Salud Animal Actual</title>
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
            font-family: Arial;
            font-size: 9pt;
            color: #333;
            line-height: 1.4;
            background: #f5f5f5;
        }
        
        * {
            font-family: Helvetica !important;
        }

        /* Banner profesional */
        .banner {
            background: #1e3c72;
            color: white;
            padding: 15px 30px;
            margin-bottom: 15px;
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
            font-size: 20pt;
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

        .banner .date {
            font-size: 9pt;
            font-weight: 600;
            margin-bottom: 3px;
        }

        /* Contenedor principal */
        .container {
            padding: 0 30px 20px 30px;
        }

        .filter-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #17a2b8;
            font-size: 8pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 7pt;
        }

        table thead {
            background-color: #28a745;
            color: white;
        }

        table th {
            padding: 8px 4px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }

        table td {
            padding: 6px 4px;
            border: 1px solid #ddd;
        }

        table tbody tr {
            page-break-inside: avoid;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .badge-info { background-color: #17a2b8; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 7pt;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Banner Profesional -->
    <div class="banner">
        <div class="banner-content">
            <div class="banner-left">
                <h1>Reporte de Salud Animal Actual</h1>
                <div class="subtitle">Sistema de Rescate de Animales</div>
            </div>
            <div class="banner-right">
                <div class="date">{{ $fechaGeneracion }}</div>
            </div>
        </div>
    </div>

    <div class="container">

    @if($fechaDesde || $fechaHasta)
    <div class="filter-info">
        <strong>Filtro aplicado:</strong>
        @if($fechaDesde && $fechaHasta)
            Desde: {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} - Hasta: {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
        @elseif($fechaDesde)
            Desde: {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }}
        @elseif($fechaHasta)
            Hasta: {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
        @endif
    </div>
    @endif

    @if(!empty($healthData))
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Centro</th>
                <th style="width: 10%;">Nombre del Animal</th>
                <th style="width: 15%;">Diagnóstico Inicial</th>
                <th style="width: 12%;">Fecha Inicial</th>
                <th style="width: 12%;">Fecha Última Evaluación</th>
                <th style="width: 21%;">Última Intervención Médica</th>
                <th style="width: 15%;">Estado Actual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($healthData as $data)
            <tr>
                <td>{{ $data['centro'] }}</td>
                <td>{{ $data['nombre_animal'] }}</td>
                <td>{{ $data['diagnostico_inicial'] }}</td>
                <td>{{ $data['fecha_creacion_hoja'] ? $data['fecha_creacion_hoja']->format('d/m/Y') : '-' }}</td>
                <td>{{ $data['fecha_ultima_evaluacion'] ? $data['fecha_ultima_evaluacion']->format('d/m/Y') : '-' }}</td>
                <td>
                    @if($data['ultima_intervencion'])
                        @if($data['ultima_intervencion']['diagnostico'])
                            {{ $data['ultima_intervencion']['diagnostico'] }}<br>
                        @endif
                        @if($data['ultima_intervencion']['descripcion'])
                            {{ mb_substr($data['ultima_intervencion']['descripcion'], 0, 50) }}{{ mb_strlen($data['ultima_intervencion']['descripcion']) > 50 ? '...' : '' }}
                        @endif
                    @else
                        Sin intervenciones registradas
                    @endif
                </td>
                <td><span class="badge badge-secondary">{{ $data['estado_actual'] }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 20px; color: #666;">
        No hay datos disponibles para el período seleccionado
    </div>
    @endif

    <div class="footer">
        Generado el {{ $fechaGeneracion }} | Sistema de Rescate de Animales
    </div>
    </div>
</body>
</html>

