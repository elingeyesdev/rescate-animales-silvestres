@extends('adminlte::page')

@section('template_title')
    {{ $veterinarian->name ?? __('Show') . " " . __('Veterinarian') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Veterinarian</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('veterinarians.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Especialidad:</strong>
                                    {{ $veterinarian->especialidad }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cv Documentado:</strong>
                                    {{ $veterinarian->cv_documentado }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Persona Id:</strong>
                                    {{ $veterinarian->persona_id }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
