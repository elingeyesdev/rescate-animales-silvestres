<div class="row padding-1 p-1">
    <div class="col-md-12">
        <input type="hidden" name="animal_file_id" id="animal_file_id" value="{{ old('animal_file_id', $release?->animal_file_id) }}">
        <div class="mb-3">
            <h5 class="mb-2">{{ __('Paso 1: Seleccione el Animal a Liberar') }}</h5>
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
                        <label for="imagen" class="form-label">{{ __('Imagen del animal al momento de liberación') }}</label>
                        <div class="custom-file">
                            <input type="file" accept="image/jpeg,image/jpg,image/png" name="imagen" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen">
                            <label class="custom-file-label" for="imagen" data-browse="Subir">Subir imagen del animal</label>
                        </div>
                        {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
                        <div class="mt-2">
                            <img id="release_preview_imagen" src="" alt="Imagen seleccionada" style="max-height:200px; display:none; border-radius:4px;">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-2 mb20">
                        <label class="form-label">{{ __('Selecciona la ubicación en el mapa') }}</label>
                        <div id="release_map" style="height: 300px; border-radius: 4px;"></div>
                        <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $release?->latitud) }}">
                        <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $release?->longitud) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-2 mb20">
                        <label for="direccion" class="form-label">{{ __('Direccion') }}</label>
                        <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $release?->direccion) }}" id="direccion" placeholder="{{ __('Ingrese la dirección de liberación') }}">
                        {!! $errors->first('direccion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group mb-2 mb20">
                        <label for="detalle" class="form-label">{{ __('Detalle') }}</label>
                        <textarea name="detalle" class="form-control @error('detalle') is-invalid @enderror" id="detalle" rows="3" placeholder="{{ __('Ingrese detalles adicionales sobre la liberación') }}">{{ old('detalle', $release?->detalle) }}</textarea>
                        {!! $errors->first('detalle', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 mt20 mt-2" id="submit_wrap" style="display:none;">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

@include('partials.leaflet')
@include('partials.custom-file')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentURL = null;
    const input = document.getElementById('imagen');
    const preview = document.getElementById('release_preview_imagen');
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
        // Inicializar mapa cuando se muestra el paso 2
        if (typeof window.initMapWithGeolocation === 'function') {
            window.initMapWithGeolocation({
                mapId: 'release_map',
                latInputId: 'latitud',
                lonInputId: 'longitud',
                dirInputId: 'direccion',
                start: { lat: -17.7833, lon: -63.1821, zoom: 13 },
                enableReverseGeocode: true,
            });
        }
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