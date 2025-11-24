@extends('adminlte::page')

@section('template_title')
    {{ __('Registrar Animal (Transaccional)') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Registrar Animal') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('animal-records.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div id="report_select_wrap">
                                        @include('animal.form', [
                                            'animal' => $animal ?? null,
                                            'reports' => $reports ?? [],
                                            'showSubmit' => false
                                        ])
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label d-block">{{ __('Seleccione el reporte de origen') }}</label>
                                        <div class="d-flex flex-wrap" id="report_cards">
                                            @foreach(($reportCards ?? []) as $rep)
                                                <div class="card m-2 report-card" data-report-id="{{ $rep->id }}" style="width: 140px; cursor: pointer;">
                                                    <div class="card-img-top" style="height:100px; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#f7f7f7;">
                                                        @if(!empty($rep->imagen_url))
                                                            <img src="{{ asset('storage/'.$rep->imagen_url) }}" alt="#{{ $rep->id }}" style="max-height:100%; max-width:100%;">
                                                        @else
                                                            <span class="text-muted small">{{ __('Sin imagen') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="small">#{{ $rep->id }}</div>
                                                        <div class="small text-muted">{{ __('Reportados') }}: {{ $rep->cantidad_animales }}</div>
                                                        <div class="small text-muted">{{ __('Asignados') }}: {{ $rep->asignados }}</div>
                                                        <div class="small text-success">{{ __('Disp.') }}: {{ max(0, ($rep->cantidad_animales - $rep->asignados)) }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if((($reportCards ?? collect())->count() === 0) && (($reports ?? collect())->count() === 0))
                                            <div class="alert alert-info mt-2">{{ __('No hay hallazgos aprobados con cupo disponibles. Cree o apruebe un hallazgo primero.') }}</div>
                                        @endif

                                        <div class="form-group mt-2">
                                            <label for="llegaron_cantidad" class="form-label">{{ __('Cantidad llegada (confirmación)') }}</label>
                                            <input type="number" min="1" class="form-control" id="llegaron_cantidad" name="llegaron_cantidad" value="{{ old('llegaron_cantidad', 1) }}" style="max-width: 200px;">
                                            <small class="text-muted">{{ __('Ingrese la cantidad de animales que efectivamente llegaron de este hallazgo (para confirmar disponibilidad).') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @include('animal-file.form', [
                                        'animalFile' => $animalFile ?? null,
                                        'animalTypes' => $animalTypes ?? [],
                                        'species' => $species ?? [],
                                        'animalStatuses' => $animalStatuses ?? [],
                                        'showAnimalSelect' => false,
                                        'showSubmit' => false
                                    ])
                                </div>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('animal-files.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
                                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .report-card.active { border:2px solid #28a745; box-shadow: 0 0 0 2px rgba(40,167,69,.25); }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const select = document.getElementById('reporte_id');
        const cards = document.querySelectorAll('.report-card');
        cards.forEach(card => {
            card.addEventListener('click', function(){
                const id = this.getAttribute('data-report-id');
                if (select) select.value = id;
                cards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
    </script>
@endsection


