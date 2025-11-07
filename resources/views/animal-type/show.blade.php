@extends('adminlte::page')

@section('template_title')
    {{ $animalType->name ?? __('Show') . " " . __('Animal Type') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Animal Type</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('animal-types.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $animalType->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Permite Adopcion:</strong>
                                    {{ $animalType->permite_adopcion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Permite Liberacion:</strong>
                                    {{ $animalType->permite_liberacion }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
