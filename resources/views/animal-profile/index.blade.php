@extends('adminlte::page')

@section('template_title')
    Animal Profiles
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Animal Profiles') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('animal-profiles.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Estado Salud</th>
									<th >Sexo</th>
									<th >Especie</th>
									<th >Raza</th>
									<th >Alimentacion</th>
									<th >Frecuencia</th>
									<th >Cantidad</th>
									<th >Color</th>
									<th >Imagen</th>
									<th >Reporte Id</th>
									<th >Detalle</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($animalProfiles as $animalProfile)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $animalProfile->estado_salud }}</td>
										<td >{{ $animalProfile->sexo }}</td>
										<td >{{ $animalProfile->especie }}</td>
										<td >{{ $animalProfile->raza }}</td>
										<td >{{ $animalProfile->alimentacion }}</td>
										<td >{{ $animalProfile->frecuencia }}</td>
										<td >{{ $animalProfile->cantidad }}</td>
										<td >{{ $animalProfile->color }}</td>
										<td >{{ $animalProfile->imagen }}</td>
										<td >{{ $animalProfile->reporte_id }}</td>
										<td >{{ $animalProfile->detalle }}</td>

                                            <td>
                                                <form action="{{ route('animal-profiles.destroy', $animalProfile->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('animal-profiles.show', $animalProfile->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('animal-profiles.edit', $animalProfile->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $animalProfiles->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
