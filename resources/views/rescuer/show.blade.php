@extends('adminlte::page')

@section('template_title')
    {{ $rescuer->name ?? __('Show') . ' ' . __('Rescuer') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div>
                            <span class="card-title">{{ __('Show') }} {{ __('Rescuer') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-primary btn-sm" href="{{ route('rescuers.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Persona:</strong>
                                    {{ $rescuer->person?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Motivo de postulación:</strong>
                                    {{ $rescuer->motivo_postulacion ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>CV:</strong>
                                    @if($rescuer->cv_documentado)
                                        <a href="{{ asset('storage/' . $rescuer->cv_documentado) }}" target="_blank">Ver CV</a>
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Aprobado:</strong>
                                    {{ $rescuer->aprobado === null ? '-' : ($rescuer->aprobado ? 'Sí' : 'No') }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Motivo revisión:</strong>
                                    {{ $rescuer->motivo_revision ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado de la postulación:</strong>
                                    @if($rescuer->aprobado === true)
                                        Postulación aceptada.
                                    @elseif($rescuer->aprobado === false && $rescuer->motivo_revision)
                                        Postulación no aceptada. Motivo: {{ $rescuer->motivo_revision }}
                                    @elseif($rescuer->aprobado === false)
                                        Postulación no aceptada.
                                    @elseif($rescuer->aprobado === null)
                                        Postulación en proceso de revisión.
                                    @else
                                        -
                                    @endif
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
