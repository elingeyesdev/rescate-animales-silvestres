@extends('adminlte::page')


@section('template_title')
    {{ $animalProfile->name ?? __('Show') . " " . __('Animal Profile') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Animal Profile</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('animal-profiles.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado Salud:</strong>
                                    {{ $animalProfile->estado_salud }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Sexo:</strong>
                                    {{ $animalProfile->sexo }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Especie:</strong>
                                    {{ $animalProfile->especie }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Raza:</strong>
                                    {{ $animalProfile->raza }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Alimentacion:</strong>
                                    {{ $animalProfile->alimentacion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Frecuencia:</strong>
                                    {{ $animalProfile->frecuencia }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cantidad:</strong>
                                    {{ $animalProfile->cantidad }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Color:</strong>
                                    {{ $animalProfile->color }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Imagen:</strong>
                                    {{ $animalProfile->imagen }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Reporte Id:</strong>
                                    {{ $animalProfile->reporte_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Detalle:</strong>
                                    {{ $animalProfile->detalle }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
