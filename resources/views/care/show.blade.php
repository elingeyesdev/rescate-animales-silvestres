@extends('adminlte::page')

@section('template_title')
    {{ $care->name ?? __('Show') . " " . __('Care') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} {{ __('Care') }}</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('cares.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Animal:</strong>
                                    {{ $care->animalFile?->nombre ?? '-' }}
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
                                    {{ $care->fecha ? \Carbon\Carbon::parse($care->fecha)->format('d-m-Y') : '-' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
