@extends('adminlte::page')

@section('template_title')
    {{ __('Registrar Animal') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Registrar Hoja de Vida del Animal') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <div class="font-weight-bold mb-1">{{ __('No se pudo registrar la hoja. Revisa los errores:') }}</div>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('animal-records.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <h5 class="mb-2">{{ __('Paso 1: Seleccione el hallazgo') }}</h5>
                                <div class="d-flex flex-wrap" id="report_cards">
                                    @foreach(($reportCards ?? []) as $rep)
                                        <div class="card m-2 report-card" data-report-id="{{ $rep->id }}" data-cond-id="{{ $rep->condicion_inicial_id }}" data-cond-name="{{ $rep->condicion_nombre }}" data-obs="{{ e($rep->observaciones) }}" style="width: 200px; cursor: pointer;">
                                            <div class="card-img-top mt-3" style="height:110px; overflow:hidden; display:flex; align-items:center; justify-content:center; ">
                                                @if(!empty($rep->imagen_url))
                                                    <img src="{{ asset('storage/'.$rep->imagen_url) }}" alt="#{{ $rep->id }}" style="max-height:100%; max-width:100%;">
                                                @else
                                                    <span class="text-muted small">{{ __('Sin imagen') }}</span>
                                                @endif
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="small font-weight-bold">Hallazgo N°{{ $rep->id }}</div>
                                                @if(!empty($rep->reportante_nombre))
                                                    <div class="small">{{ __('Reportante') }}: {{ $rep->reportante_nombre }}</div>
                                                @endif
                                                <!--<div class="small text-muted">{{ __('Asignados') }}: {{ $rep->asignados }}</div>
                                                {{-- <div class="small text-success">{{ __('Disp.') }}: {{ $available }}</div> --}}-->
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if((($reportCards ?? collect())->count() === 0) && (($reports ?? collect())->count() === 0))
                                    <div class="alert alert-info mt-2">{{ __('No hay hallazgos aprobados disponibles sin asignar. Cree o apruebe un hallazgo primero.') }}</div>
                                @endif
                                <button type="button" id="btn_continuar" class="btn btn-primary mt-2" disabled>{{ __('Continuar') }}</button>
                            </div>

                            <div id="step2" style="display:none;">
                                <hr class="mt-4 mb-3">
                                <h5 class="mb-3">{{ __('Paso 2: Complete la Hoja de Animal') }}</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div id="report_select_wrap">
                                            @include('animal.form', [
                                                'animal' => $animal ?? null,
                                                'reports' => $reports ?? [],
                                                'showSubmit' => false,
                                                'showName' => true,
                                                'hideReportSelect' => true,
                                                'hideInitialState' => true
                                            ])
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="estado_id" class="form-label">{{ __('Estado Actual') }}</label>
                                            <select name="estado_id" id="estado_id" class="form-control @error('estado_id') is-invalid @enderror">
                                                @foreach(collect($animalStatuses ?? [])->sortBy('nombre') as $st)
                                                    <option value="{{ $st->id }}" {{ (string)old('estado_id') === (string)$st->id ? 'selected' : ((empty(old('estado_id')) && (string)$defaultStatusId === (string)$st->id) ? 'selected' : '') }}>{{ $st->nombre }}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('estado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                        </div>

                                        
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="form-label">{{ __('Estado inicial del hallazgo') }}</label>
                                            <input type="text" class="form-control" id="estado_inicial_nombre" value="" readonly>
                                            <input type="hidden" name="estado_inicial_id" id="estado_inicial_id" value="">
                                        </div>
                                        @include('animal-file.form', [
                                            'animalFile' => $animalFile ?? null,
                                            'species' => $species ?? [],
                                            'animalStatuses' => $animalStatuses ?? [],
                                            'showAnimalSelect' => false,
                                            'showSubmit' => false,
                                            'hideState' => true
                                        ])
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3" id="save_wrap" style="display:none;">
                                <a href="{{ route('animal-files.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
                                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')

    <style>
        .report-card.active { border:2px solid #28a745; box-shadow: 0 0 0 2px rgba(40,167,69,.25); }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const select = document.getElementById('reporte_id');
        const cards = document.querySelectorAll('.report-card');
        const preview = document.getElementById('preview-animalfile-imagen');
        const btnNext = document.getElementById('btn_continuar');
        const step2 = document.getElementById('step2');
        const saveWrap = document.getElementById('save_wrap');
        const estadoSelect = document.getElementById('estado_id');
        const descField = document.getElementById('descripcion');
        let selectedAvailable = null;
        cards.forEach(card => {
            card.addEventListener('click', function(){
                const id = this.getAttribute('data-report-id');
                if (select) select.value = id;
                cards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                // intenta cargar la imagen del reporte como vista previa de la hoja
                const img = this.querySelector('img');
                if (img && preview) {
                    preview.src = img.src;
                    preview.style.display = '';
                }
                // setear estado inicial desde condición del reporte (por nombre + hidden id)
                const condName = this.getAttribute('data-cond-name') || '';
                const condId = this.getAttribute('data-cond-id') || '';
                const inicialLabel = document.getElementById('estado_inicial_nombre');
                const inicialId = document.getElementById('estado_inicial_id');
                if (inicialLabel) inicialLabel.value = condName || '';
                if (inicialId) inicialId.value = condId || '';
                if (estadoSelect && condName) {
                    // buscar opción cuyo texto coincida con el nombre de la condición
                    const opts = Array.from(estadoSelect.options || []);
                    const found = opts.find(o => String(o.textContent || '').trim().toLowerCase() === condName.toLowerCase());
                    if (found) {
                        estadoSelect.value = found.value;
                    }
                }
                // prellenar descripción con observaciones del reporte si no hay valor
                const obs = this.getAttribute('data-obs') || '';
                if (descField && (!descField.value || descField.value.trim() === '')) {
                    descField.value = obs;
                }
                if (btnNext) btnNext.disabled = false;
            });
        });
        // next step
        btnNext?.addEventListener('click', function(){
            if (!select?.value) return;
            step2.style.display = '';
            if (saveWrap) saveWrap.style.display = '';
            this.disabled = true;
            this.textContent = '{{ __('Seleccionado') }}';
            step2.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
    </script>
@endsection


