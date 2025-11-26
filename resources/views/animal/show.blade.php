@extends('adminlte::page')

@section('template_title')
    {{ $animal->name ?? __('Show') . " " . __('Animal') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Animal</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('animals.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $animal->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Sexo:</strong>
                                    {{ $animal->sexo }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Descripcion:</strong>
                                    {{ $animal->descripcion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Número de reporte:</strong>
                                    {{ $animal->reporte_id ? ('#' . $animal->reporte_id) : '-' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
