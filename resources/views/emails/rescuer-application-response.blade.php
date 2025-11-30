@extends('emails.layout')

@section('title', $approved ? 'Solicitud de Rescatista Aprobada' : 'Solicitud de Rescatista Rechazada')

@section('header', $approved ? 'Solicitud de Rescatista Aprobada' : 'Solicitud de Rescatista Rechazada')

@section('content')
    <h2>Estimado/a {{ $rescuer->person->nombre ?? 'Usuario' }},</h2>
    
    @if($approved)
        <div class="info-box">
            <p><strong>¡Felicitaciones!</strong> Su solicitud para ser rescatista ha sido <strong>APROBADA</strong>.</p>
        </div>
        <p>Ahora tiene acceso a las funcionalidades de rescatista en el sistema, incluyendo la capacidad de crear y gestionar traslados de animales.</p>
    @else
        <div class="warning-box">
            <p>Lamentamos informarle que su solicitud para ser rescatista ha sido <strong>RECHAZADA</strong>.</p>
        </div>
    @endif

    @if($rescuer->motivo_revision)
        <div class="info-box">
            <p><strong>Motivo de la decisión:</strong></p>
            <p>{{ $rescuer->motivo_revision }}</p>
        </div>
    @endif

    <p>Si tiene alguna pregunta o necesita más información, no dude en contactarnos.</p>
    
    <p>
        <a href="{{ url('/profile') }}" class="button">Ver Mi Perfil</a>
    </p>
@endsection

