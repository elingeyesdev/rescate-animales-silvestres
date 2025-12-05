@extends('adminlte::page')

@section('template_title')
    {{ __('Animal Files') }}
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Animal Files') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('animal-records.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                              </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <form method="GET" class="mb-3">
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label class="mb-1">{{ __('Nombre del animal') }}</label>
                                    <input type="text" name="nombre" value="{{ request('nombre') }}" class="form-control" placeholder="{{ __('Buscar por nombre') }}">
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
                                <div class="col-md-3">
                                    <label class="mb-1">{{ __('Estado') }}</label>
                                    <select name="estado_id" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        @foreach(($statuses ?? []) as $st)
                                            <option value="{{ $st->id }}" {{ (string)request('estado_id') === (string)$st->id ? 'selected' : '' }}>
                                                {{ $st->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="mb-1">{{ __('Centro') }}</label>
                                    <select name="centro_id" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        @foreach(($centers ?? []) as $c)
                                            <option value="{{ $c->id }}" {{ (string)request('centro_id') === (string)$c->id ? 'selected' : '' }}>
                                                {{ $c->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary btn-sm mr-3">{{ __('Buscar') }}</button>
                                <a href="{{ route('animal-files.index') }}" class="btn btn-link p-0">{{ __('Mostrar todos') }}</a>
                            </div>
                        </form>

                        <style>
                        .animalfile-card-img {
                            width: 100%;
                            height: 180px;
                            object-fit: cover;
                            background: #f4f6f9;
                        }
                        .animalfile-card .card-header { 
                            padding-left: 1.25rem; 
                            padding-right: 1.25rem; 
                            padding-top: 0.75rem;
                            padding-bottom: 0.75rem;
                        }
                        .animalfile-card .card-body { 
                            padding: 0.5rem 1.25rem 0.25rem 1.25rem; 
                        }
                        .animalfile-card .card-body .list-group-item {
                            border-left: 0;
                            border-right: 0;
                            padding: 0.35rem 0;
                            border-color: #dee2e6;
                        }
                        .animalfile-card .card-body .list-group-item:first-child {
                            border-top: 0;
                        }
                        .animalfile-card .card-body .list-group-item:last-child {
                            border-bottom: 0;
                            margin-bottom: 0;
                        }
                        .animalfile-card .card-footer { 
                            padding-top: 0.25rem; 
                            padding-bottom: 0.5rem; 
                            background-color: #f8f9fa;
                            margin-top: 0;
                        }
                        .animalfile-card-grid > [class*='col-'] { margin-bottom: 30px; }
                        .animalfile-card .card-footer form { display: flex; width: 100%; }
                        .animalfile-card .card-footer form > * { flex: 1 1 0; }
                        .animalfile-card .card-footer form > * + * { margin-left: .5rem; }
                        .animalfile-card .card-footer .btn-group-two {
                            display: flex;
                            width: 100%;
                            gap: 0.5rem;
                        }
                        .animalfile-card .card-footer .btn-group-two > * {
                            flex: 1 1 0;
                        }
                        </style>

                        <div class="row mt-3 animalfile-card-grid">
                            @foreach ($animalFiles as $animalFile)
                                <div class="col-md-4">
                                    <div class="card card-outline card-secondary h-100 animalfile-card">
                                        @if($animalFile->imagen_url)
                                            <img class="animalfile-card-img" src="{{ asset('storage/' . $animalFile->imagen_url) }}" alt="imagen animal">
                                        @endif
                                        <div class="card-header d-flex align-items-center">
                                            <h3 class="card-title mb-0" title="{{ $animalFile->animal?->nombre }}">
                                                <i class="fas fa-paw text-primary mr-2"></i>
                                                {{ \Illuminate\Support\Str::limit($animalFile->animal?->nombre ?? __('Sin nombre'), 26) }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-unbordered mb-0">
                                                <li class="list-group-item">
                                                    <i class="fas fa-paw text-muted mr-2"></i>
                                                    <b>{{ __('Especie:') }}</b>
                                                    <span class="float-right">{{ $animalFile->species?->nombre ?? '-' }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <i class="fas fa-heartbeat text-muted mr-2"></i>
                                                    <b>{{ __('Estado Actual:') }}</b>
                                                    <span class="float-right">
                                                        @if($animalFile->animalStatus)
                                                            <span class="badge badge-info">{{ $animalFile->animalStatus->nombre }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </span>
                                                </li>
                                                @if($animalFile->center)
                                                <li class="list-group-item">
                                                    <i class="fas fa-hospital text-muted mr-2"></i>
                                                    <b>{{ __('Centro actual:') }}</b>
                                                    <span class="float-right">{{ \Illuminate\Support\Str::limit($animalFile->center->nombre, 20) }}</span>
                                                </li>
                                                @endif
                                                
                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                            @if(Auth::user()->hasRole('admin'))
                                            <form action="{{ route('animal-files.destroy', $animalFile->id) }}" method="POST" class="mb-0 d-flex w-100">
                                                <a class="btn btn-primary btn-sm" href="{{ route('animal-files.show', $animalFile->id) }}">
                                                    <i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}
                                                </a>
                                                <a class="btn btn-success btn-sm" href="{{ route('animal-files.edit', $animalFile->id) }}">
                                                    <i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}
                                                </a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm js-confirm-delete">
                                                    <i class="fa fa-fw fa-trash"></i> {{ __('Eliminar') }}
                                                </button>
                                            </form>
                                            @else
                                            <div class="btn-group-two">
                                                <a class="btn btn-primary btn-sm" href="{{ route('animal-files.show', $animalFile->id) }}">
                                                    <i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}
                                                </a>
                                                @if(Auth::user()->hasRole('veterinario'))
                                                <a class="btn btn-success btn-sm" href="{{ route('animal-files.edit', $animalFile->id) }}">
                                                    <i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}
                                                </a>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($animalFiles->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> {{ __('No se registró ninguna hoja de animal todavía.') }}
                            </div>
                        @endif
                    </div>
                </div>
                {!! $animalFiles->withQueryString()->links() !!}
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
