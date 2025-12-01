@extends('emails.layout')

@section('title', 'Nuevo Hallazgo de Animal en Peligro')

@section('header', 'Nuevo Hallazgo de Animal en Peligro')

@section('content')
    <h2>Se ha registrado un nuevo hallazgo de animal en peligro</h2>
    
    <div class="info-box">
        <p><strong>Fecha del reporte:</strong> {{ $report->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Reportado por:</strong> {{ $report->person->nombre ?? 'N/A' }}</p>
        @if($report->direccion)
            <p><strong>Ubicación:</strong> {{ $report->direccion }}</p>
        @endif
        @if($report->condicionInicial)
            <p><strong>Condición inicial:</strong> {{ $report->condicionInicial->nombre }}</p>
        @endif
        @if($report->incidentType)
            <p><strong>Tipo de incidente:</strong> {{ $report->incidentType->nombre }}</p>
        @endif
        @if($report->urgencia)
            <p><strong>Nivel de urgencia:</strong> {{ $report->urgencia }}/5</p>
        @endif
        @if($report->observaciones)
            <p><strong>Observaciones:</strong> {{ $report->observaciones }}</p>
        @endif
    </div>

    <p>Por favor, revise el hallazgo en el sistema para tomar las acciones correspondientes.</p>
    
    <p>
        <a href="{{ url('/reports/' . $report->id) }}" class="button">Ver Detalles del Hallazgo</a>
    </p>
@endsection

