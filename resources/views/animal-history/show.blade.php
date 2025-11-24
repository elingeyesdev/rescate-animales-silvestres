@extends('adminlte::page')

@section('template_title')
    {{ __('Historial de Hoja #') . $animalHistory->animal_file_id }}
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
                        <div class="timeline">
                            @foreach(($timeline ?? []) as $t)
                                <div class="card card-outline card-info mb-3">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title mb-0">{{ $t['title'] }}</h5>
                                            <small class="text-muted">{{ $t['changed_at_label'] ?? $t['changed_at'] }}</small>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                @forelse(($t['details'] ?? []) as $d)
                                                    <div class="mb-2">
                                                        <span class="text-muted">{{ $d['label'] }}:</span>
                                                        <span>{{ $d['value'] }}</span>
                                                    </div>
                                                @empty
                                                    <div class="text-muted">{{ __('Sin detalles') }}</div>
                                                @endforelse
                                            </div>
                                            <div class="col-md-4 text-right">
                                                @if(!empty($t['image_url']))
                                                    <img src="{{ asset('storage/' . $t['image_url']) }}" alt="Imagen" style="max-height: 140px; width: auto;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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


