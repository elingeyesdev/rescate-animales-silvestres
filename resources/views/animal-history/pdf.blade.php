<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Animal - {{ $animalFile->animal?->nombre ?? 'Sin nombre' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
            padding: 0 20px;
        }
        
        .header {
            background-color: #3c8dbc;
            color: white;
            padding: 30px 40px;
            text-align: center;
            margin: 0 -20px 30px -20px;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
        }
        
        .header p {
            font-size: 14px;
            font-weight: 500;
            color: white;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #3c8dbc;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        
        /* Cuadro destacado para la hoja de vida */
        .animal-file-box {
            background-color: #f8f9fa;
            border: 2px solid #3c8dbc;
            border-radius: 8px;
            padding: 25px 30px;
            margin: 0 15px 25px 15px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 10px 12px;
            font-weight: bold;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            background-color: #ffffff;
        }
        
        .info-value {
            display: table-cell;
            padding: 10px 12px;
            border-bottom: 1px solid #dee2e6;
            background-color: #ffffff;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            background-color: #17a2b8;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .timeline {
            margin: 20px 15px;
            padding: 0 10px;
        }
        
        .timeline-item {
            margin-bottom: 20px;
            padding: 18px 20px;
            border-left: 4px solid #3c8dbc;
            background-color: #ffffff;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            page-break-inside: avoid;
            margin-left: 15px;
            margin-right: 15px;
        }
        
        .timeline-header {
            font-size: 14px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .timeline-date-time {
            font-size: 10px;
            color: #6c757d;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px dashed #dee2e6;
        }
        
        .timeline-details {
            font-size: 10px;
            color: #495057;
            margin-top: 10px;
        }
        
        .timeline-detail-item {
            margin-bottom: 6px;
        }
        
        .timeline-image {
            max-width: 150px;
            max-height: 150px;
            margin-top: 10px;
            border-radius: 4px;
            border: 2px solid #dee2e6;
        }
        
        .date-separator {
            margin: 20px 15px 15px 15px;
            padding: 8px 15px;
            font-weight: bold;
            color: #3c8dbc;
            font-size: 13px;
            background-color: #f0f0f0;
            border-left: 4px solid #3c8dbc;
            border-radius: 4px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }
        
        .animal-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 4px;
            border: 2px solid #dee2e6;
            margin: 10px 0;
        }
        
        .no-data {
            color: #6c757d;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="color: white; font-size: 28px; font-weight: bold; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Rescate Animal</h1>
        <p style="color: white; font-size: 14px; font-weight: 500;">Sistema de Rescate Animal - Gestión Integral</p>
    </div>
    
    <!-- Información de la Hoja de Vida -->
    <div class="section">
        <div class="section-title" style="background-color: #3c8dbc; color: white; padding: 12px 20px; font-size: 16px; font-weight: bold; margin-bottom: 20px; border-radius: 6px;">Información de la Hoja de Vida</div>
        
        <div class="animal-file-box">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Nombre:</div>
                    <div class="info-value">{{ $animalFile->animal?->nombre ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Sexo:</div>
                    <div class="info-value">{{ $animalFile->animal?->sexo ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Especie:</div>
                    <div class="info-value">{{ $animalFile->species?->nombre ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Estado Actual:</div>
                    <div class="info-value">
                        @if($animalFile->animalStatus)
                            <span class="status-badge">{{ $animalFile->animalStatus->nombre }}</span>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Centro Actual:</div>
                    <div class="info-value">{{ $animalFile->center?->nombre ?? '-' }}</div>
                </div>
                @if(!empty($animalFileImagePath))
                <div class="info-row">
                    <div class="info-label">Imagen:</div>
                    <div class="info-value">
                        @php
                            $imageFullPath = str_starts_with($animalFileImagePath, 'temp/') 
                                ? storage_path('app/' . $animalFileImagePath)
                                : public_path('storage/' . $animalFileImagePath);
                        @endphp
                        @if(file_exists($imageFullPath))
                            <img src="{{ $imageFullPath }}" class="animal-image" alt="Imagen del animal">
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Línea de Tiempo -->
    <div class="section">
        <div class="section-title" style="background-color: #3c8dbc; color: white; padding: 12px 20px; font-size: 16px; font-weight: bold; margin-bottom: 20px; border-radius: 6px;">Línea de Tiempo del Historial</div>
        
        @if(!empty($timeline))
            @php $currentDate = null; @endphp
            @foreach($timeline as $t)
                @php
                    $datetime = trim($t['changed_at'] ?? '');
                    $date = $datetime ? explode(' ', $datetime)[0] : '';
                    $time = $datetime && strpos($datetime, ' ') !== false ? trim(substr($datetime, strpos($datetime, ' '))) : '';
                    $title = $t['title'] ?? 'Actualización';
                @endphp
                
                @if($date && $date !== $currentDate)
                    <div class="date-separator">
                        {{ $date }}
                    </div>
                    @php $currentDate = $date; @endphp
                @endif
                
                <div class="timeline-item">
                    <div class="timeline-header">{{ $title }}</div>
                    <div class="timeline-date-time">
                        <strong>Fecha:</strong> {{ $date ?: '-' }} &nbsp;&nbsp; <strong>Hora:</strong> {{ $time ?: '-' }}
                    </div>
                    
                    @if(!empty($t['details']))
                        <div class="timeline-details">
                            @foreach($t['details'] as $d)
                                <div class="timeline-detail-item">
                                    <strong>{{ $d['label'] }}:</strong> {{ $d['value'] }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    @if(!empty($t['image_url']))
                        @php
                            $imageFullPath = str_starts_with($t['image_url'], 'temp/') 
                                ? storage_path('app/' . $t['image_url'])
                                : public_path('storage/' . $t['image_url']);
                        @endphp
                        @if(file_exists($imageFullPath))
                            <div style="margin-top: 10px;">
                                <img src="{{ $imageFullPath }}" class="timeline-image" alt="Imagen del evento">
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
        @else
            <div class="no-data">No hay registros en el historial</div>
        @endif
    </div>
    
    <div class="footer">
        <p>Documento generado el {{ $generatedAt }} | Rescate Animal - Sistema de Rescate Animal</p>
        <p>Este documento contiene información confidencial del sistema de rescate animal.</p>
    </div>
</body>
</html>
