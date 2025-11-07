@extends('adminlte::page')

@section('template_title')
    {{ __('Cares') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Cares') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('cares.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
								<th >Animal</th>
								<th >Tipo de Cuidado</th>
									<th >Descripcion</th>
								<th >Fecha</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cares as $care)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
									<td >{{ $care->animalFile?->nombre }}</td>
									<td >{{ $care->careType?->nombre }}</td>
										<td >{{ $care->descripcion }}</td>
									<td >{{ $care->fecha ? \Carbon\Carbon::parse($care->fecha)->format('d-m-Y') : '' }}</td>

                                            <td>
                                                <form action="{{ route('cares.destroy', $care->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('cares.show', $care->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('cares.edit', $care->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('¿Estás seguro de querer eliminar?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $cares->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
