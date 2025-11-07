@extends('adminlte::page')

@section('template_title')
    {{ $animalFile->name ?? __('Show') . " " . __('Animal File') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Animal File</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('animal-files.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $animalFile->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo:</strong>
                                    {{ $animalFile->tipo }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo Id:</strong>
                                    {{ $animalFile->tipo_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Reporte Id:</strong>
                                    {{ $animalFile->reporte_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Especie Id:</strong>
                                    {{ $animalFile->especie_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Raza Id:</strong>
                                    {{ $animalFile->raza_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado Id:</strong>
                                    {{ $animalFile->estado_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Adopcion Id:</strong>
                                    {{ $animalFile->adopcion_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Liberacion Id:</strong>
                                    {{ $animalFile->liberacion_id }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
