@extends('adminlte::page')

@section('template_title')
    {{ __('Registrar Cuidado') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Registrar Cuidado') }}</span>
                    </div>
                    <form method="POST" action="{{ route('animal-care-records.store') }}" role="form" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body bg-white">
                            <div class="row padding-1 p-1">
                                <div class="col-md-12">
                                    <input type="hidden" name="animal_file_id" id="animal_file_id" value="{{ old('animal_file_id') }}">
                                    <div class="mb-3">
                                        <h5 class="mb-2">{{ __('Paso 1: Seleccione la Hoja de Animal') }}</h5>
                                        @php
                                            $totalCards = count($afCards ?? []);
                                            $useCarousel = $totalCards > 4;
                                        @endphp
                                        @if($useCarousel)
                                            <div id="af_carousel_wrapper" class="position-relative">
                                                <div id="af_carousel" class="carousel slide" data-ride="carousel" data-interval="false">
                                                    <div class="carousel-inner" id="af_carousel_inner">
                                                        @php
                                                            $cardsPerSlide = 4;
                                                            $chunks = array_chunk($afCards ?? [], $cardsPerSlide);
                                                        @endphp
                                                        @foreach($chunks as $chunkIndex => $chunk)
                                                            <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                                                                <div class="d-flex flex-wrap justify-content-center">
                                                                    @foreach($chunk as $card)
                                                                        <div class="card m-2 af-card" data-af-id="{{ $card['id'] }}" style="width: 200px; cursor: pointer;">
                                                                            <div class="card-img-top mt-3" style="height:110px; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                                                                                @if(!empty($card['img']))
                                                                                    <img src="{{ $card['img'] }}" alt="#{{ $card['id'] }}" style="max-height:100%; max-width:100%;">
                                                                                @else
                                                                                    <span class="text-muted small">{{ __('Sin imagen') }}</span>
                                                                                @endif
                                                                            </div>
                                                                            <div class="card-body p-2">
                                                                                <div class="small font-weight-bold">N°{{ $card['id'] }} {{ $card['name'] }}</div>
                                                                                @if(!empty($card['reporter']))
                                                                                    <div class="small">{{ __('Reportante') }}: {{ $card['reporter'] }}</div>
                                                                                @endif
                                                                                <div class="small text-muted">{{ __('Estado') }}: {{ $card['status'] ?? '-' }}</div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @if(count($chunks) > 1)
                                                        <a class="carousel-control-prev" href="#af_carousel" role="button" data-slide="prev">
                                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                            <span class="sr-only">{{ __('Anterior') }}</span>
                                                        </a>
                                                        <a class="carousel-control-next" href="#af_carousel" role="button" data-slide="next">
                                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                            <span class="sr-only">{{ __('Siguiente') }}</span>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div class="text-center mt-2">
                                                    <small class="text-muted">{{ __('Página') }} <span id="carousel_page">1</span> {{ __('de') }} {{ count($chunks) }}</small>
                                                </div>
                                            </div>
                                        @else
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
                                                            <div class="small font-weight-bold">N°{{ $card['id'] }} {{ $card['name'] }}</div>
                                                            @if(!empty($card['reporter']))
                                                                <div class="small">{{ __('Reportante') }}: {{ $card['reporter'] }}</div>
                                                            @endif
                                                            <div class="small text-muted">{{ __('Estado') }}: {{ $card['status'] ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <button type="button" id="btn_continuar" class="btn btn-primary mt-2" disabled>{{ __('Continuar') }}</button>
                                        {!! $errors->first('animal_file_id', '<div class="text-danger small mt-1" role="alert"><strong>:message</strong></div>') !!}
                                    </div>

                                    <div id="step2" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-2 mb20">
                                                <label for="tipo_cuidado_id" class="form-label">{{ __('Tipo de Cuidado') }}</label>
                                                <select name="tipo_cuidado_id" id="tipo_cuidado_id" class="form-control @error('tipo_cuidado_id') is-invalid @enderror">
                                                    <option value="">{{ __('Seleccione') }}</option>
                                                    @foreach(($careTypes ?? []) as $t)
                                                        <option value="{{ $t->id }}"
                                                            {{ (string)old('tipo_cuidado_id') === (string)$t->id || (!old('tipo_cuidado_id') && $loop->first) ? 'selected' : '' }}>
                                                            {{ $t->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('tipo_cuidado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                            </div>

                                            <div class="form-group mb-2 mb20">
                                                <label for="imagen" class="form-label">{{ __('Evidencia') }}</label>
                                                <div class="custom-file">
                                                    <input type="file" accept="image/jpeg,image/jpg,image/png" name="imagen" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen">
                                                    <label class="custom-file-label" for="imagen" data-browse="Subir">Subir la imagen del animal</label>
                                                </div>
                                                {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
                                                <div class="mt-2">
                                                    <img id="care_preview_imagen" src="" alt="Evidencia seleccionada" style="max-height:120px; display:none;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-2 mb20">
                                                <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
                                                <textarea name="descripcion" id="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror" placeholder="{{ __('Ingrese la descripción del cuidado') }}">{{ old('descripcion') }}</textarea>
                                                {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                            </div>
                                        </div>

                                        <!-- Observaciones: no necesarias en transaccional -->
                                    </div>
                                    </div> {{-- end step2 --}}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer" id="submit_wrap" style="display:none;">
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
        if (f && f.type && f.type.startsWith('image/') && f.type !== 'image/webp') {
          if (currentURL) URL.revokeObjectURL(currentURL);
          currentURL = URL.createObjectURL(f);
          if (preview) { preview.src = currentURL; preview.style.display = ''; }
        } else if (f && f.type === 'image/webp') {
          alert('El formato de imagen .webp no está permitido. Por favor, usa JPG, JPEG o PNG.');
          this.value = '';
          return;
        } else {
          if (currentURL) { URL.revokeObjectURL(currentURL); currentURL = null; }
          if (preview) { preview.removeAttribute('src'); preview.style.display = 'none'; }
        }
      });

      // Paso 1: selección por cards
      const cards = document.querySelectorAll('.af-card');
      const hiddenId = document.getElementById('animal_file_id');
      const btnNext = document.getElementById('btn_continuar');
      const step2 = document.getElementById('step2');
      const submitWrap = document.getElementById('submit_wrap');
      
      // Actualizar contador del carrusel si existe
      const carousel = document.getElementById('af_carousel');
      const carouselPage = document.getElementById('carousel_page');
      if (carousel) {
        carousel.addEventListener('slid.bs.carousel', function (e) {
          if (carouselPage) {
            const activeIndex = Array.from(carousel.querySelectorAll('.carousel-item')).indexOf(e.relatedTarget);
            carouselPage.textContent = (activeIndex + 1);
          }
        });
      }
      
      cards.forEach(card => {
        card.addEventListener('click', function(){
          const id = this.getAttribute('data-af-id');
          if (hiddenId) hiddenId.value = id;
          cards.forEach(c => c.classList.remove('active'));
          this.classList.add('active');
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
    <style>
        .af-card.active{ border:2px solid #28a745; box-shadow:0 0 0 2px rgba(40,167,69,.25); }
        #af_carousel_wrapper {
            padding: 0 50px;
        }
        #af_carousel .carousel-control-prev,
        #af_carousel .carousel-control-next {
            width: 40px;
            background-color: rgba(0,0,0,0.3);
        }
        #af_carousel .carousel-control-prev:hover,
        #af_carousel .carousel-control-next:hover {
            background-color: rgba(0,0,0,0.5);
        }
        #af_carousel .carousel-inner {
            min-height: 200px;
        }
    </style>
    @include('partials.custom-file')
@endsection




