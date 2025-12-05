<div class="row">
    <div class="col-md-12">
        {{-- Card de Información Personal --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-2"></i>Información Personal
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre" class="form-label">{{ __('Nombre completo') }} <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $person?->nombre) }}" id="nombre" placeholder="Nombre completo" required>
                            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ci" class="form-label">{{ __('CI') }} <span class="text-danger">*</span></label>
                            <input type="text" name="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci', $person?->ci) }}" id="ci" placeholder="Cédula de Identidad" required>
                            {!! $errors->first('ci', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono" class="form-label">{{ __('Teléfono') }}</label>
                            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $person?->telefono) }}" id="telefono" placeholder="Teléfono">
                            {!! $errors->first('telefono', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card de Información de Usuario --}}
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-envelope mr-2"></i>Información de Usuario
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">{{ __('Email') }} @if(!$person || !$person->user)<span class="text-danger">*</span>@endif</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $person?->user?->email) }}" id="email" placeholder="correo@ejemplo.com" @if(!$person || !$person->user)required @endif>
                            {!! $errors->first('email', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                            @if($person && $person->user)
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Puedes cambiar el correo electrónico del usuario asociado.
                                </small>
                            @else
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> El email será usado para crear la cuenta de usuario.
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if(!$person || !$person->user)
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="form-label">{{ __('Contraseña') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Mínimo 8 caracteres" required>
                            {!! $errors->first('password', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                            <small class="form-text text-muted">
                                <i class="fas fa-lock"></i> La contraseña debe tener al menos 8 caracteres.
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">{{ __('Confirmar Contraseña') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" placeholder="Confirma la contraseña" required>
                            {!! $errors->first('password_confirmation', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Card de Información de Cuidador --}}
        <div class="card card-warning card-outline" id="cuidador_card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-heart mr-2"></i>Información de Cuidador
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info-circle"></i> Asignación de Cuidador</h5>
                    <p class="mb-2">Como administrador, puedes asignar a una persona como cuidador voluntario. Si marcas "Sí", debes:</p>
                    <ul class="mb-0">
                        <li>Completar el motivo de la asignación</li>
                        <li>Seleccionar un centro de atención</li>
                        <li>Decidir si aprobar o rechazar la solicitud</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label for="es_cuidador" class="form-label">{{ __('¿Es Cuidador?') }}</label>
                    <select name="es_cuidador" id="es_cuidador" class="form-control @error('es_cuidador') is-invalid @enderror">
                        @php($val = (string)old('es_cuidador', $person?->es_cuidador ?? '0'))
                        <option value="0" {{ $val === '0' || $val === '' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ $val === '1' ? 'selected' : '' }}>Sí</option>
                    </select>
                    {!! $errors->first('es_cuidador', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>

                <div id="cuidador_fields" style="display: {{ old('es_cuidador', $person?->es_cuidador) ? 'block' : 'none' }};">
                    <div class="border rounded p-3 bg-light mb-3">
                        <h5 class="mb-3"><i class="fas fa-clipboard-list mr-2"></i>Datos del Cuidador</h5>
                        
                        <div class="form-group">
                            <label for="cuidador_motivo_revision" class="form-label">
                                {{ __('Motivo de Asignación') }} <span class="text-danger">*</span>
                            </label>
                            <textarea name="cuidador_motivo_revision" id="cuidador_motivo_revision" class="form-control @error('cuidador_motivo_revision') is-invalid @enderror" rows="4" placeholder="Ingrese el motivo o comentarios sobre la asignación como cuidador (mínimo 10 caracteres)...">{{ old('cuidador_motivo_revision', $person?->cuidador_motivo_revision) }}</textarea>
                            {!! $errors->first('cuidador_motivo_revision', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Este campo es obligatorio si la persona es cuidador. El rol se asignará automáticamente si está aprobado y tiene motivo.
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="cuidador_center_id" class="form-label">
                                {{ __('Centro de Atención') }} <span class="text-danger">*</span>
                            </label>
                            <select name="cuidador_center_id" id="cuidador_center_id" class="form-control @error('cuidador_center_id') is-invalid @enderror">
                                <option value="">Seleccione un centro</option>
                                @foreach(\App\Models\Center::orderBy('nombre')->get() as $center)
                                    <option value="{{ $center->id }}" {{ old('cuidador_center_id', $person?->cuidador_center_id) == $center->id ? 'selected' : '' }}>
                                        {{ $center->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            {!! $errors->first('cuidador_center_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                            <small class="form-text text-muted">
                                <i class="fas fa-map-marker-alt"></i> También puedes seleccionar el centro desde el mapa más abajo.
                            </small>
                        </div>

                        {{-- Mapa de selección de centros --}}
                        @if(isset($centers) && $centers->isNotEmpty())
                        <div class="form-group">
                            <label class="form-label">{{ __('Seleccionar Centro desde el Mapa') }}</label>
                            @include('partials.centers-map', [
                                'centers' => $centers ?? [],
                                'mapId' => 'admin_cuidador_centers_map',
                                'inputId' => 'cuidador_center_id',
                                'selectedCenterId' => old('cuidador_center_id', $person?->cuidador_center_id)
                            ])
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="cuidador_aprobado" class="form-label">{{ __('Estado de Aprobación') }}</label>
                            <select name="cuidador_aprobado" id="cuidador_aprobado" class="form-control @error('cuidador_aprobado') is-invalid @enderror">
                                @php($aprobado = old('cuidador_aprobado', $person?->cuidador_aprobado))
                                <option value="">Pendiente</option>
                                <option value="1" {{ $aprobado === '1' || $aprobado === 1 ? 'selected' : '' }}>Aprobado</option>
                                <option value="0" {{ $aprobado === '0' || $aprobado === 0 ? 'selected' : '' }}>Rechazado</option>
                            </select>
                            {!! $errors->first('cuidador_aprobado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                            <small class="form-text text-muted">
                                <i class="fas fa-check-circle"></i> Si apruebas al cuidador y completas el motivo, se asignará automáticamente el rol de cuidador.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
            <a href="{{ route('people.index') }}" class="btn btn-secondary ml-2">{{ __('Cancelar') }}</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const esCuidadorSelect = document.getElementById('es_cuidador');
    const cuidadorCard = document.getElementById('cuidador_card');
    const cuidadorFields = document.getElementById('cuidador_fields');
    const motivoInput = document.getElementById('cuidador_motivo_revision');
    const centerSelect = document.getElementById('cuidador_center_id');
    
    // Botón para mostrar/ocultar la sección de cuidador
    const toggleButton = document.createElement('button');
    toggleButton.type = 'button';
    toggleButton.className = 'btn btn-warning btn-sm mb-3';
    toggleButton.innerHTML = '<i class="fas fa-heart mr-2"></i>Asignar como Cuidador';
    toggleButton.id = 'toggle_cuidador_btn';
    
    // Insertar el botón antes de la card de cuidador
    if (cuidadorCard && cuidadorCard.parentNode) {
        cuidadorCard.parentNode.insertBefore(toggleButton, cuidadorCard);
    }
    
    if (esCuidadorSelect && cuidadorCard && cuidadorFields) {
        function toggleCuidadorSection() {
            const isCuidador = esCuidadorSelect.value === '1';
            
            // Mostrar/ocultar la card completa
            if (isCuidador) {
                cuidadorCard.style.display = 'block';
                toggleButton.style.display = 'none';
            } else {
                cuidadorCard.style.display = 'none';
                toggleButton.style.display = 'block';
            }
            
            // Mostrar/ocultar campos internos
            cuidadorFields.style.display = isCuidador ? 'block' : 'none';
            
            // Hacer campos requeridos o no según el estado
            if (motivoInput) {
                motivoInput.required = isCuidador;
            }
            if (centerSelect) {
                centerSelect.required = isCuidador;
            }
            
            // Invalidar tamaño del mapa si existe
            if (isCuidador && window['centersMap_admin_cuidador_centers_map']) {
                setTimeout(() => {
                    window['centersMap_admin_cuidador_centers_map'].invalidateSize();
                }, 200);
            }
        }
        
        // Botón para mostrar la sección
        toggleButton.addEventListener('click', function() {
            esCuidadorSelect.value = '1';
            toggleCuidadorSection();
        });
        
        // Cambio en el select
        esCuidadorSelect.addEventListener('change', toggleCuidadorSection);
        
        // Ejecutar al cargar la página
        toggleCuidadorSection();
        
        // Sincronizar el select con el mapa cuando se selecciona desde el mapa
        if (centerSelect) {
            centerSelect.addEventListener('change', function() {
                const selectedId = this.value;
                if (selectedId && window['centersMarkers_admin_cuidador_centers_map']) {
                    const markers = window['centersMarkers_admin_cuidador_centers_map'];
                    markers.forEach(obj => {
                        const iconEl = obj.marker?._icon;
                        if (!iconEl) return;
                        iconEl.classList.remove('selected');
                    });
                    const selected = markers.find(obj => String(obj.id) === String(selectedId));
                    if (selected && selected.marker && selected.marker._icon) {
                        selected.marker._icon.classList.add('selected');
                    }
                }
            });
        }
    }
});
</script>
@endpush
