@extends('adminlte::page')

@section('template_title')
    {{ __('Detalle de Historial #') . $animalHistory->id }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Detalle de Historial') }} #{{ $animalHistory->id }}</span>
                    </div>
                    <div class="card-body bg-white">
                        <div class="mb-3">
                            <strong>Hoja de Animal:</strong> #{{ $animalHistory->animal_file_id }}
                        </div>
                        <div class="mb-3">
                            <strong>Animal:</strong> {{ $animalHistory->animalFile?->animal?->nombre ?? '-' }}
                        </div>
                        <div class="mb-3">
                            <strong>Fecha de cambio:</strong> {{ $animalHistory->changed_at ?? '' }}
                        </div>
                        <div class="mb-3">
                            <strong>Observaciones:</strong>
                            @php
                                $obs = $animalHistory->observaciones;
                                $obsText = is_array($obs) ? ($obs['texto'] ?? null) : ($obs ?? null);
                            @endphp
                            <div>{{ $obsText ?: '-' }}</div>
                        </div>

                        @php
                                $vals = $animalHistory->valores_nuevos ?? [];
                                $care = $vals['care'] ?? [];
                                $cf = $vals['care_feeding'] ?? [];
                                $typeName = isset($cf['feeding_type_id']) ? (\App\Models\FeedingType::find($cf['feeding_type_id'])->nombre ?? null) : null;
                                $freqName = isset($cf['feeding_frequency_id']) ? (\App\Models\FeedingFrequency::find($cf['feeding_frequency_id'])->nombre ?? null) : null;
                                $portionObj = isset($cf['feeding_portion_id']) ? \App\Models\FeedingPortion::find($cf['feeding_portion_id']) : null;
                                $portionText = $portionObj ? ($portionObj->cantidad.' '.$portionObj->unidad) : null;
                            @endphp
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-outline card-info">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">{{ __('Cuidado registrado') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2"><span class="text-muted">{{ __('Descripción') }}:</span> {{ $care['descripcion'] ?? '-' }}</div>
                                            <div class="mb-2"><span class="text-muted">{{ __('Fecha') }}:</span> {{ $care['fecha'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-outline card-success">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">{{ __('Detalle de alimentación') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2"><span class="text-muted">{{ __('Tipo') }}:</span> {{ $typeName ?: '-' }}</div>
                                            <div class="mb-2"><span class="text-muted">{{ __('Frecuencia') }}:</span> {{ $freqName ?: '-' }}</div>
                                            <div class="mb-2"><span class="text-muted">{{ __('Porción') }}:</span> {{ $portionText ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <a href="{{ route('animal-histories.index') }}" class="btn btn-secondary mt-3">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


