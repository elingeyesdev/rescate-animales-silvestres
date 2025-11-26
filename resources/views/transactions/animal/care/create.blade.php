@extends('adminlte::page')

@section('template_title')
    {{ __('Registrar Cuidado (Transaccional)') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Registrar Cuidado + Historial') }}</span>
                    </div>
                    <form method="POST" action="{{ route('animal-care-records.store') }}" role="form" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body bg-white">
                            <div class="row padding-1 p-1">
                                <div class="col-md-6">
                                    <div class="form-group mb-2 mb20">
                                        <label for="animal_file_id" class="form-label">{{ __('Hoja de Animal') }}</label>
                                        <select name="animal_file_id" id="animal_file_id" class="form-control @error('animal_file_id') is-invalid @enderror">
                                            <option value="">{{ __('Seleccione') }}</option>
                                            @foreach(($animalFiles ?? []) as $af)
                                                <option value="{{ $af->id }}"
                                                    data-rep-img="{{ $af->animal?->report?->imagen_url }}"
                                                    data-rep-obs="{{ $af->animal?->report?->observaciones }}"
                                                    {{ (string)old('animal_file_id') === (string)$af->id ? 'selected' : '' }}>#{{ $af->id }} {{ $af->animal?->nombre ? '- ' . $af->animal->nombre : '' }}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('animal_file_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <label class="form-label">{{ __('Estado anterior (reporte)') }}</label>
                                        <div id="care_prev_report_text">-</div>
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <label class="form-label">{{ __('Imagen de llegada (reporte)') }}</label>
                                        <div class="mt-2">
                                            <a id="care_arrival_img_link" href="#" target="_blank" rel="noopener" style="display:none;">
                                                <img id="care_arrival_img" src="" alt="Imagen de llegada" style="max-height:120px;">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2 mb20">
                                        <label for="tipo_cuidado_id" class="form-label">{{ __('Tipo de Cuidado') }}</label>
                                        <select name="tipo_cuidado_id" id="tipo_cuidado_id" class="form-control @error('tipo_cuidado_id') is-invalid @enderror">
                                            <option value="">{{ __('Seleccione') }}</option>
                                            @foreach(($careTypes ?? []) as $t)
                                                <option value="{{ $t->id }}" {{ (string)old('tipo_cuidado_id') === (string)$t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('tipo_cuidado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                </div>

                                <!-- Fecha: se asigna automáticamente por el sistema -->
                                <div class="col-md-6">
                                    <div class="form-group mb-2 mb20">
                                        <label for="imagen" class="form-label">{{ __('Evidencia (imagen)') }}</label>
                                        <input type="file" accept="image/*" name="imagen" class="form-control @error('imagen') is-invalid @enderror" id="imagen">
                                        {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
                                        <div class="mt-2">
                                            <img id="care_preview_imagen" src="" alt="Evidencia seleccionada" style="max-height:120px; display:none;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-2 mb20">
                                        <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
                                        <input type="text" name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion') }}">
                                        {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                </div>

                                <!-- Observaciones: no necesarias en transaccional -->
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('cares.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      let currentURL = null;
      const input = document.getElementById('imagen');
      const preview = document.getElementById('care_preview_imagen');
      input?.addEventListener('change', function(){
        const f = this.files && this.files[0];
        if (f && f.type && f.type.startsWith('image/')) {
          if (currentURL) URL.revokeObjectURL(currentURL);
          currentURL = URL.createObjectURL(f);
          if (preview) { preview.src = currentURL; preview.style.display = ''; }
        } else {
          if (currentURL) { URL.revokeObjectURL(currentURL); currentURL = null; }
          if (preview) { preview.removeAttribute('src'); preview.style.display = 'none'; }
        }
      });
      const afSel = document.getElementById('animal_file_id');
      const prevText = document.getElementById('care_prev_report_text');
      const imgLink = document.getElementById('care_arrival_img_link');
      const img = document.getElementById('care_arrival_img');
      function updateArrival(){
        const opt = afSel?.selectedOptions?.[0];
        if (!opt) return;
        const repObs = opt.getAttribute('data-rep-obs') || '';
        const repImg = opt.getAttribute('data-rep-img') || '';
        if (prevText) prevText.textContent = repObs || '-';
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
@endsection




