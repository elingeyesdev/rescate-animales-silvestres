@extends('adminlte::page')

@section('template_title')
    {{ __('Registrar Evaluación Médica (Transaccional)') }}
@endsection

@section('content')
    <section class="content container-fluid mt-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Evaluación Médica + Cambio de Estado + Historial') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('medical-evaluation-transactions.store') }}" role="form" enctype="multipart/form-data">
                            @csrf

                            <div class="row padding-1 p-1">
                                <div class="col-md-12">
                                    <div class="form-group mb-2 mb20">
                                        <label for="animal_file_id" class="form-label">{{ __('Hoja de Animal') }}</label>
                                        <select name="animal_file_id" id="animal_file_id" class="form-control @error('animal_file_id') is-invalid @enderror" aria-describedby="arrival_info_hint">
                                            <option value="">{{ __('Seleccione') }}</option>
                                            @foreach(($animalFiles ?? []) as $af)
                                                <option value="{{ $af->id }}" 
                                                    data-estado="{{ $af->animalStatus?->nombre }}"
                                                    data-rep-img="{{ $af->animal?->report?->imagen_url }}"
                                                    data-rep-obs="{{ $af->animal?->report?->observaciones }}"
                                                    {{ (string)old('animal_file_id') === (string)$af->id ? 'selected' : '' }}>
                                                    #{{ $af->id }} {{ $af->animal?->nombre ? '- ' . $af->animal->nombre : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small id="arrival_info_hint" class="form-text text-muted"></small>
                                        {!! $errors->first('animal_file_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <label class="form-label">{{ __('Estado anterior (reporte)') }}</label>
                                        <div id="prev_report_text">-</div>
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <label class="form-label">{{ __('Imagen de llegada (reporte)') }}</label>
                                        <div class="mt-2">
                                            <a id="arrival_img_link" href="#" target="_blank" rel="noopener" style="display:none;">
                                                <img id="arrival_img" src="" alt="Imagen de llegada" style="max-height:120px;">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <label class="form-label">{{ __('Estado al llegar (hoja)') }}</label>
                                        <div id="arrival_status_text">-</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-2 mb20">
                                                <label for="tratamiento_id" class="form-label">{{ __('Tipo de Tratamiento') }}</label>
                                                <select name="tratamiento_id" id="tratamiento_id" class="form-control @error('tratamiento_id') is-invalid @enderror">
                                                    <option value="">{{ __('Seleccione') }}</option>
                                                    @foreach(($treatmentTypes ?? []) as $t)
                                                        <option value="{{ $t->id }}" {{ (string)old('tratamiento_id') === (string)$t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('tratamiento_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-2 mb20">
                                                <label for="veterinario_id" class="form-label">{{ __('Veterinario') }}</label>
                                                <select name="veterinario_id" id="veterinario_id" class="form-control @error('veterinario_id') is-invalid @enderror" aria-describedby="vet_tx_specialty_hint">
                                                    <option value="">{{ __('Seleccione') }}</option>
                                                    @foreach(($veterinarians ?? []) as $v)
                                                        <option value="{{ $v->id }}" data-especialidad="{{ $v->especialidad }}" {{ (string)old('veterinario_id') === (string)$v->id ? 'selected' : '' }}>
                                                            #{{ $v->id }} {{ $v->person?->nombre ?? '' }}@if($v->especialidad) ({{ $v->especialidad }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small id="vet_tx_specialty_hint" class="form-text text-muted"></small>
                                                {!! $errors->first('veterinario_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-2 mb20">
                                                <label for="estado_id" class="form-label">{{ __('Nuevo Estado del Animal') }}</label>
                                                <select name="estado_id" id="estado_id" class="form-control @error('estado_id') is-invalid @enderror">
                                                    <option value="">{{ __('Seleccione') }}</option>
                                                    @foreach(($statuses ?? []) as $s)
                                                        <option value="{{ $s->id }}" {{ (string)old('estado_id') === (string)$s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('estado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-2 mb20">
                                                <label for="imagen" class="form-label">{{ __('Evidencia (imagen)') }}</label>
                                                <input type="file" accept="image/*" name="imagen" class="form-control @error('imagen') is-invalid @enderror" id="imagen">
                                                {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
                                                <div class="mt-2">
                                                    <img id="tx-preview-eval-imagen" src="" alt="Evidencia seleccionada" style="max-height:120px; display:none;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- La fecha se asigna automáticamente en el servidor (UTC-4). --}}

                                    <div class="form-group mb-2 mb20">
                                        <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
                                        <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion') }}</textarea>
                                        {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>

                                    <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                      let currentObjectURL = null;
                                      const input = document.getElementById('imagen');
                                      const preview = document.getElementById('tx-preview-eval-imagen');
                                      input?.addEventListener('change', function(){
                                        const file = this.files && this.files[0];
                                        if (file && file.type && file.type.startsWith('image/')) {
                                          if (currentObjectURL) URL.revokeObjectURL(currentObjectURL);
                                          currentObjectURL = URL.createObjectURL(file);
                                          if (preview) {
                                            preview.src = currentObjectURL;
                                            preview.style.display = '';
                                          }
                                        } else {
                                          if (currentObjectURL) { URL.revokeObjectURL(currentObjectURL); currentObjectURL = null; }
                                          if (preview) { preview.removeAttribute('src'); preview.style.display = 'none'; }
                                        }
                                      });

                                      const vetSel = document.getElementById('veterinario_id');
                                      const vetHint = document.getElementById('vet_tx_specialty_hint');
                                      function updateVetHint(){
                                        const opt = vetSel?.selectedOptions?.[0];
                                        const esp = opt ? (opt.getAttribute('data-especialidad') || '') : '';
                                        if (vetHint) vetHint.textContent = esp ? ('Especialidad: ' + esp) : '';
                                      }
                                      vetSel?.addEventListener('change', updateVetHint);
                                      updateVetHint();

                                      const afSel = document.getElementById('animal_file_id');
                                      const prevText = document.getElementById('prev_report_text');
                                      const arrText = document.getElementById('arrival_status_text');
                                      const imgLink = document.getElementById('arrival_img_link');
                                      const img = document.getElementById('arrival_img');
                                      function updateArrival(){
                                        const opt = afSel?.selectedOptions?.[0];
                                        if (!opt) return;
                                        const repObs = opt.getAttribute('data-rep-obs') || '';
                                        const estado = opt.getAttribute('data-estado') || '';
                                        const repImg = opt.getAttribute('data-rep-img') || '';
                                        if (prevText) prevText.textContent = repObs || '-';
                                        if (arrText) arrText.textContent = estado || '-';
                                        if (repImg) {
                                          const url = '{{ asset('storage') }}/' + repImg;
                                          imgLink.style.display = '';
                                          imgLink.href = url;
                                          img.src = url;
                                        } else {
                                          imgLink.style.display = 'none';
                                          img.removeAttribute('src');
                                        }
                                      }
                                      afSel?.addEventListener('change', updateArrival);
                                      updateArrival();
                                    });
                                    </script>

                                    <!-- Observaciones: no necesarias en transaccional -->
                                </div>

                                <div class="col-md-12 mt20 mt-2">
                                    <button type="submit" class="btn btn-primary">{{ __('Guardar transacción') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


