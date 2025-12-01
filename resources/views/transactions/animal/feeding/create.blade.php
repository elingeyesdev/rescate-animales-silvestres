@extends('adminlte::page')

@section('template_title')
    {{ __('Registrar Alimentación') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Registrar Alimentación') }}</span>
                    </div>
                    <form method="POST" action="{{ route('animal-feeding-records.store') }}" role="form">
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
                                            <div class="form-group mb-1">
                                                <label for="feeding_type_id" class="form-label">{{ __('Tipo de Alimentación') }}</label>
                                                <select name="feeding_type_id" id="feeding_type_id" class="form-control @error('feeding_type_id') is-invalid @enderror">
                                                    <option value="">{{ __('Seleccione') }}</option>
                                                    @foreach(($feedingTypeOptions ?? []) as $id => $name)
                                                        <option value="{{ $id }}"
                                                            {{ (string)old('feeding_type_id') === (string)$id || (!old('feeding_type_id') && $loop->first) ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('feeding_type_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-1">
                                                <label for="feeding_frequency_id" class="form-label">{{ __('Frecuencia') }}</label>
                                                <select name="feeding_frequency_id" id="feeding_frequency_id" class="form-control @error('feeding_frequency_id') is-invalid @enderror">
                                                    <option value="">{{ __('Seleccione') }}</option>
                                                    @foreach(($feedingFrequencyOptions ?? []) as $id => $name)
                                                        <option value="{{ $id }}"
                                                            {{ (string)old('feeding_frequency_id') === (string)$id || (!old('feeding_frequency_id') && $loop->first) ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('feeding_frequency_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-1">
                                                <label for="feeding_portion_id" class="form-label">{{ __('Porción') }}</label>
                                                <select name="feeding_portion_id" id="feeding_portion_id" class="form-control @error('feeding_portion_id') is-invalid @enderror">
                                                    <option value="">{{ __('Seleccione') }}</option>
                                                    @foreach(($feedingPortionOptions ?? []) as $id => $name)
                                                        <option value="{{ $id }}"
                                                            {{ (string)old('feeding_portion_id') === (string)$id || (!old('feeding_portion_id') && $loop->first) ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('feeding_portion_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-1">
                                                <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
                                                <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3" placeholder="{{ __('Ingrese la descripción de la alimentación') }}">{{ old('descripcion') }}</textarea>
                                                {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                            </div>
                                        </div>

                                        <!-- Observaciones: no requeridas en transaccional -->
                                    </div>
                                    </div> {{-- end step2 --}}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer" id="submit_wrap" style="display:none;">
                            <a href="{{ route('care-feedings.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
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
@endsection


