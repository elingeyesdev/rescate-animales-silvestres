<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Personalizado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #4B5563;
            color: white;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        td {
            padding: 6px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .header-info {
            margin-bottom: 20px;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Reporte Personalizado</h1>
    
    <div class="header-info">
        <p><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        @if(isset($animalName) && $animalName)
            <p><strong>Filtro - Nombre Animal:</strong> {{ $animalName }}</p>
        @endif
        @if(isset($centerId) && $centerId)
            <p><strong>Filtro - Centro:</strong> {{ $centers->firstWhere('id', $centerId)->nombre ?? 'N/A' }}</p>
        @endif
        @if(isset($dateFrom) && $dateFrom)
            <p><strong>Fecha Desde:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}</p>
        @endif
        @if(isset($dateTo) && $dateTo)
            <p><strong>Fecha Hasta:</strong> {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        @endif
    </div>
    
    @if(isset($isGrouped) && $isGrouped && isset($groupedData) && count($groupedData) > 0)
        @foreach($groupedData as $group)
            <h2 style="margin-top: 20px; color: #333;">
                {{ $group['person_name'] }} 
                @if($group['group_type'] === 'veterinarian')
                    - {{ $group['total_evaluations'] }} Evaluaciones
                @else
                    - {{ $group['total_transfers'] }} Traslados
                @endif
            </h2>
            <table>
                <thead>
                    <tr>
                        @if($group['group_type'] === 'veterinarian')
                            <th>Animal</th>
                            <th>Diagnóstico</th>
                            <th>Fecha Evaluación</th>
                            <th>Tipo de Tratamiento</th>
                            <th>Especie</th>
                            <th>Estado</th>
                        @else
                            <th>Animal</th>
                            <th>Centro Destino</th>
                            <th>Fecha Traslado</th>
                            <th>Ubicación Rescate</th>
                            <th>Fecha Rescate</th>
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
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        @endforeach
    @else
        <table>
            <thead>
                <tr>
                    @foreach($selectedColumns as $col)
                        <th>{{ $availableColumns[$col] ?? $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                    <tr>
                        @foreach($selectedColumns as $col)
                            <td>{{ $row[$col] ?? 'N/A' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    <div style="margin-top: 20px; font-size: 9px; color: #666; text-align: center;">
        <p>Total de registros: 
            @if(isset($isGrouped) && $isGrouped && isset($groupedData))
                {{ count($groupedData) }} grupos
            @else
                {{ count($reportData ?? []) }}
            @endif
        </p>
    </div>
</body>
</html>

