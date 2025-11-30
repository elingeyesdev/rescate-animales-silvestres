@extends('adminlte::page')

@section('title', 'Perfil de usuario')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Perfil de usuario</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Perfil de usuario</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                {{-- Columna izquierda: resumen de la persona --}}
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @php
                                $fotoUrl = !empty($person->foto_path)
                                    ? asset('storage/' . $person->foto_path)
                                    : asset('vendor/adminlte/dist/img/user2-160x160.jpg');
                            @endphp
                            <img class="profile-user-img img-fluid img-circle"
                                 src="{{ $fotoUrl }}"
                                 alt="Foto de perfil">
                        </div>

                        <h3 class="profile-username text-center">{{ $person->nombre ?: 'Sin nombre' }}</h3>

                        <p class="text-muted text-center">{{ $user->email }}</p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>CI</b> <span class="float-right">{{ $person->ci ?: '-' }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Teléfono</b> <span class="float-right">{{ $person->telefono ?: '-' }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Es cuidador</b>
                                <span class="float-right">
                                    {{ (int)$person->es_cuidador === 1 ? 'Sí' : 'No' }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Información adicional --}}
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Estado de colaboraciones</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Rescatista:</strong>
                            @if($rescuer)
                                @if($rescuer->aprobado === true)
                                    <span class="badge badge-success mr-1">Aceptado</span>
                                @elseif($rescuer->aprobado === false && $rescuer->motivo_revision)
                                    <span class="badge badge-danger mr-1">No aceptado</span>
                                @elseif($rescuer->aprobado === false)
                                    <span class="badge badge-danger mr-1">No aceptado</span>
                                @else
                                    <span class="badge badge-warning mr-1">En revisión</span>
                                @endif
                            @else
                                <span class="text-muted">Sin postulación</span>
                            @endif
                        </p>
                        <p class="mb-2">
                            <strong>Veterinario:</strong>
                            @if($veterinarian)
                                @if($veterinarian->aprobado === true)
                                    <span class="badge badge-success mr-1">Aceptado</span>
                                    <small>Tu postulación como veterinario fue aceptada.</small>
                                @elseif($veterinarian->aprobado === false && $veterinarian->motivo_revision)
                                    <span class="badge badge-danger mr-1">No aceptado</span>
                                    <small>Tu postulación no fue aceptada. Motivo: {{ $veterinarian->motivo_revision }}</small>
                                @elseif($veterinarian->aprobado === false)
                                    <span class="badge badge-danger mr-1">No aceptado</span>
                                    <small>Tu postulación no fue aceptada.</small>
                                @else
                                    <span class="badge badge-warning mr-1">En revisión</span>
                                    <small>Tu postulación está en proceso de revisión.</small>
                                @endif
                            @else
                                <span class="text-muted">Sin postulación</span>
                            @endif
                        </p>
                        @if($rescuer?->motivo_revision || $veterinarian?->motivo_revision)
                            <hr>
                            <p class="text-muted">
                                <strong>Comentarios de revisión:</strong><br>
                                @if($rescuer?->motivo_revision)
                                    <small>Rescatista: {{ $rescuer->motivo_revision }}</small><br>
                                @endif
                                @if($veterinarian?->motivo_revision)
                                    <small>Veterinario: {{ $veterinarian->motivo_revision }}</small>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#datos" data-toggle="tab">Datos personales</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#colaborar" data-toggle="tab">Quiero colaborar</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#contactar" data-toggle="tab">Contactar administración</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="tab-content">
                            {{-- TAB DATOS PERSONALES --}}
                            <div class="active tab-pane" id="datos">
                                <form class="form-horizontal" method="POST" action="{{ route('profile.update', 0) }}"
                                      enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="modo" value="datos">

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-3 col-form-label">Nombre completo</label>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                   class="form-control @error('nombre') is-invalid @enderror"
                                                   id="nombre" name="nombre"
                                                   value="{{ old('nombre', $person->nombre) }}"
                                                   required>
                                            @error('nombre')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="ci" class="col-sm-3 col-form-label">Documento (CI)</label>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                   class="form-control @error('ci') is-invalid @enderror"
                                                   id="ci" name="ci"
                                                   value="{{ old('ci', $person->ci) }}"
                                                   required>
                                            @error('ci')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="telefono" class="col-sm-3 col-form-label">Teléfono</label>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                   class="form-control @error('telefono') is-invalid @enderror"
                                                   id="telefono" name="telefono"
                                                   value="{{ old('telefono', $person->telefono) }}">
                                            @error('telefono')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="foto" class="col-sm-3 col-form-label">Foto de perfil</label>
                                        <div class="col-sm-9">
                                            <div class="custom-file">
                                                <input type="file"
                                                       class="custom-file-input @error('foto') is-invalid @enderror"
                                                       id="foto" name="foto">
                                                <label class="custom-file-label" for="foto">
                                                    Seleccionar foto (opcional)
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Se utilizará como imagen principal de tu perfil. Formatos: jpg, jpeg, png, webp. Máx. 5MB.
                                            </small>
                                            @error('foto')
                                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror

                                            @if(!empty($person->foto_path))
                                                <div class="mt-2">
                                                    <strong>Foto actual:</strong><br>
                                                    <img id="foto_preview"
                                                         src="{{ asset('storage/' . $person->foto_path) }}"
                                                         alt="Foto actual"
                                                         class="img-thumbnail"
                                                         style="max-width: 120px;">
                                                </div>
                                            @else
                                                <div class="mt-2" id="foto_preview_container" style="display:none;">
                                                    <strong>Vista previa:</strong><br>
                                                    <img id="foto_preview"
                                                         src="#"
                                                         alt="Vista previa"
                                                         class="img-thumbnail"
                                                         style="max-width: 120px;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row mt-4">
                                        <div class="offset-sm-3 col-sm-9">
                                            <button type="submit" class="btn btn-primary">
                                                Guardar datos personales
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {{-- TAB QUIERO COLABORAR --}}
                            <div class="tab-pane" id="colaborar">
                                <ul class="nav nav-pills mb-3">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#tab-cuidador" data-toggle="tab">Cuidador</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#tab-rescatista" data-toggle="tab">Rescatista</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#tab-veterinario" data-toggle="tab">Veterinario</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    {{-- Sub-tab Cuidador --}}
                                    <div class="active tab-pane" id="tab-cuidador">
                                        <form class="form-horizontal" method="POST" action="{{ route('profile.update', 0) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="modo" value="cuidador">
                                            {{-- Datos mínimos de persona para validación --}}
                                            <input type="hidden" name="nombre" value="{{ old('nombre', $person->nombre) }}">
                                            <input type="hidden" name="ci" value="{{ old('ci', $person->ci) }}">
                                            <input type="hidden" name="telefono" value="{{ old('telefono', $person->telefono) }}">

                                            <div class="border rounded p-3 mb-3 bg-light">
                                                <h4 class="mb-3" style="font-size: 1.5rem; font-weight: 600;">Compromiso de cuidador voluntario</h4>
                                                <div class="alert alert-info" role="alert">
                                                    <p class="mb-2">
                                                        Como <strong>cuidador voluntario</strong> ayudas en el día a día de los animales
                                                        que están en recuperación: alimentación básica, limpieza de espacios,
                                                        acompañamiento y observación de su bienestar.
                                                    </p>
                                                    <p class="mb-2">
                                                        No realizas procedimientos médicos ni traslados especializados, pero
                                                        tu apoyo es clave para que los animales se mantengan estables mientras
                                                        esperan atención veterinaria.
                                                    </p>
                                                    <p class="mb-0">
                                                        El compromiso es <strong>voluntario</strong> y puedes dejar de ser cuidador en cualquier
                                                        momento desmarcando la opción y presionando el botón de guardar.
                                                    </p>
                                                </div>

                                                <p class="mb-2">
                                                    <strong>Estado actual:</strong>
                                                    @if((int)$person->es_cuidador === 1)
                                                        @if(!empty($person->cuidador_motivo_revision))
                                                            <span class="badge badge-success">Cuidador aprobado</span>
                                                            <br><small class="text-muted mt-1 d-block">Tu solicitud fue aprobada. Motivo: {{ $person->cuidador_motivo_revision }}</small>
                                                            @if($person->cuidadorCenter)
                                                                <br><small class="text-muted mt-1 d-block"><strong>Centro asignado:</strong> {{ $person->cuidadorCenter->nombre }}</small>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-warning">Solicitud pendiente</span>
                                                            <br><small class="text-muted mt-1 d-block">Debes acudir al centro para que se habilite tu rol de cuidador. Una vez que el administrador complete el motivo de revisión, se te asignará el rol automáticamente.</small>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-secondary">Aún no eres cuidador</span>
                                                    @endif
                                                </p>

                                                <div class="form-group row mb-0">
                                                    <div class="col-sm-12">
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                   class="form-check-input"
                                                                   id="compromiso_cuidador"
                                                                   name="compromiso_cuidador"
                                                                   value="1"
                                                                   {{ (int)$person->es_cuidador === 1 ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="compromiso_cuidador">
                                                                Me comprometo a ayudar de forma voluntaria en el cuidado de animales.
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Mapa de selección de centros (solo visible si el checkbox está marcado) --}}
                                            <div id="centro_selection_wrap" style="display: {{ (int)$person->es_cuidador === 1 ? 'block' : 'none' }};">
                                                @include('partials.centers-map', [
                                                    'centers' => $centers ?? [],
                                                    'mapId' => 'cuidador_centers_map',
                                                    'inputId' => 'cuidador_center_id',
                                                    'selectedCenterId' => old('cuidador_center_id', $person->cuidador_center_id)
                                                ])
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <button type="submit" class="btn btn-primary">
                                                        Guardar compromiso de cuidador
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- Sub-tab Rescatista --}}
                                    <div class="tab-pane" id="tab-rescatista">
                                        <div class="border rounded p-3 mb-3 bg-light">
                                            <h5 class="mb-3">¿Qué hace un rescatista?</h5>
                                            <p class="mb-2">
                                                El <strong>rescatista</strong> es quien acude al lugar del incidente,
                                                evalúa la situación y colabora en el rescate y traslado seguro de los animales
                                                hacia centros de atención o puntos de encuentro con veterinarios.
                                            </p>
                                            <p class="mb-0">
                                                Debe seguir los protocolos del equipo, comunicarse con el centro de rescate
                                                y priorizar siempre la seguridad propia, de otras personas y de los animales.
                                            </p>
                                        </div>

                                        <p class="mb-3">
                                            <strong>Estado de tu postulación como rescatista:</strong>
                                            @if($rescuer)
                                                @if($rescuer->aprobado === true)
                                                    <span class="badge badge-success mr-1">Aceptado</span>
                                                    <small>Tu postulación fue aceptada.</small>
                                                @elseif($rescuer->aprobado === false && $rescuer->motivo_revision)
                                                    <span class="badge badge-danger mr-1">No aceptado</span>
                                                    <small>Tu postulación no fue aceptada. Motivo: {{ $rescuer->motivo_revision }}</small>
                                                @elseif($rescuer->aprobado === false)
                                                    <span class="badge badge-danger mr-1">No aceptado</span>
                                                    <small>Tu postulación no fue aceptada.</small>
                                                @else
                                                    <span class="badge badge-warning mr-1">En revisión</span>
                                                    <small>Tu postulación está en proceso de revisión.</small>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">Sin postulación</span>
                                            @endif
                                        </p>

                                        @if(!$rescuer)
                                            {{-- Formulario solo si aún no existe una postulación --}}
                                            <form class="form-horizontal" method="POST" action="{{ route('profile.update', 0) }}"
                                                  enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="modo" value="rescatista">
                                                {{-- Datos mínimos de persona para validación --}}
                                                <input type="hidden" name="nombre" value="{{ old('nombre', $person->nombre) }}">
                                                <input type="hidden" name="ci" value="{{ old('ci', $person->ci) }}">
                                                <input type="hidden" name="telefono" value="{{ old('telefono', $person->telefono) }}">

                                                <input type="hidden" name="rol_postulacion" value="rescatista">

                                                <div class="form-group row">
                                                    <label for="cv_rescatista" class="col-sm-3 col-form-label">CV</label>
                                                    <div class="col-sm-9">
                                                        <div class="custom-file">
                                                            <input type="file"
                                                                   class="custom-file-input @error('cv') is-invalid @enderror"
                                                                   id="cv_rescatista" name="cv">
                                                            <label class="custom-file-label" for="cv_rescatista">
                                                                Seleccionar archivo
                                                            </label>
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            Formatos admitidos: pdf, doc, docx, jpg, jpeg, png, webp. Máx. 5MB.
                                                        </small>
                                                        @error('cv')
                                                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror

                                                        <div class="mt-2" id="cv_rescatista_preview_container" style="display:none;">
                                                            <strong>Vista previa (solo imágenes):</strong><br>
                                                            <img id="cv_rescatista_preview"
                                                                 src="#"
                                                                 alt="Vista previa CV rescatista"
                                                                 class="img-thumbnail"
                                                                 style="max-width: 160px;">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="motivo_postulacion_rescatista" class="col-sm-3 col-form-label">
                                                        Motivo de postulación
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <textarea
                                                            class="form-control @error('motivo_postulacion') is-invalid @enderror"
                                                            id="motivo_postulacion_rescatista"
                                                            name="motivo_postulacion"
                                                            rows="4"
                                                            placeholder="Cuéntanos por qué quieres ser rescatista.">{{ old('motivo_postulacion') }}</textarea>
                                                        @error('motivo_postulacion')
                                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <button type="submit" class="btn btn-primary">
                                                            Enviar postulación como rescatista
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        @else
                                            {{-- Resumen de la postulación existente --}}
                                            <div class="border rounded p-3 bg-white mt-2">
                                                <p class="mb-1">
                                                    <strong>Motivo de tu postulación:</strong><br>
                                                    {{ $rescuer->motivo_postulacion ?? 'Sin motivo registrado.' }}
                                                </p>
                                                @if($rescuer->cv_documentado)
                                                    <p class="mb-1">
                                                        <strong>CV enviado:</strong>
                                                        <a href="{{ asset('storage/' . $rescuer->cv_documentado) }}" target="_blank">
                                                            Ver CV como rescatista
                                                        </a>
                                                    </p>
                                                @endif
                                                <p class="mb-0">
                                                    <a href="{{ route('rescuers.show', $rescuer->id) }}" class="btn btn-link p-0">
                                                        Ver detalles completos de tu postulación
                                                    </a>
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Sub-tab Veterinario --}}
                                    <div class="tab-pane" id="tab-veterinario">
                                        <div class="border rounded p-3 mb-3 bg-light">
                                            <h5 class="mb-3">¿Qué hace un veterinario?</h5>
                                            <p class="mb-2">
                                                El <strong>veterinario</strong> realiza evaluaciones clínicas, prescribe
                                                tratamientos, participa en procedimientos médicos y define planes de cuidado
                                                acordes a la especie y condición de cada animal.
                                            </p>
                                            <p class="mb-0">
                                                Es un rol profesional que requiere formación en medicina veterinaria y,
                                                idealmente, experiencia o especialización en fauna silvestre o pequeños animales.
                                            </p>
                                        </div>

                                        <p class="mb-3">
                                            <strong>Estado de tu postulación como veterinario:</strong>
                                            @if($veterinarian)
                                                @if($veterinarian->aprobado === true)
                                                    <span class="badge badge-success mr-1">Aceptado</span>
                                                    <small>Tu postulación fue aceptada.</small>
                                                @elseif($veterinarian->aprobado === false && $veterinarian->motivo_revision)
                                                    <span class="badge badge-danger mr-1">No aceptado</span>
                                                    <small>Tu postulación no fue aceptada. Motivo: {{ $veterinarian->motivo_revision }}</small>
                                                @elseif($veterinarian->aprobado === false)
                                                    <span class="badge badge-danger mr-1">No aceptado</span>
                                                    <small>Tu postulación no fue aceptada.</small>
                                                @else
                                                    <span class="badge badge-warning mr-1">En revisión</span>
                                                    <small>Tu postulación está en proceso de revisión.</small>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">Sin postulación</span>
                                            @endif
                                        </p>

                                        @if(!$veterinarian)
                                            {{-- Formulario solo si aún no existe una postulación --}}
                                            <form class="form-horizontal" method="POST" action="{{ route('profile.update', 0) }}"
                                                  enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="modo" value="veterinario">
                                                {{-- Datos mínimos de persona para validación --}}
                                                <input type="hidden" name="nombre" value="{{ old('nombre', $person->nombre) }}">
                                                <input type="hidden" name="ci" value="{{ old('ci', $person->ci) }}">
                                                <input type="hidden" name="telefono" value="{{ old('telefono', $person->telefono) }}">

                                                <input type="hidden" name="rol_postulacion" value="veterinario">

                                                <div class="form-group row">
                                                    <label for="especialidad" class="col-sm-3 col-form-label">
                                                        Especialidad
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <input type="text"
                                                               class="form-control @error('especialidad') is-invalid @enderror"
                                                               id="especialidad" name="especialidad"
                                                               value="{{ old('especialidad') }}">
                                                        @error('especialidad')
                                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="cv_veterinario" class="col-sm-3 col-form-label">CV</label>
                                                    <div class="col-sm-9">
                                                        <div class="custom-file">
                                                            <input type="file"
                                                                   class="custom-file-input @error('cv') is-invalid @enderror"
                                                                   id="cv_veterinario" name="cv">
                                                            <label class="custom-file-label" for="cv_veterinario">
                                                                Seleccionar archivo
                                                            </label>
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            Formatos admitidos: pdf, doc, docx, jpg, jpeg, png, webp. Máx. 5MB.
                                                        </small>
                                                        @error('cv')
                                                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror

                                                        <div class="mt-2" id="cv_veterinario_preview_container" style="display:none;">
                                                            <strong>Vista previa (solo imágenes):</strong><br>
                                                            <img id="cv_veterinario_preview"
                                                                 src="#"
                                                                 alt="Vista previa CV veterinario"
                                                                 class="img-thumbnail"
                                                                 style="max-width: 160px;">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="motivo_postulacion_veterinario" class="col-sm-3 col-form-label">
                                                        Motivo de postulación
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <textarea
                                                            class="form-control @error('motivo_postulacion') is-invalid @enderror"
                                                            id="motivo_postulacion_veterinario"
                                                            name="motivo_postulacion"
                                                            rows="4"
                                                            placeholder="Cuéntanos por qué quieres colaborar como veterinario.">{{ old('motivo_postulacion') }}</textarea>
                                                        @error('motivo_postulacion')
                                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <button type="submit" class="btn btn-primary">
                                                            Enviar postulación como veterinario
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        @else
                                            {{-- Resumen de la postulación existente --}}
                                            <div class="border rounded p-3 bg-white mt-2">
                                                <p class="mb-1">
                                                    <strong>Motivo de tu postulación:</strong><br>
                                                    {{ $veterinarian->motivo_postulacion ?? 'Sin motivo registrado.' }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong>Especialidad declarada:</strong><br>
                                                    {{ $veterinarian->especialidad ?? '-' }}
                                                </p>
                                                @if($veterinarian->cv_documentado)
                                                    <p class="mb-1">
                                                        <strong>CV enviado:</strong>
                                                        <a href="{{ asset('storage/' . $veterinarian->cv_documentado) }}" target="_blank">
                                                            Ver CV como veterinario
                                                        </a>
                                                    </p>
                                                @endif
                                                <p class="mb-0">
                                                    <a href="{{ route('veterinarians.show', $veterinarian->id) }}" class="btn btn-link p-0">
                                                        Ver detalles completos de tu postulación
                                                    </a>
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- TAB CONTACTAR ADMINISTRACIÓN --}}
                            <div class="tab-pane" id="contactar">
                                <h4 class="mb-3">Contactar a administración</h4>
                                <p class="text-muted mb-4">
                                    Si tienes alguna consulta, problema o necesitas comunicarte directamente con un administrador o encargado, puedes enviar un mensaje aquí.
                                </p>
                                
                                <form action="{{ route('contact-messages.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="motivo">Motivo del contacto <span class="text-danger">*</span></label>
                                        <select name="motivo" id="motivo" class="form-control @error('motivo') is-invalid @enderror" required>
                                            <option value="">Selecciona un motivo</option>
                                            @foreach(\App\Models\ContactMessage::getMotivos() as $key => $label)
                                                <option value="{{ $key }}" {{ old('motivo') === $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('motivo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="mensaje">Mensaje <span class="text-danger">*</span></label>
                                        <textarea 
                                            name="mensaje" 
                                            id="mensaje" 
                                            class="form-control @error('mensaje') is-invalid @enderror" 
                                            rows="6" 
                                            placeholder="Escribe tu mensaje aquí (mínimo 10 caracteres, máximo 1000 caracteres)..."
                                            required
                                            minlength="10"
                                            maxlength="1000">{{ old('mensaje') }}</textarea>
                                        <small class="form-text text-muted">
                                            Mínimo 10 caracteres, máximo 1000 caracteres.
                                        </small>
                                        @error('mensaje')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Enviar mensaje
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('partials.leaflet')
@endsection

@section('js')
    <script>
        (function () {
            // Mostrar nombre de archivo seleccionado en cualquier input de tipo file con estilo custom-file
            const fileInputs = document.querySelectorAll('.custom-file-input');
            fileInputs.forEach(function (input) {
                input.addEventListener('change', function (e) {
                    const fileName = e.target.files[0]?.name || 'Seleccionar archivo';
                    const label = e.target.nextElementSibling;
                    if (label && label.classList.contains('custom-file-label')) {
                        label.textContent = fileName;
                    }
                });
            });

            // Vista previa de la foto de perfil
            const fotoInput = document.getElementById('foto');
            const fotoPreview = document.getElementById('foto_preview');
            const fotoPreviewContainer = document.getElementById('foto_preview_container') || fotoPreview?.parentElement;

            if (fotoInput && fotoPreview) {
                fotoInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (!file) {
                        return;
                    }
                    if (!file.type.startsWith('image/')) {
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        fotoPreview.src = ev.target.result;
                        if (fotoPreviewContainer) {
                            fotoPreviewContainer.style.display = 'block';
                        }
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Vista previa de CVs (solo si son imágenes)
            function setupCvPreview(inputId, imgId, containerId) {
                const input = document.getElementById(inputId);
                const img = document.getElementById(imgId);
                const container = document.getElementById(containerId);
                if (!input || !img || !container) return;

                input.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (!file) {
                        container.style.display = 'none';
                        img.src = '#';
                        return;
                    }
                    if (!file.type.startsWith('image/')) {
                        container.style.display = 'none';
                        img.src = '#';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        img.src = ev.target.result;
                        container.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                });
            }

            setupCvPreview('cv_rescatista', 'cv_rescatista_preview', 'cv_rescatista_preview_container');
            setupCvPreview('cv_veterinario', 'cv_veterinario_preview', 'cv_veterinario_preview_container');

            // Mostrar/ocultar mapa de centros cuando se marca/desmarca el checkbox de cuidador
            const compromisoCheckbox = document.getElementById('compromiso_cuidador');
            const centroWrap = document.getElementById('centro_selection_wrap');
            if (compromisoCheckbox && centroWrap) {
                compromisoCheckbox.addEventListener('change', function() {
                    centroWrap.style.display = this.checked ? 'block' : 'none';
                    // Si se muestra el mapa, invalidar tamaño para que se renderice correctamente
                    if (this.checked && window.L) {
                        setTimeout(() => {
                            const map = window.L.map ? null : (window.cuidadorCentersMap || null);
                            if (map) {
                                map.invalidateSize();
                            }
                        }, 100);
                    }
                });
            }
        })();
    </script>
@endsection


