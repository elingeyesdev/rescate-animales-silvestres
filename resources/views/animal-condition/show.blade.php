@extends('adminlte::page')

@section('template_title')
    {{ $animalCondition->name ?? __('Show') . ' ' . __('Animal Condition') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div>
                            <span class="card-title">{{ __('Show') }} {{ __('Animal Condition') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-primary btn-sm" href="{{ route('animal-conditions.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Nombre') }}:</strong>
                                    {{ $animalCondition->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Severidad') }}:</strong>
                                    {{ $animalCondition->severidad }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Activo') }}:</strong>
                                    {{ (int)$animalCondition->activo === 1 ? 'Sí' : 'No' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection


