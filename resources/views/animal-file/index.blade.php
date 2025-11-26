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
                        <style>
                        .animalfile-card-img {
                            width: 100%;
                            height: 180px;
                            object-fit: cover;
                            background: #f4f6f9;
                        }
                        .animalfile-card .card-header { padding-left: 1.25rem; padding-right: 1.25rem; }
                        .animalfile-card .card-body { padding-bottom: .75rem; }
                        .animalfile-card .card-footer { padding-top: .5rem; padding-bottom: .5rem; }
                        .animalfile-card-grid > [class*='col-'] { margin-bottom: 30px; }
                        .animalfile-card .card-footer form { display: flex; width: 100%; }
                        .animalfile-card .card-footer form > * { flex: 1 1 0; }
                        .animalfile-card .card-footer form > * + * { margin-left: .5rem; }
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
                                                {{ \Illuminate\Support\Str::limit($animalFile->animal?->nombre ?? __('Sin nombre'), 26) }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-1"><strong>{{ __('Especie:') }}</strong> {{ $animalFile->species?->nombre ?? '-' }}</p>
                                            <p class="mb-1"><strong>{{ __('Estado Actual:') }}</strong> {{ $animalFile->animalStatus?->nombre ?? '-' }}</p>
                                            @if($animalFile->center)
                                                <p class="mb-1">
                                                    <strong>{{ __('Centro actual:') }}</strong>
                                                    {{$animalFile->center->nombre }}
                                                </p>
                                            @endif
                                            <p class="mb-0"><strong>{{ __('Sexo:') }}</strong> {{ $animalFile->animal?->sexo ?? '-' }}</p>
                                        </div>
                                        <div class="card-footer">
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
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                {!! $animalFiles->withQueryString()->links() !!}
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
