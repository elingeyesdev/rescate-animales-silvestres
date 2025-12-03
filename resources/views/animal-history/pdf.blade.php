<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Animal - {{ $animalFile->animal?->nombre ?? 'Sin nombre' }}</title>
    <style>
        /* RESET & BASICS */
        @page {
            margin: 0cm; /* Sin margen para que el header toque los bordes */
        }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.5;
            background-color: #ffffff;
            margin: 0;
        }

        /* --- HEADER EMPRESARIAL (LIMPIO) --- */
        .header-banner {
            background-color: #2c3e50; /* Azul Corporativo Oscuro */
            color: #ffffff;
            padding: 40px 50px;
            height: 110px; 
            /* Se eliminó el border-bottom rojo */
            display: table;
            width: 100%;
            box-sizing: border-box; 
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
            width: 60%;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 40%;
        }

        .header-title {
            font-size: 26px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            line-height: 1.2;
        }

        .header-subtitle {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
            opacity: 0.8;
            margin-top: 5px;
        }

        .header-meta-item {
            font-size: 10px;
            margin-bottom: 3px;
            color: #ecf0f1;
        }
        
        .header-meta-item strong {
            color: #fff;
            text-transform: uppercase;
        }

        /* --- CONTENEDOR DEL CONTENIDO --- */
        .content-wrapper {
            padding: 50px;
        }

        /* --- ESTILOS GENERALES --- */
        .section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            border-bottom: 1px solid #2c3e50;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        /* TABLA DE INFO */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .info-row td {
            padding: 8px 5px;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            width: 30%;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            font-size: 8pt;
            vertical-align: top;
        }

        .info-value {
            width: 70%;
            color: #000;
            vertical-align: top;
        }

        /* BADGE */
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            color: #333;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* TIMELINE ESTILO AUDITORÍA */
        .timeline {
            border-left: 2px solid #ddd;
            margin-left: 5px;
            padding-left: 25px;
        }

        .timeline-item {
            margin-bottom: 30px;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -31px;
            top: 4px;
            width: 10px;
            height: 10px;
            background-color: #fff;
            border: 2px solid #2c3e50;
            border-radius: 50%;
        }

        .date-separator {
            font-size: 10px;
            font-weight: bold;
            background: #2c3e50;
            color: #fff;
            display: inline-block;
            padding: 3px 8px;
            border-radius: 2px;
            margin: 0 0 15px -25px;
        }

        .timeline-header {
            font-size: 11px;
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .timeline-meta {
            font-size: 9px;
            color: #7f8c8d;
            font-family: monospace;
            margin-bottom: 10px;
        }

        .timeline-details {
            background-color: #f9f9f9;
            border-left: 3px solid #ccc;
            padding: 10px;
            font-size: 9pt;
        }

        .timeline-detail-item { margin-bottom: 4px; color: #444; }
        .timeline-detail-item strong { color: #222; font-size: 8pt; text-transform: uppercase; }

        .image-container { margin-top: 10px; }
        .evidence-img {
            max-width: 120px;
            border: 1px solid #ddd;
            padding: 3px;
            background: #fff;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 40px;
            left: 50px;
            right: 50px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .footer p {
            font-size: 7pt;
            color: #999;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .no-data {
            padding: 20px;
            text-align: center;
            color: #999;
            border: 1px dashed #ddd;
            background: #fafafa;
        }
    </style>
</head>
<body>

    <div class="header-banner">
        <div class="header-left">
            <h1 class="header-title">Rescate Animal</h1>
            <p class="header-subtitle">Reporte Oficial de Seguimiento</p>
        </div>
        <div class="header-right">
            <div class="header-meta-item"><strong>Expediente:</strong> {{ $animalFile->id ?? '---' }}</div>
            <div class="header-meta-item"><strong>Fecha:</strong> {{ date('d/m/Y') }}</div>
            <div class="header-meta-item"><strong>Uso Interno</strong></div>
        </div>
    </div>

    <div class="content-wrapper">
        
        <div class="section">
            <div class="section-title">I. Información General del Paciente</div>
            
            <div class="animal-file-box">
                <table class="info-grid">
                    <tr class="info-row">
                        <td class="info-label">Nombre</td>
                        <td class="info-value">{{ $animalFile->animal?->nombre ?? '-' }}</td>
                    </tr>
                    <tr class="info-row">
                        <td class="info-label">Especie / Sexo</td>
                        <td class="info-value">
                            {{ $animalFile->species?->nombre ?? '-' }} / 
                            {{ $animalFile->animal?->sexo ?? '-' }}
                        </td>
                    </tr>
                    <tr class="info-row">
                        <td class="info-label">Estado Administrativo</td>
                        <td class="info-value">
                            @if($animalFile->animalStatus)
                                <span class="status-badge">{{ $animalFile->animalStatus->nombre }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr class="info-row">
                        <td class="info-label">Ubicación (Centro)</td>
                        <td class="info-value">{{ $animalFile->center?->nombre ?? '-' }}</td>
                    </tr>
                    
                    @if(!empty($animalFileImagePath))
                    <tr class="info-row">
                        <td class="info-label">Registro Visual</td>
                        <td class="info-value">
                            @php
                                $imageFullPath = str_starts_with($animalFileImagePath, 'temp/') 
                                    ? storage_path('app/' . $animalFileImagePath)
                                    : public_path('storage/' . $animalFileImagePath);
                            @endphp
                            @if(file_exists($imageFullPath))
                                <img src="{{ $imageFullPath }}" class="evidence-img" alt="Foto Paciente">
                            @endif
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">II. Registro Cronológico de Eventos</div>
            
            @if(!empty($timeline))
                <div class="timeline">
                    @php $currentDate = null; @endphp
                    @foreach($timeline as $t)
                        @php
                            $datetime = trim($t['changed_at'] ?? '');
                            $date = $datetime ? explode(' ', $datetime)[0] : '';
                            $time = $datetime && strpos($datetime, ' ') !== false ? trim(substr($datetime, strpos($datetime, ' '))) : '';
                            $title = $t['title'] ?? 'REGISTRO DE SISTEMA';
                        @endphp
                        
                        @if($date && $date !== $currentDate)
                            <div class="date-separator">{{ $date }}</div>
                            @php $currentDate = $date; @endphp
                        @endif
                        
                        <div class="timeline-item">
                            <div class="timeline-header">{{ $title }}</div>
                            <div class="timeline-meta">REGISTRADO A LAS {{ $time ?: '--:--' }} HRS</div>
                            
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
                                    <div class="image-container">
                                        <img src="{{ $imageFullPath }}" class="evidence-img" alt="Evidencia">
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-data">No constan registros de actividad para este expediente.</div>
            @endif
        </div>
        
        <div class="footer">
            <p>Sistema de Gestión Integral - Rescate Animal</p>
            <p>Información Confidencial | Generado el {{ $generatedAt }}</p>
        </div>

    </div>
</body>
</html>