@extends('adminlte::page')

@section('template_title')
    {{ $medicalEvaluation->name ?? __('Show') . ' ' . __('Medical Evaluation') }}
@endsection

@section('content')
    <section class="content container-fluid mt-3">
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
                                    <strong>Estado de salud anterior:</strong>
                                    @php($prev = \App\Models\AnimalHistory::where('animal_file_id', $medicalEvaluation->animal_file_id ?? null)
                                        ->whereNotNull('valores_nuevos')
                                        ->whereRaw("(valores_nuevos->'evaluacion_medica'->>'id')::text = ?", [(string)($medicalEvaluation->id ?? '')])
                                        ->first())
                                    {{ $prev?->valores_antiguos['estado']['nombre'] ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Veterinario:</strong>
                                    {{ $medicalEvaluation->veterinarian?->person?->nombre ?? '-' }}
                                    @if($medicalEvaluation->veterinarian?->especialidad)
                                        <span class="text-muted"> ({{ $medicalEvaluation->veterinarian->especialidad }})</span>
                                    @endif
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Imagen de evaluación:</strong>
                                    @if(!empty($medicalEvaluation?->imagen_url))
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $medicalEvaluation->imagen_url) }}" target="_blank" rel="noopener">
                                                <img src="{{ asset('storage/' . $medicalEvaluation->imagen_url) }}" alt="Imagen evaluación" style="max-height:240px;">
                                            </a>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Imagen de llegada:</strong>
                                    @php($foundImg = $medicalEvaluation->animalFile?->animal?->report?->imagen_url ?? null)
                                    @if($foundImg)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $foundImg) }}" target="_blank" rel="noopener">
                                                <img src="{{ asset('storage/' . $foundImg) }}" alt="Imagen de llegada" style="max-height:240px;">
                                            </a>
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
