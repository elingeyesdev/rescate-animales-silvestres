@extends('adminlte::page')

@section('template_title')
    {{ $medicalEvaluation->name ?? __('Show') . ' ' . __('Medical Evaluation') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div>
                            <span class="card-title">{{ __('Show') }} {{ __('Medical Evaluation') }}</span>
                        </div>
                        <div class="ml-auto">
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
                                <div class="form-group mb-2 mb20">
                                    <strong>Animal:</strong>
                                    {{ $medicalEvaluation->animalFile?->animal?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Imagen:</strong>
                                    @if(!empty($medicalEvaluation?->imagen_url))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $medicalEvaluation->imagen_url) }}" alt="Imagen evaluación" style="max-height:240px;">
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
@endsection
