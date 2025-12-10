<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eficacia Mensual del Rescate de Animales</title>
    <style>
        @page {
            margin: 1.5cm;
        }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18pt;
            margin: 0;
            color: #2c3e50;
        }

        .header .meta {
            font-size: 8pt;
            color: #666;
            margin-top: 5px;
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
    <div class="header">
        <h1>Eficacia Mensual del Rescate de Animales</h1>
        <div class="meta">
            Generado el: {{ $fechaGeneracion }}
        </div>
    </div>

    <div class="efficiency-summary">
        <div class="percentage">{{ $eficaciaMensual }}%</div>
        <div class="details">
            Eficacia de los Últimos 30 días<br>
            {{ $traslados30Dias }} traslados / {{ $hallazgos30Dias }} hallazgos
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
                <th style="width: 20%;">Cantidad de Hallazgos</th>
                <th style="width: 20%;">Cantidad de Traslados</th>
                <th style="width: 20%;">Eficacia Diaria</th>
                <th style="width: 20%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datosDiarios as $dato)
            <tr>
                <td>{{ $dato['fecha']->format('d/m/Y') }}</td>
                <td>{{ $dato['hallazgos'] }}</td>
                <td>{{ $dato['traslados'] }}</td>
                <td>{{ number_format($dato['eficacia'], 2) }}%</td>
                <td>
                    @if($dato['color'] === 'verde')
                        <span class="badge badge-success">100%</span>
                    @elseif($dato['color'] === 'amarillo')
                        <span class="badge badge-warning">> 50%</span>
                    @elseif($dato['color'] === 'azul')
                        <span class="badge badge-info">> 100%</span>
                    @else
                        <span class="badge badge-danger">≤ 50%</span>
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
        Sistema de Rescate Animal
    </div>
</body>
</html>

