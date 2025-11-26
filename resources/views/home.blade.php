@extends('adminlte::page')

@section('content')
<div class="container page-pad">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Bienvenido al Sistema de Rescate de Animales') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('Este es el panel de control del sistema de rescate de animales.') }}
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.page-pad')
@endsection
