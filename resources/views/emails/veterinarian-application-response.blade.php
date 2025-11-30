@extends('emails.layout')

@section('title', $approved ? 'Solicitud de Veterinario Aprobada' : 'Solicitud de Veterinario Rechazada')

@section('header', $approved ? 'Solicitud de Veterinario Aprobada' : 'Solicitud de Veterinario Rechazada')

@section('content')
    <h2>Estimado/a {{ $veterinarian->person->nombre ?? 'Usuario' }},</h2>
    
    @if($approved)
        <div class="info-box">
            <p><strong>¡Felicitaciones!</strong> Su solicitud para ser veterinario ha sido <strong>APROBADA</strong>.</p>
        </div>
        <p>Ahora tiene acceso a las funcionalidades de veterinario en el sistema, incluyendo la capacidad de crear y modificar hojas de vida de animales, realizar evaluaciones médicas y gestionar cuidados y alimentación.</p>
    @else
        <div class="warning-box">
            <p>Lamentamos informarle que su solicitud para ser veterinario ha sido <strong>RECHAZADA</strong>.</p>
        </div>
    @endif

    @if($veterinarian->motivo_revision)
        <div class="info-box">
            <p><strong>Motivo de la decisión:</strong></p>
            <p>{{ $veterinarian->motivo_revision }}</p>
        </div>
    @endif

    <p>Si tiene alguna pregunta o necesita más información, no dude en contactarnos.</p>
    
    <p>
        <a href="{{ url('/profile') }}" class="button">Ver Mi Perfil</a>
    </p>
@endsection

