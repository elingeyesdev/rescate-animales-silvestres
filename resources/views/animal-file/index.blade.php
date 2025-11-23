@extends('adminlte::page')

@section('template_title')
    {{ __('Animal Files') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Animal Files') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('animal-files.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        
                                    <th >Nombre del animal</th>
                                    <th >Sexo</th>
                                    <th >Tipo</th>
                                    <th >Reporte</th>
                                    <th >Especie</th>
                                    <th >Imagen</th>
                                    <th >Raza</th>
                                    <th >Estado</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($animalFiles as $animalFile)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
                                            <td >{{ $animalFile->animal?->nombre }}</td>
                                            <td >{{ $animalFile->animal?->sexo }}</td>
                                            <td >{{ $animalFile->animalType?->nombre }}</td>
                                            <td >{{ $animalFile->animal?->report?->id ? '#' . $animalFile->animal?->report?->id : '' }}</td>
                                            <td >{{ $animalFile->species?->nombre }}</td>
                                            <td >
                                                @if($animalFile->imagen_url)
                                                    <img src="{{ asset('storage/' . $animalFile->imagen_url) }}" alt="img" style="height:50px; width:auto;"/>
                                                @endif
                                            </td>
                                            <td >{{ $animalFile->breed?->nombre }}</td>
                                            <td >{{ $animalFile->animalStatus?->nombre }}</td>

                                            <td>
                                                <form action="{{ route('animal-files.destroy', $animalFile->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('animal-files.show', $animalFile->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('animal-files.edit', $animalFile->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
                                                </form
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $animalFiles->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
