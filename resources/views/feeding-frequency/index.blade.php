@extends('adminlte::page')

@section('template_title')
    {{ __('Feeding Frequencies') }}
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Feeding Frequencies') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('feeding-frequencies.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
									<th >Descripcion</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($feedingFrequencies as $feedingFrequency)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $feedingFrequency->nombre }}</td>
										<td >{{ $feedingFrequency->descripcion }}</td>

                                            <td>
                                                <form action="{{ route('feeding-frequencies.destroy', $feedingFrequency->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('feeding-frequencies.show', $feedingFrequency->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('feeding-frequencies.edit', $feedingFrequency->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $feedingFrequencies->withQueryString()->links() !!}
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
