<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Animal - {{ $animalFile->animal?->nombre ?? 'Sin nombre' }}</title>
    <style>
        /* RESET & BASICS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif; /* DejaVu Sans es mejor para DomPDF y acentos */
            font-size: 10pt;
            color: #2c3e50; /* Gris oscuro azulado en lugar de negro puro */
            line-height: 1.5;
            background-color: #ffffff;
            padding: 40px 50px; /* Márgenes de hoja A4 estándar */
        }

        /* HEADER / MEMBRETE */
        .header {
            border-bottom: 2px solid #2980b9; /* Azul corporativo */
            padding-bottom: 15px;
            margin-bottom: 35px;
            display: table;
            width: 100%;
        }

        .header-logo-text {
            display: table-cell;
            vertical-align: bottom;
            text-align: left;
            width: 60%;
        }

        .header-logo-text h1 {
            font-size: 24px;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
            margin: 0;
        }

        .header-logo-text p {
            font-size: 10px;
            color: #7f8c8d;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .header-meta {
            display: table-cell;
            vertical-align: bottom;
            text-align: right;
            width: 40%;
        }

        .report-badge {
            background-color: #f4f6f7;
            color: #2c3e50;
            padding: 5px 10px;
            border: 1px solid #bdc3c7;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }

        /* SECCIONES */
        .section {
            margin-bottom: 35px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2980b9;
            text-transform: uppercase;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        /* TABLA DE INFO (HOJA DE VIDA) */
        .animal-file-box {
            background-color: transparent;
            border: none;
            padding: 0;
            margin: 0;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .info-row {
            display: table-row;
        }

        /* Zebra Striping sutil para profesionalismo */
        .info-row:nth-child(odd) {
            background-color: #ffffff;
        }
        .info-row:nth-child(even) {
            background-color: #f8f9fa; 
        }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 8px 10px;
            font-weight: bold;
            color: #34495e;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            width: 70%;
            padding: 8px 10px;
            color: #555;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: top;
        }

        /* STATUS BADGE */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            background-color: #27ae60; /* Verde éxito corporativo */
            color: white;
            text-transform: uppercase;
        }

        /* IMÁGENES */
        .animal-image {
            max-width: 160px;
            max-height: 160px;
            border: 4px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 5px;
        }

        /* LÍNEA DE TIEMPO (Estilo Corporativo Limpio) */
        .timeline {
            margin: 10px 0;
            padding-left: 10px;
            border-left: 2px solid #bdc3c7; /* Línea guía gris */
        }

        .timeline-item {
            margin-bottom: 25px;
            padding-left: 20px;
            position: relative;
        }

        /* Pseudo-elemento para el punto de la timeline (simulado con borde) */
        .timeline-header {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .timeline-date-time {
            font-size: 9px;
            color: #7f8c8d;
            margin-bottom: 8px;
            font-family: monospace; /* Para tabular números */
        }

        .timeline-details {
            background-color: #fcfcfc;
            border: 1px solid #f1f1f1;
            padding: 10px;
            font-size: 10px;
            border-radius: 2px;
        }

        .timeline-detail-item {
            margin-bottom: 4px;
            color: #555;
        }

        .timeline-detail-item strong {
            color: #333;
        }

        .timeline-image {
            max-width: 120px;
            max-height: 120px;
            border: 1px solid #ddd;
            padding: 2px;
            background: white;
            margin-top: 8px;
        }

        /* SEPARADOR DE FECHA */
        .date-separator {
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            background-color: #95a5a6;
            padding: 4px 10px;
            border-radius: 10px;
            display: inline-block;
            margin: 15px 0 15px -20px; /* Alineado a la izquierda sobre la línea */
            position: relative;
            z-index: 10;
        }

        /* FOOTER */
        .footer {
            margin-top: 50px;
            padding-top: 15px;
            border-top: 1px solid #bdc3c7;
            text-align: center;
            font-size: 8px;
            color: #95a5a6;
        }

        .no-data {
            color: #7f8c8d;
            font-style: italic;
            background: #f9f9f9;
            padding: 15px;
            text-align: center;
            border: 1px dashed #ccc;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-logo-text">
            <h1>Rescate Animal</h1>
            <p>Reporte Oficial de Historial y Seguimiento</p>
        </div>
        <div class="header-meta">
            
            <div style="margin-top: 5px; font-size: 9px; color: #7f8c8d;">
                Ref: {{ $animalFile->id ?? 'N/A' }}
            </div>
        </div>
    </div>
    
    <div class="section">
        <div class="section-title">Ficha Técnica del Animal</div>
        
        <div class="animal-file-box">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Nombre</div>
                    <div class="info-value">{{ $animalFile->animal?->nombre ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Sexo</div>
                    <div class="info-value">{{ $animalFile->animal?->sexo ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Especie</div>
                    <div class="info-value">{{ $animalFile->species?->nombre ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Estado Actual</div>
                    <div class="info-value">
                        @if($animalFile->animalStatus)
                            <span class="status-badge">{{ $animalFile->animalStatus->nombre }}</span>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Centro Actual</div>
                    <div class="info-value">{{ $animalFile->center?->nombre ?? '-' }}</div>
                </div>
                
                @if(!empty($animalFileImagePath))
                <div class="info-row">
                    <div class="info-label">Registro Fotográfico</div>
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
    
    <div class="section">
        <div class="section-title">Eventos y Seguimiento</div>
        
        @if(!empty($timeline))
            <div class="timeline">
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
                            Hora: {{ $time ?: '--:--' }}
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
                                <div>
                                    <img src="{{ $imageFullPath }}" class="timeline-image" alt="Evidencia">
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-data">No se encontraron registros históricos para este expediente.</div>
        @endif
    </div>
    
    <div class="footer">
        <p>Generado el {{ $generatedAt }} | Sistema de Gestión Integral - Rescate Animal</p>
        <p>Este documento es para uso interno exclusivo. La información contenida está protegida.</p>
    </div>
</body>
</html>