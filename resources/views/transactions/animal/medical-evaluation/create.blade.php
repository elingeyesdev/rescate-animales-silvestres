@extends('adminlte::page')

@section('template_title')
    {{ __('Registrar Evaluación Médica (Transaccional)') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
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
                                    <input type="hidden" name="animal_file_id" id="animal_file_id" value="{{ old('animal_file_id') }}">
                                    <div class="mb-3">
                                        <h5 class="mb-2">{{ __('Paso 1: Seleccione la Hoja de Animal') }}</h5>
                                        <div class="d-flex flex-wrap" id="af_cards">
                                            @foreach(($afCards ?? []) as $card)
                                                <div class="card m-2 af-card" data-af-id="{{ $card['id'] }}" style="width: 200px; cursor: pointer;">
                                                    <div class="card-img-top mt-3" style="height:110px; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                                                        @if(!empty($card['img']))
                                                            <img src="{{ $card['img'] }}" alt="#{{ $card['id'] }}" style="max-height:100%; max-width:100%;">
                                                        @else
                                                            <span class="text-muted small">{{ __('Sin imagen') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="small font-weight-bold">#{{ $card['id'] }} {{ $card['name'] }}</div>
                                                        @if(!empty($card['reporter']))
                                                            <div class="small">{{ __('Reportante') }}: {{ $card['reporter'] }}</div>
                                                        @endif
                                                        <div class="small text-muted">{{ __('Estado') }}: {{ $card['status'] ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" id="btn_continuar" class="btn btn-primary mt-2" disabled>{{ __('Continuar') }}</button>
                                        {!! $errors->first('animal_file_id', '<div class="text-danger small mt-1" role="alert"><strong>:message</strong></div>') !!}
                                    </div>

                                    <div id="step2" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group mb-2 mb20">
                                                <label class="form-label d-block">{{ __('Foto actual') }}</label>
                                                <img id="header_current_img" src="" alt="Foto actual" style="max-height:120px; border-radius:4px;">
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group mb-2 mb20">
                                                <label class="form-label">{{ __('Animal') }}</label>
                                                <div id="header_animal_name">-</div>
                                            </div>
                                            <div class="form-group mb-2 mb20">
                                                <label class="form-label">{{ __('Estado actual') }}</label>
                                                <div id="header_current_status">-</div>
                                            </div>
                                            <div class="form-group mb-2 mb20">
                                                <label class="form-label">{{ __('Última actualización (no médica)') }}</label>
                                                <div id="header_last_update">-</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <label class="form-label">{{ __('Últimas evaluaciones médicas') }}</label>
                                        <div id="last_evals_container">
                                            <div class="text-muted">-</div>
                                        </div>
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
                                                <div class="custom-file">
                                                    <input type="file" accept="image/*" name="imagen" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen">
                                                    <label class="custom-file-label" for="imagen" data-browse="Subir">Subir la imagen del animal</label>
                                                </div>
                                                {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
                                                <div class="mt-2">
                                                    <img id="tx-preview-eval-imagen" src="" alt="Evidencia seleccionada" style="max-height:120px; display:none;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- La fecha se asigna automáticamente en el servidor (UTC-4). --}}

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-2 mb20">
                                                <label for="diagnostico" class="form-label">{{ __('Diagnóstico') }}</label>
                                                <textarea name="diagnostico" id="diagnostico" class="form-control @error('diagnostico') is-invalid @enderror" rows="2">{{ old('diagnostico') }}</textarea>
                                                {!! $errors->first('diagnostico', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-2 mb20">
                                                <label class="form-label d-block">{{ __('¿Apto para traslado inmediato?') }}</label>
                                                <div class="icheck-primary d-inline mr-3">
                                                    <input type="radio" id="apto_si" name="apto_traslado" value="si" {{ old('apto_traslado')==='si'?'checked':'' }}>
                                                    <label for="apto_si">{{ __('Sí') }}</label>
                                                </div>
                                                <div class="icheck-primary d-inline mr-3">
                                                    <input type="radio" id="apto_no" name="apto_traslado" value="no" {{ old('apto_traslado')==='no'?'checked':'' }}>
                                                    <label for="apto_no">{{ __('No') }}</label>
                                                </div>
                                                <div class="icheck-primary d-inline">
                                                    <input type="radio" id="apto_restr" name="apto_traslado" value="con_restricciones" {{ old('apto_traslado')==='con_restricciones'?'checked':'' }}>
                                                    <label for="apto_restr">{{ __('Con restricciones') }}</label>
                                                </div>
                                                {!! $errors->first('apto_traslado', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card card-outline card-secondary">
                                        <div class="card-header" id="optHeader" style="cursor:pointer;">
                                            <h3 class="card-title">{{ __('Campos opcionales') }}</h3>
                                        </div>
                                        <div class="card-body" id="optBody" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-2 mb20">
                                                        <label for="tratamiento_texto" class="form-label">{{ __('Detalles adicionales') }}</label>
                                                        <textarea name="tratamiento_texto" id="tratamiento_texto" class="form-control @error('tratamiento_texto') is-invalid @enderror" rows="2">{{ old('tratamiento_texto') }}</textarea>
                                                        {!! $errors->first('tratamiento_texto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mb-2 mb20">
                                                        <label for="peso" class="form-label">{{ __('Peso (kg)') }}</label>
                                                        <input type="number" step="0.01" min="0" name="peso" id="peso" class="form-control @error('peso') is-invalid @enderror" value="{{ old('peso') }}">
                                                        {!! $errors->first('peso', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mb-2 mb20">
                                                        <label for="temperatura" class="form-label">{{ __('Temperatura (°C)') }}</label>
                                                        <input type="number" step="0.1" min="0" name="temperatura" id="temperatura" class="form-control @error('temperatura') is-invalid @enderror" value="{{ old('temperatura') }}">
                                                        {!! $errors->first('temperatura', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-2 mb20">
                                                        <label for="recomendacion" class="form-label">{{ __('Recomendación / siguiente acción') }}</label>
                                                        <select name="recomendacion" id="recomendacion" class="form-control @error('recomendacion') is-invalid @enderror">
                                                            <option value="">{{ __('Seleccione') }}</option>
                                                            <option value="traslado" {{ old('recomendacion')==='traslado'?'selected':'' }}>{{ __('Traslado a otro centro') }}</option>
                                                            <option value="observacion_24h" {{ old('recomendacion')==='observacion_24h'?'selected':'' }}>{{ __('Observación 24h') }}</option>
                                                            <option value="nueva_revision" {{ old('recomendacion')==='nueva_revision'?'selected':'' }}>{{ __('Nueva revisión médica') }}</option>
                                                            <option value="tratamiento_prolongado" {{ old('recomendacion')==='tratamiento_prolongado'?'selected':'' }}>{{ __('Tratamiento prolongado') }}</option>
                                                        </select>
                                                        {!! $errors->first('recomendacion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                                    </div>
                                                </div>
                                               
                                            </div>
                                        </div>
                                    </div>

                                    </div> {{-- end step2 --}}

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
                                      }
                                      vetSel?.addEventListener('change', updateVetHint);
                                      updateVetHint();

                                      // Paso 1: selección por cards
                                      const cards = document.querySelectorAll('.af-card');
                                      const hiddenId = document.getElementById('animal_file_id');
                                      const btnNext = document.getElementById('btn_continuar');
                                      const step2 = document.getElementById('step2');
                                      const headerImg = document.getElementById('header_current_img');
                                      const headerName = document.getElementById('header_animal_name');
                                      const headerStatus = document.getElementById('header_current_status');
                                      const headerLast = document.getElementById('header_last_update');
                                      const lastEvalsCont = document.getElementById('last_evals_container');
                                      const submitWrap = document.getElementById('submit_wrap');
                                      const afMeta = @json($afMeta);
                                      const lastData = @json($lastDataByAnimalFile ?? []);
                                      function formatDateYmd(dStr){
                                        try {
                                          const d = new Date(dStr);
                                          const dd = String(d.getDate()).padStart(2,'0');
                                          const mm = String(d.getMonth()+1).padStart(2,'0');
                                          const yy = String(d.getFullYear());
                                          return `${dd}/${mm}/${yy}`;
                                        } catch(e){ return dStr || ''; }
                                      }
                                      function applyMeta(id){
                                        const meta = afMeta[id] || {};
                                        const last = (lastData[id]?.lastHistory) || null;
                                        const evals = (lastData[id]?.lastEvaluations) || [];
                                        if (headerImg) headerImg.src = meta.img || '';
                                        if (headerName) headerName.textContent = meta.name || '-';
                                        if (headerStatus) headerStatus.textContent = meta.status || '-';
                                        if (headerLast) {
                                          if (last && last.changed_at) {
                                            headerLast.textContent = formatDateYmd(last.changed_at);
                                          } else {
                                            headerLast.textContent = '-';
                                          }
                                        }
                                        if (lastEvalsCont) {
                                          lastEvalsCont.innerHTML = '';
                                          const n = evals.length;
                                          const titleEl = lastEvalsCont.previousElementSibling;
                                          if (titleEl && titleEl.tagName === 'LABEL') {
                                            titleEl.innerHTML = `{{ __('Últimas evaluaciones médicas') }}${n === 1 ? ' (1 evaluación)' : (n > 1 ? ` (${n} evaluaciones)` : ' (0)')}`;
                                          }
                                          if (n === 0) {
                                            lastEvalsCont.innerHTML = '<div class="text-muted">-</div>';
                                          } else {
                                            evals.forEach((e) => {
                                              const wrap = document.createElement('div');
                                              wrap.className = 'card card-outline card-secondary mb-1';
                                              const hdr = document.createElement('div');
                                              hdr.className = 'card-header';
                                              hdr.style.cursor = 'pointer';
                                              hdr.textContent = `${formatDateYmd(e.fecha) || ''}`;
                                              const body = document.createElement('div');
                                              body.className = 'card-body';
                                              body.style.display = 'none';
                                              body.innerHTML = `
                                                ${e.diagnostico ? ('<div><strong>Dx:</strong> ' + e.diagnostico + '</div>') : ''}
                                                ${e.descripcion ? ('<div><strong>Desc:</strong> ' + e.descripcion + '</div>') : ''}
                                                ${e.tratamiento_texto ? ('<div><strong>Tratamiento:</strong> ' + e.tratamiento_texto + '</div>') : ''}
                                              `;
                                              hdr.addEventListener('click', () => {
                                                body.style.display = body.style.display === 'none' ? '' : 'none';
                                              });
                                              wrap.appendChild(hdr);
                                              wrap.appendChild(body);
                                              lastEvalsCont.appendChild(wrap);
                                            });
                                          }
                                        }
                                      }
                                      cards.forEach(card => {
                                        card.addEventListener('click', function(){
                                          const id = this.getAttribute('data-af-id');
                                          if (hiddenId) hiddenId.value = id;
                                          cards.forEach(c => c.classList.remove('active'));
                                          this.classList.add('active');
                                          applyMeta(id);
                                          if (btnNext) btnNext.disabled = false;
                                        });
                                      });
                                      function revealStep2(){
                                        if (step2) step2.style.display = '';
                                        if (submitWrap) submitWrap.style.display = '';
                                      }
                                      btnNext?.addEventListener('click', function(){
                                        if (!hiddenId?.value) return;
                                        revealStep2();
                                        this.disabled = true;
                                        this.textContent = '{{ __('Seleccionado') }}';
                                        step2.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                      });
                                      // Mostrar paso 2 automáticamente si ya venimos con animal_file_id (post-validación)
                                      if (hiddenId && hiddenId.value) {
                                        revealStep2();
                                      }
                                    });
                                    </script>
                                    <script>
                                    document.addEventListener('DOMContentLoaded', function(){
                                        const hdr = document.getElementById('optHeader');
                                        const body = document.getElementById('optBody');
                                        hdr?.addEventListener('click', function(){
                                            if (!body) return;
                                            body.style.display = body.style.display === 'none' ? '' : 'none';
                                        });
                                    });
                                    </script>

                                    <!-- Observaciones: no necesarias en transaccional -->
                                </div> {{-- /.col-12 --}}

                                <div class="col-md-12 mt20 mt-2" id="submit_wrap" style="display:none;">
                                    <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.custom-file')
    @include('partials.page-pad')
    <style>.af-card.active{ border:2px solid #28a745; box-shadow:0 0 0 2px rgba(40,167,69,.25); }</style>
@endsection


