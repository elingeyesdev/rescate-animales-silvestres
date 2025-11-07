@extends('adminlte::page')

@section('template_title')
    {{ $medicalEvaluation->name ?? __('Show') . " " . __('Medical Evaluation') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Medical Evaluation</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('medical-evaluations.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Tratamiento:</strong>
                                    {{ $medicalEvaluation->treatmentType?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Descripcion:</strong>
                                    {{ $medicalEvaluation->descripcion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Revisión:</strong>
                                    {{ $medicalEvaluation->fecha ? \Carbon\Carbon::parse($medicalEvaluation->fecha)->format('d-m-Y') : '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Veterinario:</strong>
                                    {{ $medicalEvaluation->veterinarian?->person?->nombre ?? '-' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
