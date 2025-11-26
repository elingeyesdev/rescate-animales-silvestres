@extends('adminlte::page')

@section('template_title')
    {{ $breed->name ?? __('Show') . " " . __('Breed') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} {{ __('Breed') }}</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('breeds.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Especie:</strong>
                                    {{ $breed->species?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $breed->nombre ?: '-' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
