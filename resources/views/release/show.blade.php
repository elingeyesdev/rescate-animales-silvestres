@extends('adminlte::page')

@section('template_title')
    {{ $release->name ?? __('Show') . " " . __('Release') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Release</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('releases.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Direccion:</strong>
                                    {{ $release->direccion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Detalle:</strong>
                                    {{ $release->detalle }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Latitud:</strong>
                                    {{ $release->latitud }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Longitud:</strong>
                                    {{ $release->longitud }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Aprobada:</strong>
                                    {{ $release->aprobada }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
