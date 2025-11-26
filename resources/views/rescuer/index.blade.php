@extends('adminlte::page')

@section('template_title')
    Rescuers
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Rescuers') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('rescuers.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
								<th >Persona</th>
								<th >CV</th>
								<th >Aprobado</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rescuers as $rescuer)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
									<td >{{ $rescuer->person?->nombre }}</td>
									<td >
                                                @if($rescuer->cv_documentado)
                                                    <a href="{{ asset('storage/' . $rescuer->cv_documentado) }}" target="_blank">Ver CV</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
									<td >{{ $rescuer->aprobado === null ? '-' : ($rescuer->aprobado ? 'Sí' : 'No') }}</td>

                                            <td>
                                                <form action="{{ route('rescuers.destroy', $rescuer->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('rescuers.show', $rescuer->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('rescuers.edit', $rescuer->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $rescuers->withQueryString()->links() !!}
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
