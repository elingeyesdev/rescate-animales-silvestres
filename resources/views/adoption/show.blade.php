@extends('adminlte::page')

@section('template_title')
    {{ $adoption->name ?? __('Show') . " " . __('Adoption') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Adoption</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('adoptions.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Direccion:</strong>
                                    {{ $adoption->direccion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Latitud:</strong>
                                    {{ $adoption->latitud }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Longitud:</strong>
                                    {{ $adoption->longitud }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Detalle:</strong>
                                    {{ $adoption->detalle }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Aprobada:</strong>
                                    {{ (int)$adoption->aprobada === 1 ? 'Sí' : 'No' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Adoptante:</strong>
                                    {{ $adoption->adopter?->nombre ?? '-' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
