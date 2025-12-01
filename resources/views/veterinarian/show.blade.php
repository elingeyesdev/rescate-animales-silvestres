@extends('adminlte::page')

@section('template_title')
    {{ $veterinarian->name ?? __('Show') . " " . __('Veterinarian') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <div>
                            <span class="card-title">{{ __('Show') }} {{ __('Veterinarian') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-primary btn-sm" href="{{ route('veterinarians.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Persona:</strong>
                                    {{ $veterinarian->person?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Especialidad:</strong>
                                    {{ $veterinarian->especialidad ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Motivo de postulación:</strong>
                                    {{ $veterinarian->motivo_postulacion ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>CV:</strong>
                                    @if($veterinarian->cv_documentado)
                                        <a href="{{ asset('storage/' . $veterinarian->cv_documentado) }}" target="_blank">Ver CV</a>
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Aprobado:</strong>
                                    {{ $veterinarian->aprobado === null ? '-' : ($veterinarian->aprobado ? 'Sí' : 'No') }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Motivo revisión:</strong>
                                    {{ $veterinarian->motivo_revision ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado de la postulación:</strong>
                                    @if($veterinarian->aprobado === true)
                                        Postulación aceptada.
                                    @elseif($veterinarian->aprobado === false && $veterinarian->motivo_revision)
                                        Postulación no aceptada. Motivo: {{ $veterinarian->motivo_revision }}
                                    @elseif($veterinarian->aprobado === false)
                                        Postulación no aceptada.
                                    @elseif($veterinarian->aprobado === null)
                                        Postulación en proceso de revisión.
                                    @else
                                        -
                                    @endif
                                </div>

                                @if($veterinarian->aprobado === null && Auth::user()->hasAnyRole(['admin', 'encargado']))
                                <div class="form-group mb-2 mb20">
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalAprobarVeterinarian">
                                        <i class="fa fa-check-circle"></i> Aprobar/Rechazar Solicitud
                                    </button>
                                </div>
                                @endif

                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($veterinarian->aprobado === null && Auth::user()->hasAnyRole(['admin', 'encargado']))
    {{-- Modal para aprobar/rechazar solicitud de veterinario --}}
    <div class="modal fade" id="modalAprobarVeterinarian" tabindex="-1" role="dialog" aria-labelledby="modalAprobarVeterinarianLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAprobarVeterinarianLabel">
                        <i class="fa fa-user-check"></i> {{ __('Revisar Solicitud de Veterinario') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('veterinarians.approve', $veterinarian->id) }}" method="POST" id="formAprobarVeterinarian">
                    @method('PUT')
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">{{ __('¿Desea aprobar o rechazar esta solicitud de veterinario?') }}</p>
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
                        <input type="hidden" name="action" id="actionVeterinarian" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="btnRechazarVeterinarian">
                            <i class="fa fa-times-circle"></i> {{ __('Rechazar') }}
                        </button>
                        <button type="button" class="btn btn-success" id="btnAprobarVeterinarian">
                            <i class="fa fa-check-circle"></i> {{ __('Aprobar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.getElementById('formAprobarVeterinarian');
        var actionInput = document.getElementById('actionVeterinarian');
        var motivoInput = document.getElementById('motivo_revision');
        var btnRechazar = document.getElementById('btnRechazarVeterinarian');
        var btnAprobar = document.getElementById('btnAprobarVeterinarian');
        
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
