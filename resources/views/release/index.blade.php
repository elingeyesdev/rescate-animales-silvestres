@extends('adminlte::page')

@section('template_title')
    {{ __('Liberaciones') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">{{ __('Liberaciones') }}</span>
                            @if(Auth::user()->hasAnyRole(['admin', 'encargado']))
                            <div class="float-right">
                                <a href="{{ route('releases.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('Crear nueva') }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <form method="GET" class="mb-3 js-auto-filter-form">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <label class="mb-1">{{ __('Nombre del animal') }}</label>
                                    <input type="text" name="nombre_animal" value="{{ request('nombre_animal') }}" class="form-control" placeholder="{{ __('Buscar por nombre') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-1">{{ __('Especie') }}</label>
                                    <select name="especie_id" class="form-control">
                                        <option value="">{{ __('Todas') }}</option>
                                        @foreach(($species ?? []) as $s)
                                            <option value="{{ $s->id }}" {{ (string)request('especie_id') === (string)$s->id ? 'selected' : '' }}>
                                                {{ $s->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="mb-1">{{ __('Fecha desde') }}</label>
                                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="mb-1">{{ __('Fecha hasta') }}</label>
                                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control">
                                </div>
                            </div>
                            <div class="mt-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary btn-sm mr-3">{{ __('Buscar') }}</button>
                                <a href="{{ route('releases.index') }}" class="btn btn-link p-0">{{ __('Mostrar todos') }}</a>
                            </div>
                        </form>

                        <style>
                        .release-card-img {
                            width: 100%;
                            height: 180px;
                            object-fit: cover;
                            background: #f4f6f9;
                        }
                        .release-card .card-header { 
                            padding-left: 1.25rem; 
                            padding-right: 1.25rem; 
                            padding-top: 0.75rem;
                            padding-bottom: 0.75rem;
                        }
                        .release-card .card-body { 
                            padding: 0.5rem 1.25rem 0.25rem 1.25rem; 
                        }
                        .release-card .card-body .list-group-item {
                            border-left: 0;
                            border-right: 0;
                            padding: 0.35rem 0;
                            border-color: #dee2e6;
                        }
                        .release-card .card-body .list-group-item:first-child {
                            border-top: 0;
                        }
                        .release-card .card-body .list-group-item:last-child {
                            border-bottom: 0;
                            margin-bottom: 0;
                        }
                        .release-card .card-footer { 
                            padding-top: 0.25rem; 
                            padding-bottom: 0.5rem; 
                            background-color: #f8f9fa;
                            margin-top: 0;
                        }
                        .release-card-grid > [class*='col-'] { margin-bottom: 30px; }
                        .release-card .card-footer form { display: flex; width: 100%; }
                        .release-card .card-footer form > * { flex: 1 1 0; }
                        .release-card .card-footer form > * + * { margin-left: .5rem; }
                        </style>

                        <div class="row mt-3 release-card-grid">
                            @foreach ($releases as $release)
                                @php
                                    $animalFile = $release->animalFile;
                                    $animal = $animalFile?->animal;
                                    $imagenUrl = $release->imagen_url ?? $animalFile?->imagen_url;
                                @endphp
                                <div class="col-md-4">
                                    <div class="card card-outline card-secondary h-100 release-card">
                                        @if($imagenUrl)
                                            <img class="release-card-img" src="{{ asset('storage/' . $imagenUrl) }}" alt="Imagen del animal liberado">
                                        @else
                                            <div class="release-card-img d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-dove fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="card-header d-flex align-items-center">
                                            <h3 class="card-title mb-0" title="{{ $animal?->nombre }}">
                                                <i class="fas fa-dove text-primary mr-2"></i>
                                                {{ \Illuminate\Support\Str::limit($animal?->nombre ?? __('Sin nombre'), 26) }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-unbordered mb-0">
                                                <li class="list-group-item">
                                                    <i class="fas fa-paw text-muted mr-2"></i>
                                                    <b>{{ __('Especie:') }}</b>
                                                    <span class="float-right">{{ $animalFile?->species?->nombre ?? '-' }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <i class="fas fa-heartbeat text-muted mr-2"></i>
                                                    <b>{{ __('Estado:') }}</b>
                                                    <span class="float-right">
                                                        @if($animalFile?->animalStatus)
                                                            <span class="badge badge-info">{{ $animalFile->animalStatus->nombre }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </span>
                                                </li>
                                                
                                                <li class="list-group-item">
                                                    <i class="fas fa-calendar-alt text-muted mr-2"></i>
                                                    <b>{{ __('Fecha de liberación:') }}</b>
                                                    <span class="float-right">{{ optional($release->created_at)->format('d/m/Y') }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                            <a class="btn btn-primary btn-sm w-100" href="{{ route('releases.show', $release->id) }}">
                                                <i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($releases->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> {{ __('No se encontraron liberaciones con los filtros seleccionados.') }}
                            </div>
                        @endif
                    </div>
                </div>
                {!! $releases->withQueryString()->links() !!}
            </div>
        </div>
    </section>
    @include('partials.page-pad')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.querySelector('form.js-auto-filter-form');
        if (form) {
            var applyBtn = form.querySelector('button[type="submit"]');
            applyBtn && applyBtn.addEventListener('click', function(){ /* submit explicit */ });
        }
    });
    </script>
@endsection
