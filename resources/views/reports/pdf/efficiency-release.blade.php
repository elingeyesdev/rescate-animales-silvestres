<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eficacia de la Liberación</title>
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
            font-family: Helvetica;
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

        .efficiency-summary {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #17a2b8;
            page-break-inside: avoid;
        }

        .efficiency-summary .percentage {
            font-size: 24pt;
            font-weight: bold;
            color: #17a2b8;
            text-align: center;
            margin: 10px 0;
        }

        .efficiency-summary .details {
            text-align: center;
            font-size: 9pt;
            color: #666;
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
            font-size: 8pt;
        }

        table thead {
            background-color: #17a2b8;
            color: white;
        }

        table th {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }

        table td {
            padding: 6px;
            border: 1px solid #ddd;
        }

        table tbody tr {
            page-break-inside: avoid;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            display: inline-block;
        }

        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #333; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }

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
                <h1>Eficacia de la Liberación</h1>
                <div class="subtitle">Sistema de Rescate de Animales</div>
            </div>
            <div class="banner-right">
                <div class="date">{{ $fechaGeneracion }}</div>
            </div>
        </div>
    </div>

    <div class="container">

    <div class="efficiency-summary">
        <div class="percentage">{{ $eficaciaMensual }}%</div>
        <div class="details">
            Eficacia de los Últimos 30 días<br>
            {{ $animalesLiberados30Dias }} animales liberados / {{ $animalesEstables30Dias }} animales estables
        </div>
    </div>

    @if($filtro)
    <div class="filter-info">
        <strong>Filtro aplicado:</strong>
        @if($filtro === 'semana')
            Última Semana
        @elseif($filtro === 'mes')
            Último Mes
        @elseif($filtro === 'rango' && $fechaDesde && $fechaHasta)
            Rango: {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
        @endif
    </div>
    @endif

    @if(!empty($datosDiarios))
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Fecha</th>
                <th style="width: 20%;">Animales Estables</th>
                <th style="width: 20%;">Animales Liberados</th>
                <th style="width: 20%;">Eficacia Diaria</th>
                <th style="width: 20%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datosDiarios as $dato)
            <tr>
                <td>{{ $dato['fecha']->format('d/m/Y') }}</td>
                <td>{{ $dato['estables'] }}</td>
                <td>{{ $dato['liberados'] }}</td>
                <td>{{ number_format($dato['eficacia'], 2) }}%</td>
                <td>
                    @if($dato['color'] === 'verde')
                        <span class="badge badge-success">100%</span>
                    @elseif($dato['color'] === 'amarillo')
                        <span class="badge badge-warning">> 50%</span>
                    @elseif($dato['color'] === 'azul')
                        <span class="badge badge-info">> 100%</span>
                    @else
                        <span class="badge badge-danger"><= 50%</span>
                    @endif
                </td>
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

