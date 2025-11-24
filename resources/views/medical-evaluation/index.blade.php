@extends('adminlte::page')

@section('template_title')
    Medical Evaluations
@endsection

@section('content')
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Medical Evaluations') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('medical-evaluation-transactions.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
								<th >Tratamiento</th>
								<th >Descripcion</th>
								<th >Fecha Revisión</th>
								<th >Veterinario</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medicalEvaluations as $medicalEvaluation)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
									<td >{{ $medicalEvaluation->treatmentType?->nombre }}</td>
									<td >{{ $medicalEvaluation->descripcion }}</td>
									<td >{{ $medicalEvaluation->fecha ? \Carbon\Carbon::parse($medicalEvaluation->fecha)->format('d-m-Y') : '' }}</td>
									<td >{{ $medicalEvaluation->veterinarian?->person?->nombre }}</td>

                                            <td>
                                                <form action="{{ route('medical-evaluations.destroy', $medicalEvaluation->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('medical-evaluations.show', $medicalEvaluation->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('medical-evaluations.edit', $medicalEvaluation->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $medicalEvaluations->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
