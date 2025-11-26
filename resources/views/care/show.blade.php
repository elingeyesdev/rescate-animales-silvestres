@extends('adminlte::page')

@section('template_title')
    {{ $care->name ?? __('Show') . " " . __('Care') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div>
                            <span class="card-title">{{ __('Show') }} {{ __('Care') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-primary btn-sm" href="{{ route('cares.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Animal:</strong>
                                    {{ $care->animalFile?->animal?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo de Cuidado:</strong>
                                    {{ $care->careType?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Descripcion:</strong>
                                    {{ $care->descripcion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha:</strong>
                                    {{ $care->fecha ? \Carbon\Carbon::parse($care->fecha)->format('d/m/Y') : '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Imagen:</strong>
                                    @if(!empty($care?->imagen_url))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $care->imagen_url) }}" alt="Imagen cuidado" style="max-height:240px;">
                                        </div>
                                    @else
                                        -
                                    @endif
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
