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
                                    {{ $animalFile->nombre ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Sexo:</strong>
                                    {{ $animalFile->sexo ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo:</strong>
                                    {{ $animalFile->animalType?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Reporte:</strong>
                                    {{ $animalFile->reporte_id ? '#' . $animalFile->reporte_id : '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Especie:</strong>
                                    {{ $animalFile->species?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Imagen:</strong>
                                    @if($animalFile->imagen_url)
                                        <div><img src="{{ asset('storage/' . $animalFile->imagen_url) }}" alt="img" style="max-height:180px;"></div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Raza:</strong>
                                    {{ $animalFile->breed?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $animalFile->animalStatus?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Adopción:</strong>
                                    {{ $animalFile->adopcion_id ? '#' . $animalFile->adopcion_id : '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Liberación:</strong>
                                    {{ $animalFile->liberacion_id ? '#' . $animalFile->liberacion_id : '-' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
