@extends('adminlte::page')

@section('template_title')
    {{ __('Historial de Animales') }}
@endsection

@section('content')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Historial de Animales') }}
                            </span>
                            <form method="get" class="form-inline">
                                <label for="order" class="mr-2">{{ __('Orden') }}</label>
                                <select name="order" id="order" class="form-control" onchange="this.form.submit()">
                                    @php $ord = request()->get('order'); @endphp
                                    <option value="desc" {{ $ord!=='asc'?'selected':'' }}>{{ __('Más nuevo primero') }}</option>
                                    <option value="asc" {{ $ord==='asc'?'selected':'' }}>{{ __('Más viejo primero') }}</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                <tr>
                                    <th>No</th>
                                    <th>Hoja</th>
                                    <th>Animal</th>
                                    <th>Fecha de cambio</th>
                                    <th>Resumen</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($histories as $h)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>#{{ $h->animal_file_id }}</td>
                                        <td>{{ $h->animalFile?->animal?->nombre ?? '-' }}</td>
                                        <td>{{ $h->changed_at ? \Carbon\Carbon::parse($h->changed_at)->format('d/m/Y H:i') : '' }}</td>
                                        <td>
                                            @php
                                                $desc = data_get($h->valores_nuevos, 'care.descripcion');
                                                $obsText = is_array($h->observaciones ?? null) ? ($h->observaciones['texto'] ?? null) : ($h->observaciones ?? null);
                                            @endphp
                                            {{ $desc ? \Illuminate\Support\Str::limit($desc, 60) : ($obsText ? \Illuminate\Support\Str::limit($obsText, 60) : '-') }}
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="{{ route('animal-histories.show', $h->id) }}">
                                                <i class="fa fa-fw fa-eye"></i> {{ __('Show') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $histories->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection


@include('partials.page-pad')

