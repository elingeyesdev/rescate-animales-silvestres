@extends('adminlte::page')

@section('template_title')
    Health Records
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Health Records') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('health-records.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Tipo</th>
									<th >Descripcion</th>
									<th >Tratamiento</th>
									<th >Fecha Revision</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($healthRecords as $healthRecord)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $healthRecord->tipo }}</td>
										<td >{{ $healthRecord->descripcion }}</td>
										<td >{{ $healthRecord->tratamiento }}</td>
										<td >{{ $healthRecord->fecha_revision }}</td>

                                            <td>
                                                <form action="{{ route('health-records.destroy', $healthRecord->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('health-records.show', $healthRecord->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('health-records.edit', $healthRecord->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $healthRecords->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
