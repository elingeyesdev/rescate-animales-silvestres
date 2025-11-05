@extends('adminlte::page')

@section('template_title')
    {{ $healthRecord->name ?? __('Show') . " " . __('Health Record') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Health Record</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('health-records.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo:</strong>
                                    {{ $healthRecord->tipo }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Descripcion:</strong>
                                    {{ $healthRecord->descripcion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tratamiento:</strong>
                                    {{ $healthRecord->tratamiento }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Revision:</strong>
                                    {{ $healthRecord->fecha_revision }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
