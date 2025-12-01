@extends('adminlte::page')

@section('template_title')
    People
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('People') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('people.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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

                    <div class="card-body bg-white">
                        <form method="GET" class="mb-2">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <label class="mb-1">{{ __('Nombre') }}</label>
                                    <input type="text" 
                                           name="nombre" 
                                           id="nombre" 
                                           class="form-control" 
                                           value="{{ request('nombre') }}" 
                                           placeholder="{{ __('Buscar por nombre') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-1">{{ __('Email') }}</label>
                                    <input type="text" 
                                           name="email" 
                                           id="email" 
                                           class="form-control" 
                                           value="{{ request('email') }}" 
                                           placeholder="{{ __('Buscar por email') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="mb-1">{{ __('CI') }}</label>
                                    <input type="text" 
                                           name="ci" 
                                           id="ci" 
                                           class="form-control" 
                                           value="{{ request('ci') }}" 
                                           placeholder="{{ __('Buscar por CI') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="mb-1">{{ __('Rol') }}</label>
                                    <select name="rol" id="rol" class="form-control">
                                        <option value="">{{ __('Todos los roles') }}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ request('rol') == $role->name ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="mb-1">{{ __('Es Cuidador') }}</label>
                                    <select name="es_cuidador" id="es_cuidador" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        <option value="1" {{ request('es_cuidador') == '1' ? 'selected' : '' }}>{{ __('Sí') }}</option>
                                        <option value="0" {{ request('es_cuidador') == '0' ? 'selected' : '' }}>{{ __('No') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary btn-sm mr-3">{{ __('Buscar') }}</button>
                                <a href="{{ route('people.index') }}" class="btn btn-link p-0">{{ __('Mostrar todos') }}</a>
                            </div>
                        </form>
                    </div>

                    <style>
                    .person-card .card-footer form { display: flex; width: 100%; }
                    .person-card .card-footer form > * { flex: 1 1 0; }
                    .person-card .card-footer form > * + * { margin-left: .5rem; }
                    .text-xs {
                        font-size: 0.75rem;
                        line-height: 1.2;
                    }
                    </style>

                    <div class="card-body pb-0" style="padding-top: 0.5rem;">
                        <div class="row">
                            @foreach ($people as $person)
                                @php
                                    $email = $person->user?->email ?? 'Sin email';
                                    $roleLabel = $person->highest_role ?? 'Sin rol';
                                    $fotoUrl = !empty($person->foto_path)
                                        ? asset('storage/' . $person->foto_path)
                                        : asset('storage/personas/persona.png');
                                    
                                    // Verificar si tiene solicitud pendiente
                                    $hasPendingRequest = false;
                                    // Rescatista pendiente
                                    if ($person->rescuers->whereNull('aprobado')->isNotEmpty()) {
                                        $hasPendingRequest = true;
                                    }
                                    // Veterinario pendiente
                                    if ($person->veterinarians->whereNull('aprobado')->isNotEmpty()) {
                                        $hasPendingRequest = true;
                                    }
                                    // Cuidador pendiente
                                    if ($person->es_cuidador && is_null($person->cuidador_motivo_revision)) {
                                        $hasPendingRequest = true;
                                    }
                                    
                                    // Ajustar tamaño del email si es muy largo
                                    $emailLength = strlen($email);
                                    $emailClass = $emailLength > 24 ? 'text-xs' : 'text-sm';
                                @endphp
                                <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
                                    <div class="card bg-light d-flex flex-fill person-card">
                                        <div class="card-header text-muted border-bottom-0 d-flex justify-content-between align-items-center" style="padding-right: {{  '1rem' }};">
                                            <span>{{ $roleLabel }}</span>
                                            @if($hasPendingRequest)
                                                <span class="badge badge-warning" style="font-size: 11px; padding: 4px 8px; margin-left: auto;">
                                                    Solicitud pendiente
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
                                                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-paw"></i></span> Cuidador: {{ (int)$person->es_cuidador === 1 ? 'Sí' : 'No' }}</li>
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
                                            <form action="{{ route('people.destroy', $person->id) }}" method="POST" class="mb-0 d-flex w-100">
                                                <a class="btn btn-sm btn-primary" href="{{ route('people.show', $person->id) }}">
                                                    <i class="fa fa-fw fa-eye"></i> {{ __('Show') }}
                                                </a>
                                                <a class="btn btn-sm btn-success" href="{{ route('people.edit', $person->id) }}">
                                                    <i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}
                                                </a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger js-confirm-delete">
                                                    <i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($people->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> {{ __('No se encontraron personas.') }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <nav aria-label="Contacts Page Navigation">
                            {!! $people->withQueryString()->links() !!}
                        </nav>
                    </div>
                    <!-- /.card-footer -->
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
