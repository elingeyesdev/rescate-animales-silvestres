@extends('adminlte::page')

@section('template_title')
    {{ __('Historial de Animal ') . $animalHistory->animal_file_id }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">
                            {{ __('Detalle de Historial') }}
                            @if($animalHistory->animalFile?->animal?->nombre)
                                {{ ' ' . __('de') . ' ' . $animalHistory->animalFile->animal->nombre }}
                            @endif
                        </span>
                    </div>
                    <div class="card-body bg-white">
                        @php
                            $af = $animalHistory->animalFile;
                            $animal = $af?->animal;
                            $statusName = $af?->animalStatus?->nombre ?? '-';
                            $animalName = $animal?->nombre ?? '-';
                            $report = $animal?->report ?? null;
                            $reportDate = optional($report?->created_at)->format('d/m/Y');
                            $arrivalImg = $report?->imagen_url;
                        @endphp
                        <div class="sticky-summary d-flex align-items-center mb-3 p-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <h4 class="mb-0 mr-3">{{ $animalName !== '-' ? $animalName : __('Detalle de Historial') }}</h4>
                                    @if($statusName && $statusName !== '-')
                                        <span class="badge badge-info" style="font-size:0.95rem;">{{ $statusName }}</span>
                                    @endif
                                </div>
                                <div class="text-muted small mt-1">
                                    @if($reportDate)
                                        {{ __('Hallazgo') }}: {{ $reportDate }}
                                    @endif
                                </div>
                            </div>
                            @if($arrivalImg)
                                <div class="ml-3">
                                    <img src="{{ asset('storage/' . $arrivalImg) }}" alt="Llegada" style="max-height:96px; border-radius:6px;">
                                </div>
                            @endif
                        </div>
                        
                        <div class="timeline">
                            @php $currentDate = null; @endphp
                            @foreach(($timeline ?? []) as $t)
                                @php
                                    $datetime = trim($t['changed_at'] ?? '');
                                    $date = $datetime ? explode(' ', $datetime)[0] : '';
                                    $time = $datetime && strpos($datetime, ' ') !== false ? trim(substr($datetime, strpos($datetime, ' '))) : '';
                                    $title = $t['title'] ?? 'Actualización';
                                    $icon = 'far fa-clock';
                                    $bg = 'bg-gray';
                                    switch ($title) {
                                        case 'Reporte de hallazgo': $icon='fas fa-flag'; $bg='bg-success'; break;
                                        case 'Traslado': $icon='fas fa-truck'; $bg='bg-warning'; break;
                                        case 'Evaluación Médica': $icon='fas fa-stethoscope'; $bg='bg-danger'; break;
                                        case 'Cuidado': $icon='fas fa-hand-holding-heart'; $bg='bg-purple'; break;
                                        case 'Alimentación': $icon='fas fa-utensils'; $bg='bg-teal'; break;
                                        case 'Cambio de estado': $icon='fas fa-exchange-alt'; $bg='bg-info'; break;
                                        case 'Creación de Hoja de Vida': $icon='fas fa-file-medical'; $bg='bg-primary'; break;
                                        case 'Animal': $icon='fas fa-paw'; $bg='bg-primary'; break;
                                    }
                                @endphp
                                @if($date && $date !== $currentDate)
                                    <div class="time-label">
                                        <span class="bg-navy">{{ $date }}</span>
                                    </div>
                                    @php $currentDate = $date; @endphp
                                @endif
                                <div>
                                    <i class="{{ $icon }} {{ $bg }}"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $time }} @if($date)&nbsp;&nbsp;<i class="fas fa-calendar-alt"></i> {{ $date }}@endif</span>
                                        <h3 class="timeline-header">{{ $title }}</h3>
                                        <div class="timeline-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    @forelse(($t['details'] ?? []) as $d)
                                                        <div class="mb-2">
                                                            <span class="text-muted">{{ $d['label'] }}:</span>
                                                            <span>{{ $d['value'] }}</span>
                                                        </div>
                                                    @empty
                                                        <div class="text-muted">{{ __('Sin detalles') }}</div>
                                                    @endforelse
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    @if(!empty($t['image_url']))
                                                        <img src="{{ asset('storage/' . $t['image_url']) }}"
                                                             data-full="{{ asset('storage/' . $t['image_url']) }}"
                                                             alt="Imagen"
                                                             class="history-thumb"
                                                             style="max-height: 200px; max-width: 100%; height: auto; width: auto; cursor: zoom-in;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @php $hasLink = false; @endphp
                                        @if($hasLink)
                                            <div class="timeline-footer">
                                                <a class="btn btn-primary btn-sm" href="#">{{ __('Ver') }}</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                        <div id="imageOverlay" style="display:none; position:fixed; left:0; top:0; right:0; bottom:0; background:rgba(0,0,0,.85); z-index:1050; align-items:center; justify-content:center;">
                            <button id="overlayClose" type="button" style="position:absolute; top:16px; right:16px; background:rgba(0,0,0,.4); border:0; color:#fff; padding:8px 12px; border-radius:4px; cursor:pointer;">
                                ✕ {{ __('Cerrar') }}
                            </button>
                            <img id="overlayImg" src="" alt="Imagen" style="max-width:90%; max-height:90%; border-radius:4px; box-shadow:0 6px 24px rgba(0,0,0,.35);">
                        </div>

                        <a href="{{ route('animal-histories.index') }}" class="btn btn-secondary mt-3">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
    <style>
        .sticky-summary{
            position: sticky;
            top: 0;
            z-index: 1020; /* above body content */
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            box-shadow: 0 2px 6px rgba(0,0,0,.05);
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const overlay = document.getElementById('imageOverlay');
        const overlayImg = document.getElementById('overlayImg');
        const closeBtn = document.getElementById('overlayClose');
        document.querySelectorAll('.history-thumb').forEach(function(el){
            el.addEventListener('click', function(){
                const full = this.getAttribute('data-full') || this.src;
                if (overlay && overlayImg) {
                    overlayImg.src = full;
                    overlay.style.display = 'flex';
                }
            });
        });
        function hideOverlay(){
            if (overlay) {
                overlay.style.display = 'none';
                if (overlayImg) overlayImg.src = '';
            }
        }
        closeBtn?.addEventListener('click', hideOverlay);
        overlay?.addEventListener('click', function(e){ if (e.target === overlay) hideOverlay(); });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hideOverlay(); });
    });
    </script>
@endsection


