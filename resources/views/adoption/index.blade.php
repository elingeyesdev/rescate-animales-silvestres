@extends('adminlte::page')

@section('template_title')
    {{ __('Adoptions') }}
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Adoptions') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('adoptions.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Direccion</th>
									<th >Latitud</th>
									<th >Longitud</th>
									<th >Detalle</th>
									<th >Aprobada</th>
								<th >Adoptante</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($adoptions as $adoption)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $adoption->direccion }}</td>
										<td >{{ $adoption->latitud }}</td>
										<td >{{ $adoption->longitud }}</td>
										<td >{{ $adoption->detalle }}</td>
									<td >{{ (int)$adoption->aprobada === 1 ? 'Sí' : 'No' }}</td>
									<td >{{ $adoption->adopter?->nombre ?? $adoption->adoptante_id }}</td>

                                            <td>
                                                <form action="{{ route('adoptions.destroy', $adoption->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('adoptions.show', $adoption->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('adoptions.edit', $adoption->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $adoptions->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@include('partials.page-pad')
@endsection
