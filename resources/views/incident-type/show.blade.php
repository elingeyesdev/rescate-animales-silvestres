@extends('adminlte::page')

@section('template_title')
    {{ $incidentType->name ?? __('Show') . ' ' . __('Incident Type') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div>
                            <span class="card-title">{{ __('Show') }} {{ __('Incident Type') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-primary btn-sm" href="{{ route('incident-types.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Nombre') }}:</strong>
                                    {{ $incidentType->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Riesgo') }}:</strong>
                                    @php($map = ['Bajo','Medio','Alto'])
                                    {{ $map[(int)($incidentType->riesgo ?? 0)] ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Activo') }}:</strong>
                                    {{ (int)$incidentType->activo === 1 ? 'Sí' : 'No' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection


