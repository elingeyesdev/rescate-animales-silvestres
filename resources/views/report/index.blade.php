@extends('adminlte::page')

@section('template_title')
    {{ __('Reports') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Reports') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('reports.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                    <th >Reportante</th>
                                    <th >Aprobado</th>
                                    <th >Imagen</th>
									<th >Observaciones</th>
									<th >Cantidad Animales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reports as $report)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
									<td >{{ $report->person?->nombre }}</td>
									<td >{{ (int)$report->aprobado === 1 ? 'Sí' : 'No' }}</td>
									<td >
                                            @if($report->imagen_url)
                                                <img src="{{ asset('storage/' . $report->imagen_url) }}" alt="img" style="height:50px; width:auto;"/>
                                            @endif
                                        </td>
									<td >{{ $report->observaciones ?: '-' }}</td>
										<td >{{ $report->cantidad_animales }}</td>

                                            <td>
                                                <form action="{{ route('reports.destroy', $report->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('reports.show', $report->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('reports.edit', $report->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $reports->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
