@extends('adminlte::page')

@section('template_title')
    Rescuers
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Rescuers') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('rescuers.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
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
                    .rescuer-card .card-footer > div { display: flex; width: 100%; }
                    .rescuer-card .card-footer > div > * { flex: 1 1 0; }
                    .rescuer-card .card-footer > div > * + * { margin-left: .5rem; }
                    .text-xs {
                        font-size: 0.75rem;
                        line-height: 1.2;
                    }
                    </style>

                    <div class="card-body pb-0" style="padding-top: 0.5rem;">
                        <div class="row">
                            @foreach ($rescuers as $rescuer)
                                @php
                                    $person = $rescuer->person;
                                    $email = $person->user?->email ?? 'Sin email';
                                    $fotoUrl = !empty($person->foto_path)
                                        ? asset('storage/' . $person->foto_path)
                                        : asset('storage/personas/persona.png');
                                    
                                    // Ajustar tamaño del email si es muy largo
                                    $emailLength = strlen($email);
                                    $emailClass = $emailLength > 23 ? 'text-xs' : 'text-sm';
                                    
                                    $isPending = $rescuer->aprobado === null;
                                @endphp
                                <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
                                    <div class="card bg-light d-flex flex-fill rescuer-card">
                                        <div class="card-header text-muted border-bottom-0 d-flex justify-content-between align-items-center" style="padding-right: 1rem;">
                                            <span>{{ __('Rescatista') }}</span>
                                            @if($isPending)
                                                <span class="badge badge-warning" style="font-size: 11px; padding: 4px 8px; margin-left: auto;">
                                                    Solicitud pendiente
                                                </span>
                                            @elseif($rescuer->aprobado === true)
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
                                                        
                                                        @if($rescuer->cv_documentado)
                                                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-file-pdf"></i></span> <a href="{{ asset('storage/' . $rescuer->cv_documentado) }}" target="_blank" class="text-primary">Ver CV</a></li>
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
                                                <a class="btn btn-sm btn-primary" href="{{ route('rescuers.show', $rescuer->id) }}" style="flex: 1 1 0;">
                                                    <i class="fa fa-fw fa-eye"></i> {{ __('Show') }}
                                                </a>
                                                <a class="btn btn-sm btn-success" href="{{ route('rescuers.edit', $rescuer->id) }}" style="flex: 1 1 0; margin-left: 0.5rem;">
                                                    <i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}
                                                </a>
                                                @if($isPending)
                                                <button type="button" 
                                                        class="btn btn-sm btn-warning" 
                                                        data-toggle="modal" 
                                                        data-target="#modalAprobarRescuer{{ $rescuer->id }}"
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

                        @if($rescuers->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> {{ __('No se encontraron rescatistas.') }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <nav aria-label="Contacts Page Navigation">
                            {!! $rescuers->withQueryString()->links() !!}
                        </nav>
                    </div>
                    <!-- /.card-footer -->
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
    
    {{-- Modales de aprobación para cada rescatista --}}
    @foreach ($rescuers as $rescuer)
        @if($rescuer->aprobado === null && Auth::user()->hasAnyRole(['admin', 'encargado']))
        <div class="modal fade" id="modalAprobarRescuer{{ $rescuer->id }}" tabindex="-1" role="dialog" aria-labelledby="modalAprobarRescuer{{ $rescuer->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAprobarRescuer{{ $rescuer->id }}Label">
                            <i class="fa fa-check-circle"></i> {{ __('Aprobar/Rechazar Solicitud de Rescatista') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('rescuers.approve', $rescuer->id) }}" method="POST" id="formAprobarRescuer{{ $rescuer->id }}">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <p class="mb-3">{{ __('¿Desea aprobar o rechazar esta solicitud de rescatista?') }}</p>
                            <div class="form-group">
                                <label for="motivo_revision{{ $rescuer->id }}">{{ __('Motivo de revisión') }} <span class="text-danger">*</span></label>
                                <textarea 
                                    class="form-control" 
                                    id="motivo_revision{{ $rescuer->id }}" 
                                    name="motivo_revision" 
                                    rows="3" 
                                    required 
                                    minlength="3"
                                    placeholder="{{ __('Ingrese el motivo de la aprobación o rechazo...') }}"></textarea>
                                <small class="form-text text-muted">{{ __('Mínimo 3 caracteres') }}</small>
                            </div>
                            <input type="hidden" name="action" id="actionRescuer{{ $rescuer->id }}" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="btnRechazarRescuer{{ $rescuer->id }}">
                                <i class="fa fa-times-circle"></i> {{ __('Rechazar') }}
                            </button>
                            <button type="button" class="btn btn-success" id="btnAprobarRescuer{{ $rescuer->id }}">
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
        // Manejar aprobación/rechazo de rescatistas
        @foreach ($rescuers as $rescuer)
            @if($rescuer->aprobado === null && Auth::user()->hasAnyRole(['admin', 'encargado']))
            (function() {
                var form = document.getElementById('formAprobarRescuer{{ $rescuer->id }}');
                var actionInput = document.getElementById('actionRescuer{{ $rescuer->id }}');
                var motivoInput = document.getElementById('motivo_revision{{ $rescuer->id }}');
                var btnRechazar = document.getElementById('btnRechazarRescuer{{ $rescuer->id }}');
                var btnAprobar = document.getElementById('btnAprobarRescuer{{ $rescuer->id }}');
                
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
