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
                                <div class="col-md-2">
                                    <label class="mb-1">{{ __('Aprobada') }}</label>
                                    <select name="aprobada" class="form-control">
                                        <option value="">{{ __('Todas') }}</option>
                                        <option value="1" {{ request('aprobada')==='1'?'selected':'' }}>{{ __('Sí') }}</option>
                                        <option value="0" {{ request('aprobada')==='0'?'selected':'' }}>{{ __('No') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
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
                        .release-card .card-header { padding-left: 1.25rem; padding-right: 1.25rem; }
                        .release-card .card-header .card-tools { margin-left: auto; margin-right: .25rem; }
                        .release-card .card-body { padding-bottom: .75rem; }
                        .release-card .card-footer { padding-top: .5rem; padding-bottom: .5rem; }
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
                                    $imagenUrl = $animalFile?->imagen_url;
                                @endphp
                                <div class="col-md-4">
                                    <div class="card card-outline card-secondary h-100 release-card">
                                        @if($imagenUrl)
                                            <img class="release-card-img" src="{{ asset('storage/' . $imagenUrl) }}" alt="Imagen del animal liberado">
                                        @else
                                            <div class="release-card-img d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-paw fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h3 class="card-title mb-0" title="{{ $animal?->nombre }}">
                                                {{ \Illuminate\Support\Str::limit($animal?->nombre ?? __('Sin nombre'), 26) }}
                                            </h3>
                                            <div class="card-tools">
                                                <span class="badge badge-{{ (int)$release->aprobada === 1 ? 'success' : 'warning' }}">
                                                    {{ (int)$release->aprobada === 1 ? __('Aprobada') : __('Pendiente') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-1"><strong>{{ __('Especie:') }}</strong> {{ $animalFile?->species?->nombre ?? '-' }}</p>
                                            <p class="mb-1"><strong>{{ __('Estado:') }}</strong> {{ $animalFile?->animalStatus?->nombre ?? '-' }}</p>
                                            @if($release->direccion)
                                                <p class="mb-1"><strong>{{ __('Dirección:') }}</strong> {{ \Illuminate\Support\Str::limit($release->direccion, 40) }}</p>
                                            @endif
                                            @if($release->detalle)
                                                <p class="mb-1"><strong>{{ __('Detalle:') }}</strong> {{ \Illuminate\Support\Str::limit($release->detalle, 50) }}</p>
                                            @endif
                                            <p class="mb-0"><strong>{{ __('Fecha de liberación:') }}</strong> {{ optional($release->created_at)->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="card-footer">
                                            @if(Auth::user()->hasAnyRole(['admin', 'encargado']))
                                            <form action="{{ route('releases.destroy', $release->id) }}" method="POST" class="mb-0 d-flex w-100">
                                                <a class="btn btn-primary btn-sm" href="{{ route('releases.show', $release->id) }}">
                                                    <i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}
                                                </a>
                                                <a class="btn btn-success btn-sm" href="{{ route('releases.edit', $release->id) }}">
                                                    <i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}
                                                </a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm js-confirm-delete">
                                                    <i class="fa fa-fw fa-trash"></i> {{ __('Eliminar') }}
                                                </button>
                                            </form>
                                            @else
                                            <a class="btn btn-primary btn-sm w-100" href="{{ route('releases.show', $release->id) }}">
                                                <i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}
                                            </a>
                                            @endif
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
