@extends('adminlte::page')

@section('template_title')
    Veterinarians
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Veterinarians') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('veterinarians.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
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

                    <style>
                    .veterinarian-card .card-footer form { display: flex; width: 100%; }
                    .veterinarian-card .card-footer > div { display: flex; width: 100%; }
                    .veterinarian-card .card-footer > div > * { flex: 1 1 0; }
                    .veterinarian-card .card-footer > div > * + * { margin-left: .5rem; }
                    .text-xs {
                        font-size: 0.75rem;
                        line-height: 1.2;
                    }
                    </style>

                    <div class="card-body pb-0" style="padding-top: 0.5rem;">
                        <div class="row">
                            @foreach ($veterinarians as $veterinarian)
                                @php
                                    $person = $veterinarian->person;
                                    $email = $person->user?->email ?? 'Sin email';
                                    $fotoUrl = !empty($person->foto_path)
                                        ? asset('storage/' . $person->foto_path)
                                        : asset('storage/personas/persona.png');
                                    
                                    // Ajustar tamaño del email si es muy largo
                                    $emailLength = strlen($email);
                                    $emailClass = $emailLength > 23 ? 'text-xs' : 'text-sm';
                                    
                                    $isPending = $veterinarian->aprobado === null;
                                @endphp
                                <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
                                    <div class="card bg-light d-flex flex-fill veterinarian-card">
                                        <div class="card-header text-muted border-bottom-0 d-flex justify-content-between align-items-center" style="padding-right: 1rem;">
                                            <span>{{ __('Veterinario') }}</span>
                                            @if($isPending)
                                                <span class="badge badge-warning" style="font-size: 11px; padding: 4px 8px; margin-left: auto;">
                                                    Solicitud pendiente
                                                </span>
                                            @elseif($veterinarian->aprobado === true)
                                                <span class="badge badge-success" style="font-size: 11px; padding: 4px 8px; margin-left: auto;">
                                                    Aprobado
                                                </span>
                                            @else
                                                <span class="badge badge-danger" style="font-size: 11px; padding: 4px 8px; margin-left: auto;">
                                                    Rechazado
                                                </span>
                                            @endif
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="row">
                                                <div class="col-7">
                                                    <h2 class="lead"><b>{{ $person->nombre ?: 'Sin nombre' }}</b></h2>
                                                    <p class="text-muted {{ $emailClass }}"><b>Email: </b>{{ $email }}</p>
                                                    <ul class="ml-4 mb-0 fa-ul text-muted">
                                                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-id-card"></i></span> CI: {{ $person->ci ?: '-' }}</li>
                                                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Teléfono: {{ $person->telefono ?: '-' }}</li>
                                                        @if($veterinarian->especialidad)
                                                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-stethoscope"></i></span> <strong>Especialidad:</strong> {{ $veterinarian->especialidad }}</li>
                                                        @endif
                                                        @if($veterinarian->cv_documentado)
                                                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-file-pdf"></i></span> <a href="{{ asset('storage/' . $veterinarian->cv_documentado) }}" target="_blank" class="text-primary">Ver CV</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                <div class="col-5 text-center">
                                                    <div style="width: 80px; height: 80px; margin: 0 auto; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #e9ecef;">
                                                        <img src="{{ $fotoUrl }}" alt="user-avatar" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="d-flex w-100">
                                                <a class="btn btn-sm btn-primary" href="{{ route('veterinarians.show', $veterinarian->id) }}" style="flex: 1 1 0;">
                                                    <i class="fa fa-fw fa-eye"></i> {{ __('Show') }}
                                                </a>
                                                <a class="btn btn-sm btn-success" href="{{ route('veterinarians.edit', $veterinarian->id) }}" style="flex: 1 1 0; margin-left: 0.5rem;">
                                                    <i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}
                                                </a>
                                                @if($isPending)
                                                <button type="button" 
                                                        class="btn btn-sm btn-warning" 
                                                        data-toggle="modal" 
                                                        data-target="#modalAprobarVeterinarian{{ $veterinarian->id }}"
                                                        style="flex: 1 1 0; margin-left: 0.5rem;">
                                                    <i class="fa fa-fw fa-check"></i> {{ __('Aprobar') }}
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($veterinarians->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> {{ __('No se encontraron veterinarios.') }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <nav aria-label="Contacts Page Navigation">
                            {!! $veterinarians->withQueryString()->links() !!}
                        </nav>
                    </div>
                    <!-- /.card-footer -->
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
    
    {{-- Modales de aprobación para cada veterinario --}}
    @foreach ($veterinarians as $veterinarian)
        @if($veterinarian->aprobado === null && Auth::user()->hasAnyRole(['admin', 'encargado']))
        <div class="modal fade" id="modalAprobarVeterinarian{{ $veterinarian->id }}" tabindex="-1" role="dialog" aria-labelledby="modalAprobarVeterinarian{{ $veterinarian->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAprobarVeterinarian{{ $veterinarian->id }}Label">
                            <i class="fa fa-check-circle"></i> {{ __('Aprobar/Rechazar Solicitud de Veterinario') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('veterinarians.approve', $veterinarian->id) }}" method="POST" id="formAprobarVeterinarian{{ $veterinarian->id }}">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <p class="mb-3">{{ __('¿Desea aprobar o rechazar esta solicitud de veterinario?') }}</p>
                            <div class="form-group">
                                <label for="motivo_revision{{ $veterinarian->id }}">{{ __('Motivo de revisión') }} <span class="text-danger">*</span></label>
                                <textarea 
                                    class="form-control" 
                                    id="motivo_revision{{ $veterinarian->id }}" 
                                    name="motivo_revision" 
                                    rows="3" 
                                    required 
                                    minlength="3"
                                    placeholder="{{ __('Ingrese el motivo de la aprobación o rechazo...') }}"></textarea>
                                <small class="form-text text-muted">{{ __('Mínimo 3 caracteres') }}</small>
                            </div>
                            <input type="hidden" name="action" id="actionVeterinarian{{ $veterinarian->id }}" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="btnRechazarVeterinarian{{ $veterinarian->id }}">
                                <i class="fa fa-times-circle"></i> {{ __('Rechazar') }}
                            </button>
                            <button type="button" class="btn btn-success" id="btnAprobarVeterinarian{{ $veterinarian->id }}">
                                <i class="fa fa-check-circle"></i> {{ __('Aprobar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // Manejar aprobación/rechazo de veterinarios
        @foreach ($veterinarians as $veterinarian)
            @if($veterinarian->aprobado === null && Auth::user()->hasAnyRole(['admin', 'encargado']))
            (function() {
                var form = document.getElementById('formAprobarVeterinarian{{ $veterinarian->id }}');
                var actionInput = document.getElementById('actionVeterinarian{{ $veterinarian->id }}');
                var motivoInput = document.getElementById('motivo_revision{{ $veterinarian->id }}');
                var btnRechazar = document.getElementById('btnRechazarVeterinarian{{ $veterinarian->id }}');
                var btnAprobar = document.getElementById('btnAprobarVeterinarian{{ $veterinarian->id }}');
                
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
            })();
            @endif
        @endforeach
    });
    </script>
@endsection
