@extends('adminlte::page')

@section('template_title')
    Animal Files
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
                                        
									<th >Nombre</th>
									<th >Tipo</th>
									<th >Tipo Id</th>
									<th >Reporte Id</th>
									<th >Especie Id</th>
									<th >Raza Id</th>
									<th >Estado Id</th>
									<th >Adopcion Id</th>
									<th >Liberacion Id</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($animalFiles as $animalFile)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $animalFile->nombre }}</td>
										<td >{{ $animalFile->tipo }}</td>
										<td >{{ $animalFile->tipo_id }}</td>
										<td >{{ $animalFile->reporte_id }}</td>
										<td >{{ $animalFile->especie_id }}</td>
										<td >{{ $animalFile->raza_id }}</td>
										<td >{{ $animalFile->estado_id }}</td>
										<td >{{ $animalFile->adopcion_id }}</td>
										<td >{{ $animalFile->liberacion_id }}</td>

                                            <td>
                                                <form action="{{ route('animal-files.destroy', $animalFile->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('animal-files.show', $animalFile->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('animal-files.edit', $animalFile->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
                                                </form>
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
