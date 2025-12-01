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

                                @if($rescuer->aprobado === null && Auth::user()->hasAnyRole(['admin', 'encargado']))
                                <div class="form-group mb-2 mb20">
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalAprobarRescuer">
                                        <i class="fa fa-check-circle"></i> Aprobar/Rechazar Solicitud
                                    </button>
                                </div>
                                @endif

                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($rescuer->aprobado === null && Auth::user()->hasAnyRole(['admin', 'encargado']))
    {{-- Modal para aprobar/rechazar solicitud de rescatista --}}
    <div class="modal fade" id="modalAprobarRescuer" tabindex="-1" role="dialog" aria-labelledby="modalAprobarRescuerLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAprobarRescuerLabel">
                        <i class="fa fa-user-check"></i> {{ __('Revisar Solicitud de Rescatista') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('rescuers.approve', $rescuer->id) }}" method="POST" id="formAprobarRescuer">
                    @method('PUT')
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">{{ __('¿Desea aprobar o rechazar esta solicitud de rescatista?') }}</p>
                        <div class="form-group">
                            <label for="motivo_revision">{{ __('Motivo de revisión') }} <span class="text-danger">*</span></label>
                            <textarea 
                                class="form-control" 
                                id="motivo_revision" 
                                name="motivo_revision" 
                                rows="3" 
                                required 
                                minlength="3"
                                placeholder="{{ __('Ingrese el motivo de la aprobación o rechazo...') }}"></textarea>
                            <small class="form-text text-muted">{{ __('Mínimo 3 caracteres') }}</small>
                        </div>
                        <input type="hidden" name="action" id="actionRescuer" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="btnRechazarRescuer">
                            <i class="fa fa-times-circle"></i> {{ __('Rechazar') }}
                        </button>
                        <button type="button" class="btn btn-success" id="btnAprobarRescuer">
                            <i class="fa fa-check-circle"></i> {{ __('Aprobar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.getElementById('formAprobarRescuer');
        var actionInput = document.getElementById('actionRescuer');
        var motivoInput = document.getElementById('motivo_revision');
        var btnRechazar = document.getElementById('btnRechazarRescuer');
        var btnAprobar = document.getElementById('btnAprobarRescuer');
        
        function submitForm(action) {
            // Validar que el motivo esté lleno
            if (!motivoInput.value || motivoInput.value.trim().length < 3) {
                alert('{{ __('Por favor, ingrese un motivo de revisión (mínimo 3 caracteres).') }}');
                motivoInput.focus();
                return false;
            }
            
            // Establecer el valor de action
            if (actionInput) {
                actionInput.value = action;
            }
            
            // Deshabilitar botones para evitar doble envío
            if (btnRechazar) btnRechazar.disabled = true;
            if (btnAprobar) btnAprobar.disabled = true;
            
            // Enviar formulario
            if (form) {
                form.submit();
            }
            return true;
        }
        
        if (btnRechazar) {
            btnRechazar.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                submitForm('reject');
            });
        }
        
        if (btnAprobar) {
            btnAprobar.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                submitForm('approve');
            });
        }
    });
    </script>
    @endif

    @include('partials.page-pad')
@endsection
