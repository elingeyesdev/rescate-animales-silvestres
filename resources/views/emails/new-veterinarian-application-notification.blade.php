@extends('emails.layout')

@section('title', 'Nueva Solicitud de Veterinario')

@section('header', 'Nueva Solicitud de Veterinario')

@section('content')
    <h2>Se ha recibido una nueva solicitud para ser veterinario</h2>
    
    <div class="info-box">
        <p><strong>Solicitante:</strong> {{ $veterinarian->person->nombre ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $veterinarian->person->user->email ?? 'N/A' }}</p>
        <p><strong>CI:</strong> {{ $veterinarian->person->ci ?? 'N/A' }}</p>
        <p><strong>Teléfono:</strong> {{ $veterinarian->person->telefono ?? 'N/A' }}</p>
        @if($veterinarian->especialidad)
            <p><strong>Especialidad:</strong> {{ $veterinarian->especialidad }}</p>
        @endif
        @if($veterinarian->motivo_postulacion)
            <p><strong>Motivo de postulación:</strong> {{ $veterinarian->motivo_postulacion }}</p>
        @endif
        <p><strong>Fecha de solicitud:</strong> {{ $veterinarian->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <p>Por favor, revise la solicitud en el sistema para aprobar o rechazar.</p>
    
    <p>
        <a href="{{ url('/veterinarians/' . $veterinarian->id) }}" class="button">Ver Detalles de la Solicitud</a>
    </p>
@endsection

