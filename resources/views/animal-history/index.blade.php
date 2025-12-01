@extends('adminlte::page')

@section('template_title')
    {{ __('Historial de Animales') }}
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Historial de Animales') }}
                            </span>
                            <form method="get" class="form-inline">
                                <label for="order" class="mr-2">{{ __('Orden') }}</label>
                                <select name="order" id="order" class="form-control" onchange="this.form.submit()">
                                    @php $ord = request()->get('order'); @endphp
                                    <option value="desc" {{ $ord!=='asc'?'selected':'' }}>{{ __('Más nuevo primero') }}</option>
                                    <option value="asc" {{ $ord==='asc'?'selected':'' }}>{{ __('Más viejo primero') }}</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <style>
                        .history-card-img {
                            width: 100%;
                            height: 180px;
                            object-fit: cover;
                            background: #f4f6f9;
                        }
                        .history-card .card-header { 
                            padding-left: 1.25rem; 
                            padding-right: 1.25rem; 
                            padding-top: 0.75rem;
                            padding-bottom: 0.75rem;
                        }
                        .history-card .card-body { 
                            padding: 0.5rem 1.25rem 0.25rem 1.25rem; 
                        }
                        .history-card .card-body .list-group-item {
                            border-left: 0;
                            border-right: 0;
                            padding: 0.35rem 0;
                            border-color: #dee2e6;
                        }
                        .history-card .card-body .list-group-item:first-child {
                            border-top: 0;
                        }
                        .history-card .card-body .list-group-item:last-child {
                            border-bottom: 0;
                            margin-bottom: 0;
                        }
                        .history-card .card-footer { 
                            padding-top: 0.5rem; 
                            padding-bottom: 0.5rem; 
                            background-color: #f8f9fa;
                            margin-top: 0;
                        }
                        .history-card-grid > [class*='col-'] { margin-bottom: 30px; }
                        </style>

                        <div class="row mt-3 history-card-grid">
                            @foreach ($histories as $h)
                                @php
                                    $animalFile = $h->animalFile;
                                    $animal = $animalFile?->animal;
                                    $imagenUrl = $animalFile?->imagen_url ?? $animal?->report?->imagen_url ?? null;
                                    $nombre = $animal?->nombre ?? __('Sin nombre');
                                    $fechaCambio = $h->changed_at ? \Carbon\Carbon::parse($h->changed_at)->format('d/m/Y H:i') : '-';
                                    $desc = data_get($h->valores_nuevos, 'care.descripcion');
                                    $obsText = is_array($h->observaciones ?? null) ? ($h->observaciones['texto'] ?? null) : ($h->observaciones ?? null);
                                    $resumen = $desc ? \Illuminate\Support\Str::limit($desc, 60) : ($obsText ? \Illuminate\Support\Str::limit($obsText, 60) : '-');
                                @endphp
                                <div class="col-md-4">
                                    <div class="card card-outline card-secondary h-100 history-card">
                                        @if($imagenUrl)
                                            <img class="history-card-img" src="{{ asset('storage/' . $imagenUrl) }}" alt="Imagen del animal">
                                        @else
                                            <div class="history-card-img d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-history fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="card-header d-flex align-items-center">
                                            <h3 class="card-title mb-0" title="{{ $nombre }}">
                                                <i class="fas fa-history text-primary mr-2"></i>
                                                {{ \Illuminate\Support\Str::limit($nombre, 26) }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-unbordered mb-0">
                                                <li class="list-group-item">
                                                    <i class="fas fa-calendar-alt text-muted mr-2"></i>
                                                    <b>{{ __('Fecha de cambio:') }}</b>
                                                    <span class="float-right">{{ $fechaCambio }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <i class="fas fa-info-circle text-muted mr-2"></i>
                                                    <b>{{ __('Resumen:') }}</b>
                                                    <span class="float-right text-right" style="max-width: 60%;">
                                                        {{ $resumen }}
                                                    </span>
                                                </li>
                                                @if($animalFile?->species)
                                                <li class="list-group-item">
                                                    <i class="fas fa-paw text-muted mr-2"></i>
                                                    <b>{{ __('Especie:') }}</b>
                                                    <span class="float-right">{{ $animalFile->species->nombre }}</span>
                                                </li>
                                                @endif
                                                @if($animalFile?->animalStatus)
                                                <li class="list-group-item">
                                                    <i class="fas fa-heartbeat text-muted mr-2"></i>
                                                    <b>{{ __('Estado:') }}</b>
                                                    <span class="float-right">
                                                        <span class="badge badge-info">{{ $animalFile->animalStatus->nombre }}</span>
                                                    </span>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                            <a class="btn btn-primary btn-sm w-100" href="{{ route('animal-histories.show', $h->id) }}">
                                                <i class="fa fa-fw fa-eye"></i> {{ __('Ver Historial') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($histories->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> {{ __('No se encontraron registros en el historial de animales.') }}
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        {!! $histories->withQueryString()->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection

