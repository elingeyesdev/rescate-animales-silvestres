@extends('adminlte::page')

@section('template_title')
    {{ $animalFile->name ?? __('Show') . ' ' . __('Animal File') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div>
                            <span class="card-title">{{ __('Show') }} {{ __('Animal File') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-primary btn-sm" href="{{ route('animal-files.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $animalFile->animal?->nombre ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Sexo:</strong>
                                    {{ $animalFile->animal?->sexo ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo:</strong>
                                    {{ $animalFile->animalType?->nombre ?? '-' }}
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
                                    <strong>Estado:</strong>
                                    {{ $animalFile->animalStatus?->nombre ?? '-' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
