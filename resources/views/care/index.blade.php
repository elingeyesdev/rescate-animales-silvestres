@extends('adminlte::page')

@section('template_title')
    {{ __('Cares') }}
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Cares') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('animal-care-records.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                        @if($groupedCares->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> {{ __('No se encontraron cuidados de animales.') }}
                            </div>
                        @else
                            @foreach($groupedCares as $animalFileId => $cares)
                                @php
                                    $firstCare = $cares->first();
                                    $animalFile = $firstCare->animalFile;
                                    $animal = $animalFile?->animal;
                                    
                                    if ($animalFileId === 'sin_animal') {
                                        $animalName = 'Cuidados sin animal asignado';
                                        $animalImage = asset('storage/personas/persona.png');
                                        $species = '-';
                                        $status = '-';
                                        $showAnimalInfo = false;
                                    } else {
                                        $animalName = $animal?->nombre ?? ('Animal ' . ($animalFile?->animal_id ?? '-'));
                                        $animalImage = $animalFile?->imagen_url 
                                            ? asset('storage/' . $animalFile->imagen_url) 
                                            : asset('storage/personas/persona.png');
                                        $species = $animalFile?->species?->nombre ?? '-';
                                        $status = $animalFile?->animalStatus?->nombre ?? '-';
                                        $showAnimalInfo = true;
                                    }
                                @endphp
                                <div class="card card-outline card-primary mb-4">
                                    <div class="card-body">
                                        <div class="row align-items-start">
                                            {{-- Foto del animal a la izquierda --}}
                                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                                <img src="{{ $animalImage }}" 
                                                     alt="{{ $animalName }}" 
                                                     class="img-fluid rounded"
                                                     style="max-height: 200px; max-width: 100%; object-fit: cover;">
                                            </div>
                                            {{-- Información del animal a la derecha --}}
                                            <div class="col-md-9">
                                                <h5 class="mb-3">
                                                    <strong>{{ $animalName }}</strong>
                                                    @if($showAnimalInfo && $animalFile)
                                                        <span class="badge badge-info ml-2">Hoja N°{{ $animalFile->id }}</span>
                                                    @endif
                                                </h5>
                                                @if($showAnimalInfo)
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <small class="text-muted"><strong>Especie:</strong> {{ $species }}</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted"><strong>Estado:</strong> {{ $status }}</small>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                {{-- Tabla de cuidados --}}
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th class="text-center">Tipo de Cuidado</th>
                                                                <th class="text-center">Descripción</th>
                                                                <th class="text-center">Fecha</th>
                                                                <th class="text-center">Detalle</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($cares as $index => $care)
                                                                <tr>
                                                                    <td class="text-center">{{ $care->careType?->nombre ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        @if($care->descripcion)
                                                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $care->descripcion }}">
                                                                                {{ Str::limit($care->descripcion, 50) }}
                                                                            </span>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">{{ $care->fecha ? \Carbon\Carbon::parse($care->fecha)->format('d/m/Y') : '-' }}</td>
                                                                    <td class="text-center">
                                                                        <div class="btn-group btn-group-sm" role="group">
                                                                            <a class="btn btn-primary btn-sm" href="{{ route('cares.show', $care->id) }}" title="{{ __('Show') }}">
                                                                                <i class="fa fa-fw fa-eye"></i> Ver
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
