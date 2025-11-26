@extends('adminlte::page')

@section('template_title')
    Veterinarians
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Veterinarians') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('veterinarians.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Especialidad</th>
								<th >CV</th>
								<th >Aprobado</th>
								<th >Persona</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($veterinarians as $veterinarian)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $veterinarian->especialidad }}</td>
									<td >
                                                @if($veterinarian->cv_documentado)
                                                    <a href="{{ asset('storage/' . $veterinarian->cv_documentado) }}" target="_blank">Ver CV</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
									<td >{{ $veterinarian->aprobado === null ? '-' : ($veterinarian->aprobado ? 'Sí' : 'No') }}</td>
									<td >{{ $veterinarian->person?->nombre }}</td>

                                            <td>
                                                <form action="{{ route('veterinarians.destroy', $veterinarian->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('veterinarians.show', $veterinarian->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('veterinarians.edit', $veterinarian->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $veterinarians->withQueryString()->links() !!}
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
