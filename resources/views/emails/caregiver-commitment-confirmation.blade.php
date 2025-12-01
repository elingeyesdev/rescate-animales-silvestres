@extends('emails.layout')

@section('title', 'Confirmación de Compromiso como Cuidador Voluntario')

@section('header', 'Confirmación de Compromiso como Cuidador Voluntario')

@section('content')
    <h2>Estimado/a {{ $person->nombre ?? 'Usuario' }},</h2>
    
    <div class="info-box">
        <p>Gracias por su compromiso como <strong>cuidador voluntario</strong>. Hemos registrado su solicitud en nuestro sistema.</p>
    </div>

    @if($center)
        <div class="warning-box">
            <p><strong>IMPORTANTE:</strong> Para validar su ayuda y activar su rol de cuidador, debe acercarse físicamente al centro asignado:</p>
            <p><strong>Centro:</strong> {{ $center->nombre }}</p>
            @if($center->direccion)
                <p><strong>Dirección:</strong> {{ $center->direccion }}</p>
            @endif
            @if($center->contacto)
                <p><strong>Contacto:</strong> {{ $center->contacto }}</p>
            @endif
        </div>
    @else
        <div class="warning-box">
            <p><strong>IMPORTANTE:</strong> Para validar su ayuda y activar su rol de cuidador, debe acercarse físicamente al centro que seleccionó.</p>
        </div>
    @endif

    <p>Una vez que se acerque al centro y un administrador o encargado valide su presencia, se le asignará el rol de cuidador y podrá acceder a las funcionalidades correspondientes, incluyendo la creación de registros de cuidado de animales.</p>
    
    <p>Si tiene alguna pregunta o necesita más información, no dude en contactarnos.</p>
    
    <p>
        <a href="{{ url('/profile') }}" class="button">Ver Mi Perfil</a>
    </p>
@endsection

