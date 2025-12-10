<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Salud Animal Actual</title>
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
    <div class="header">
        <h1>Reporte de Salud Animal Actual</h1>
        <div class="meta">
            Generado el: {{ $fechaGeneracion }}
        </div>
    </div>

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
                <th style="width: 12%;">Nombre del Animal</th>
                <th style="width: 18%;">Diagnóstico Inicial</th>
                <th style="width: 12%;">Fecha Inicial</th>
                <th style="width: 12%;">Fecha Última Evaluación</th>
                <th style="width: 21%;">Última Intervención Médica</th>
                <th style="width: 10%;">Estado Actual</th>
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
                        <strong>Tipo:</strong> {{ $data['ultima_intervencion']['tipo'] }}<br>
                        @if($data['ultima_intervencion']['diagnostico'])
                            <strong>Diagnóstico:</strong> {{ $data['ultima_intervencion']['diagnostico'] }}<br>
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
        Sistema de Rescate Animal
    </div>
</body>
</html>

