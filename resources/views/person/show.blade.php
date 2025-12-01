@extends('adminlte::page')

@section('template_title')
    {{ $person->nombre ?? __('Show') . ' ' . __('Person') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} {{ __('Person') }}</span>
                        </div>
                        <div class="float-right">
                            @if($isAdmin)
                                <a class="btn btn-success btn-sm mr-2" href="{{ route('people.edit', $person->id) }}">
                                    <i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}
                                </a>
                            @endif
                            <a class="btn btn-primary btn-sm" href="{{ route('people.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>Email:</strong>
                                    {{ $person->user?->email ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $person->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>CI:</strong>
                                    {{ $person->ci ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Teléfono:</strong>
                                    {{ $person->telefono ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Es Cuidador:</strong>
                                    {{ (int)$person->es_cuidador === 1 ? 'Sí' : 'No' }}
                                    @if((int)$person->es_cuidador === 1)
                                        @if(!empty($person->cuidador_motivo_revision))
                                            <span class="badge badge-success ml-2">Aprobado</span>
                                        @else
                                            <span class="badge badge-warning ml-2">Pendiente de aprobación</span>
                                        @endif
                                    @endif
                                </div>
                                @if((int)$person->es_cuidador === 1)
                                    @if($person->cuidador_center_id)
                                        <div class="form-group mb-2 mb20">
                                            <strong>Centro asignado:</strong>
                                            {{ $person->cuidadorCenter?->nombre ?? '-' }}
                                        </div>
                                    @endif
                                    @if(!empty($person->cuidador_motivo_revision))
                                        <div class="form-group mb-2 mb20">
                                            <strong>Motivo de revisión:</strong>
                                            {{ $person->cuidador_motivo_revision }}
                                        </div>
                                    @endif
                                @endif
                                <div class="form-group mb-2 mb20">
                                    <strong>Rol principal:</strong>
                                    {{ $person->highest_role }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Roles asignados:</strong>
                                    @if($person->user && method_exists($person->user, 'getRoleNames'))
                                        @php
                                            $roles = $person->user->getRoleNames();
                                        @endphp
                                        @if($roles->isNotEmpty())
                                            <div class="mt-2">
                                                @foreach($roles as $role)
                                                    <span class="badge badge-primary mr-1">{{ ucfirst($role) }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">Sin roles asignados</span>
                                        @endif
                                    @else
                                        <span class="text-muted">No disponible</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>Foto de perfil:</strong><br>
                                    @php
                                        $fotoUrl = !empty($person->foto_path)
                                            ? asset('storage/' . $person->foto_path)
                                            : asset('storage/personas/persona.png');
                                    @endphp
                                    <img src="{{ $fotoUrl }}" 
                                         alt="Foto de {{ $person->nombre }}"
                                         class="img-circle img-fluid"
                                         style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                        </div>

                        {{-- Información de roles (ocultar si la persona es admin) --}}
                        @if(!$personIsAdmin && ($hasRescuer || $hasVeterinarian))
                            <hr>
                            <h5>Información de roles</h5>
                            @if($hasRescuer)
                                <div class="form-group mb-2 mb20">
                                    <strong>Rescatista:</strong>
                                    @php
                                        $rescuer = $person->rescuers->first();
                                    @endphp
                                    @if($rescuer)
                                        @if($rescuer->aprobado === true)
                                            <span class="badge badge-success">Aprobado</span>
                                        @elseif($rescuer->aprobado === false)
                                            <span class="badge badge-danger">Rechazado</span>
                                        @else
                                            <span class="badge badge-warning">En revisión</span>
                                        @endif
                                        @if($rescuer->motivo_revision)
                                            <br><small class="text-muted">Motivo: {{ $rescuer->motivo_revision }}</small>
                                        @endif
                                    @endif
                                </div>
                            @endif
                            @if($hasVeterinarian)
                                <div class="form-group mb-2 mb20">
                                    <strong>Veterinario:</strong>
                                    @php
                                        $veterinarian = $person->veterinarians->first();
                                    @endphp
                                    @if($veterinarian)
                                        @if($veterinarian->aprobado === true)
                                            <span class="badge badge-success">Aprobado</span>
                                        @elseif($veterinarian->aprobado === false)
                                            <span class="badge badge-danger">Rechazado</span>
                                        @else
                                            <span class="badge badge-warning">En revisión</span>
                                        @endif
                                        @if($veterinarian->especialidad)
                                            <br><small class="text-muted">Especialidad: {{ $veterinarian->especialidad }}</small>
                                        @endif
                                        @if($veterinarian->motivo_revision)
                                            <br><small class="text-muted">Motivo: {{ $veterinarian->motivo_revision }}</small>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        @endif

                        {{-- Botones de asignación (solo para admin/encargado y solo si la persona NO es admin) --}}
                        @if(($isAdmin || (isset($isEncargado) && $isEncargado)) && !$personIsAdmin)
                            <hr>
                            <h5>Asignar roles</h5>
                            <div class="form-group mb-2">
                                @php
                                    $rescuer = $hasRescuer ? $person->rescuers->first() : null;
                                    $veterinarian = $hasVeterinarian ? $person->veterinarians->first() : null;
                                    $rescuerEnRevision = $rescuer && $rescuer->aprobado === null;
                                    $veterinarianEnRevision = $veterinarian && $veterinarian->aprobado === null;
                                @endphp
                                
                                @if(!$hasRescuer)
                                    <a class="btn btn-outline-info btn-sm" 
                                       href="{{ route('rescuers.create', ['persona_id' => $person->id]) }}">
                                        <i class="fa fa-user-injured"></i> Asignar como rescatista
                                    </a>
                                @elseif($rescuerEnRevision)
                                    <a class="btn btn-outline-warning btn-sm" 
                                       href="{{ route('rescuers.show', $rescuer->id) }}">
                                        <i class="fa fa-user-injured"></i> Ver Solicitud de Rescatista
                                    </a>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        <i class="fa fa-user-injured"></i> Ya es rescatista
                                    </button>
                                @endif
                                
                                @if(!$hasVeterinarian)
                                    <a class="btn btn-outline-info btn-sm" 
                                       href="{{ route('veterinarians.create', ['persona_id' => $person->id]) }}">
                                        <i class="fa fa-user-md"></i> Asignar como veterinario
                                    </a>
                                @elseif($veterinarianEnRevision)
                                    <a class="btn btn-outline-warning btn-sm" 
                                       href="{{ route('veterinarians.show', $veterinarian->id) }}">
                                        <i class="fa fa-user-md"></i> Ver Solicitud de Veterinario
                                    </a>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        <i class="fa fa-user-md"></i> Ya es veterinario
                                    </button>
                                @endif

                                {{-- Botón para aprobar/rechazar solicitud de cuidador --}}
                                @if(isset($cuidadorPendiente) && $cuidadorPendiente && isset($canApproveCuidador) && $canApproveCuidador)
                                    <button type="button" class="btn btn-outline-warning btn-sm" data-toggle="modal" data-target="#modalCuidadorAprobacion">
                                        <i class="fa fa-user-check"></i> Revisar solicitud de cuidador
                                    </button>
                                @endif

                                {{-- Botón para convertir en encargado --}}
                                @php
                                    $hasEncargadoRole = $person->user && $person->user->hasRole('encargado');
                                @endphp
                                @if(!$hasEncargadoRole && $isAdmin)
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modalConvertirEncargado">
                                        <i class="fa fa-user-shield"></i> Convertir en Encargado
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal para aprobar/rechazar solicitud de cuidador --}}
    @if(isset($cuidadorPendiente) && $cuidadorPendiente && isset($canApproveCuidador) && $canApproveCuidador)
    <div class="modal fade" id="modalCuidadorAprobacion" tabindex="-1" role="dialog" aria-labelledby="modalCuidadorAprobacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCuidadorAprobacionLabel">
                        <i class="fa fa-user-check"></i> Revisar Solicitud de Cuidador
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('people.update', $person->id) }}" method="POST" id="formCuidadorAprobacion">
                    @method('PUT')
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cuidador_motivo_revision">Motivo de revisión <span class="text-danger">*</span></label>
                            <textarea 
                                class="form-control @error('cuidador_motivo_revision') is-invalid @enderror" 
                                id="cuidador_motivo_revision" 
                                name="cuidador_motivo_revision" 
                                rows="4" 
                                placeholder="Ingrese el motivo de aprobación o rechazo de la solicitud de cuidador..."
                                required
                                minlength="3">{{ old('cuidador_motivo_revision') }}</textarea>
                            @error('cuidador_motivo_revision')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                Este motivo será visible para el usuario y determinará si se le asigna el rol de cuidador.
                            </small>
                        </div>
                        @if($person->cuidador_center_id)
                        <div class="alert alert-info">
                            <strong>Centro seleccionado:</strong> {{ $person->cuidadorCenter?->nombre ?? 'N/A' }}
                        </div>
                        @endif
                        <input type="hidden" name="action" id="actionCuidador" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" id="btnRechazarCuidador">
                            <i class="fa fa-times-circle"></i> Rechazar
                        </button>
                        <button type="button" class="btn btn-success" id="btnAprobarCuidador">
                            <i class="fa fa-check-circle"></i> Aprobar
                        </button>
                    </div>
                </form>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var form = document.getElementById('formCuidadorAprobacion');
                    var actionInput = document.getElementById('actionCuidador');
                    var btnRechazar = document.getElementById('btnRechazarCuidador');
                    var btnAprobar = document.getElementById('btnAprobarCuidador');
                    var motivoTextarea = document.getElementById('cuidador_motivo_revision');
                    
                    function submitForm(action) {
                        // Validar que el motivo esté lleno
                        if (!motivoTextarea || !motivoTextarea.value || motivoTextarea.value.trim().length < 3) {
                            alert('Por favor, ingrese un motivo de revisión con al menos 3 caracteres.');
                            if (motivoTextarea) {
                                motivoTextarea.focus();
                                motivoTextarea.classList.add('is-invalid');
                            }
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
                    
                    // Validación en tiempo real del textarea
                    if (motivoTextarea) {
                        motivoTextarea.addEventListener('input', function() {
                            if (this.value.trim().length >= 3) {
                                this.classList.remove('is-invalid');
                                this.classList.add('is-valid');
                            } else {
                                this.classList.remove('is-valid');
                                this.classList.add('is-invalid');
                            }
                        });
                    }
                });
                </script>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal para convertir en encargado --}}
    @php
        $hasEncargadoRole = $person->user && $person->user->hasRole('encargado');
    @endphp
    @if(!$hasEncargadoRole && $isAdmin)
    <div class="modal fade" id="modalConvertirEncargado" tabindex="-1" role="dialog" aria-labelledby="modalConvertirEncargadoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConvertirEncargadoLabel">
                        <i class="fa fa-user-shield"></i> Convertir en Encargado
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('people.convert-to-encargado', $person->id) }}" method="POST" id="formConvertirEncargado">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            <strong>¿Está seguro?</strong> Se asignará el rol de <strong>Encargado</strong> a <strong>{{ $person->nombre }}</strong>.
                            <br><br>
                            Los encargados tienen permisos para:
                            <ul class="mb-0 mt-2">
                                <li>Gestionar hallazgos y reportes</li>
                                <li>Revisar solicitudes de personal</li>
                                <li>Acceder al mapa de campo</li>
                                <li>Gestionar animales y liberaciones</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i> Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @include('partials.page-pad')
@endsection
