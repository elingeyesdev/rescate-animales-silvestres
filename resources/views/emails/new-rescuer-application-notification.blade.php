@extends('emails.layout')

@section('title', 'Nueva Solicitud de Rescatista')

@section('header', 'Nueva Solicitud de Rescatista')

@section('content')
    <h2>Se ha recibido una nueva solicitud para ser rescatista</h2>
    
    <div class="info-box">
        <p><strong>Solicitante:</strong> {{ $rescuer->person->nombre ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $rescuer->person->user->email ?? 'N/A' }}</p>
        <p><strong>CI:</strong> {{ $rescuer->person->ci ?? 'N/A' }}</p>
        <p><strong>Teléfono:</strong> {{ $rescuer->person->telefono ?? 'N/A' }}</p>
        @if($rescuer->motivo_postulacion)
            <p><strong>Motivo de postulación:</strong> {{ $rescuer->motivo_postulacion }}</p>
        @endif
        <p><strong>Fecha de solicitud:</strong> {{ $rescuer->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <p>Por favor, revise la solicitud en el sistema para aprobar o rechazar.</p>
    
    <p>
        <a href="{{ url('/rescuers/' . $rescuer->id) }}" class="button">Ver Detalles de la Solicitud</a>
    </p>
@endsection

