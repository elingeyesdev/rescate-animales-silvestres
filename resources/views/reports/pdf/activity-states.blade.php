<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Actividad por Estados</title>
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

        .totals {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .totals-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .totals-grid .total-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .total-item .number {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .total-item.en-peligro { background-color: #f8d7da; }
        .total-item.en-traslado { background-color: #fff3cd; }
        .total-item.tratados { background-color: #d4edda; }
        .total-item.liberados { background-color: #d1ecf1; }

        .table-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .table-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 10px;
            padding: 8px;
            color: white;
        }

        .table-title.en-peligro { background-color: #dc3545; }
        .table-title.en-traslado { background-color: #ffc107; }
        .table-title.tratados { background-color: #28a745; }
        .table-title.liberados { background-color: #17a2b8; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }

        table thead {
            background-color: #f8f9fa;
        }

        table th {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            background-color: #e9ecef;
        }

        table td {
            padding: 6px;
            border: 1px solid #ddd;
        }

        table tbody tr {
            page-break-inside: avoid;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .badge-danger { background-color: #dc3545; color: white; }
        .badge-warning { background-color: #ffc107; color: #333; }
        .badge-success { background-color: #28a745; color: white; }
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
                <h1>Reporte de Actividad por Estados</h1>
                <div class="subtitle">Sistema de Rescate de Animales</div>
            </div>
            <div class="banner-right">
                <div class="date">{{ $fechaGeneracion }}</div>
            </div>
        </div>
    </div>

    <div class="container">

    <div class="totals">
        <div class="totals-grid">
            <div class="total-item en-peligro">
                <div class="number">{{ $totals['en_peligro'] }}</div>
                <div>En Peligro</div>
            </div>
            <div class="total-item en-traslado">
                <div class="number">{{ $totals['rescatados'] }}</div>
                <div>En Traslado</div>
            </div>
            <div class="total-item tratados">
                <div class="number">{{ $totals['tratados'] }}</div>
                <div>Tratados</div>
            </div>
            <div class="total-item liberados">
                <div class="number">{{ $totals['liberados'] }}</div>
                <div>Liberados</div>
            </div>
        </div>
        <div style="text-align: center; font-weight: bold; margin-top: 10px;">
            Total General: {{ $totals['en_peligro'] + $totals['rescatados'] + $totals['tratados'] + $totals['liberados'] }}
        </div>
    </div>

    @if(!empty($enPeligro))
    <div class="table-section">
        <div class="table-title en-peligro">Reporte de Animales en Peligro</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Provincia</th>
                    <th style="width: 20%;">Estado</th>
                    <th style="width: 25%;">Fecha Hallazgo</th>
                    <th style="width: 30%;">Tiempo Transcurrido</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enPeligro as $report)
                <tr>
                    <td>{{ $report['province'] }}</td>
                    <td><span class="badge badge-danger">En Peligro</span></td>
                    <td>{{ $report['fecha_hallazgo']->format('d/m/Y H:i') }}</td>
                    <td>{{ $report['tiempo_transcurrido'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(!empty($rescatados))
    <div class="table-section">
        <div class="table-title en-traslado">Reporte de Animales en Traslado</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">Nombre</th>
                    <th style="width: 15%;">Estado</th>
                    <th style="width: 25%;">Centro de Destino</th>
                    <th style="width: 20%;">Fecha Traslado</th>
                    <th style="width: 20%;">Tiempo H-T</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rescatados as $report)
                <tr>
                    <td>{{ $report['nombre'] ?? 'Sin nombre' }}</td>
                    <td><span class="badge badge-warning">En Traslado</span></td>
                    <td>{{ $report['centro'] ? $report['centro']->nombre : '-' }}</td>
                    <td>{{ $report['fecha_traslado'] ? $report['fecha_traslado']->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $report['tiempo_hallazgo_traslado'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(!empty($tratados))
    <div class="table-section">
        <div class="table-title tratados">Reporte de Animales en Tratamiento</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">Nombre</th>
                    <th style="width: 15%;">Estado</th>
                    <th style="width: 20%;">Fecha Hallazgo</th>
                    <th style="width: 20%;">Fecha Inicio Tratamiento</th>
                    <th style="width: 25%;">Tiempo desde Tratamiento</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tratados as $report)
                <tr>
                    <td>{{ $report['nombre'] ?? 'Sin nombre' }}</td>
                    <td><span class="badge badge-success">Tratado</span></td>
                    <td>{{ $report['fecha_hallazgo']->format('d/m/Y H:i') }}</td>
                    <td>{{ $report['fecha_tratamiento'] ? $report['fecha_tratamiento']->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $report['tiempo_desde_tratamiento'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(!empty($liberados))
    <div class="table-section">
        <div class="table-title liberados">Reporte de Animales Liberados</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Nombre</th>
                    <th style="width: 20%;">Estado</th>
                    <th style="width: 25%;">Fecha Hallazgo</th>
                    <th style="width: 25%;">Fecha Liberación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($liberados as $report)
                <tr>
                    <td>{{ $report['nombre'] ?? 'Sin nombre' }}</td>
                    <td><span class="badge badge-info">Liberado</span></td>
                    <td>{{ $report['fecha_hallazgo']->format('d/m/Y H:i') }}</td>
                    <td>{{ $report['fecha_liberacion']->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Generado el {{ $fechaGeneracion }} | Sistema de Rescate de Animales
    </div>
    </div>
</body>
</html>

