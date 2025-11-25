@extends('adminlte::page')

@section('template_title')
    {{ __('Reports') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">{{ __('Hallazgos') }}</span>
                            <div class="float-right">
                                <a href="{{ route('reports.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('Crear nuevo') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <form method="GET" class="mb-3 js-auto-filter-form">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <label class="mb-1">
                                        {{ __('Urgencia') }}
                                        <button type="button" class="btn btn-link btn-sm p-0 ml-1 align-baseline" data-toggle="tooltip" title="{{ __('Qué tan pronto se debe rescatar al animal. 1–2: Baja (situación estable), 3: Media (requiere seguimiento), 4–5: Alta (atención rápida).') }}">¿{{ __('Qué es urgencia') }}?</button>
                                    </label>
                                    <select name="urgencia_nivel" class="form-control">
                                        <option value="">{{ __('Todas') }}</option>
                                        <option value="alta" {{ request('urgencia_nivel')==='alta'?'selected':'' }}>{{ __('Alta') }}</option>
                                        <option value="media" {{ request('urgencia_nivel')==='media'?'selected':'' }}>{{ __('Media') }}</option>
                                        <option value="baja" {{ request('urgencia_nivel')==='baja'?'selected':'' }}>{{ __('Baja') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-1">{{ __('Reportante') }}</label>
                                    <select name="persona_id" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        @foreach(($reporters ?? []) as $p)
                                            <option value="{{ $p->id }}" {{ (string)$p->id === (string)request('persona_id') ? 'selected' : '' }}>
                                                {{ $p->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-1">{{ __('Tipo de incidente') }}</label>
                                    <select name="tipo_incidente_id" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        @foreach(($incidentTypes ?? []) as $it)
                                            <option value="{{ $it->id }}" {{ (string)$it->id === (string)request('tipo_incidente_id') ? 'selected' : '' }}>
                                                {{ $it->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="mb-1">{{ __('Aprobado') }}</label>
                                    <select name="aprobado" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        <option value="1" {{ request('aprobado')==='1'?'selected':'' }}>{{ __('Sí') }}</option>
                                        <option value="0" {{ request('aprobado')==='0'?'selected':'' }}>{{ __('No') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('reports.index') }}" class="btn btn-link p-0">{{ __('Mostrar todos') }}</a>
                            </div>
                        </form>

                        <style>
                        .report-card-img {
                            width: 100%;
                            height: 180px;
                            object-fit: cover;
                            background: #f4f6f9;
                        }
                        .report-card .card-header { padding-left: 1.25rem; padding-right: 1.25rem; }
                        .report-card .card-header .card-tools { margin-right: 0; }
                        /* Ajuste de espacios verticales entre cuerpo y footer */
                        .report-card .card-body { padding-bottom: .75rem; }
                        .report-card .card-footer { padding-top: .5rem; padding-bottom: .5rem; }
                        /* Botones iguales y con separación uniforme */
                        .report-card .card-footer form > * { flex: 1 1 0; }
                        .report-card .card-footer form > * + * { margin-left: .5rem; }
                        </style>

                        <div class="row">
                            @foreach ($reports as $report)
                                @php
                                    $urg = $report->urgencia;
                                    // Escala 1..5
                                    if (is_numeric($urg)) {
                                        if ($urg >= 4) { $urgClass = 'danger'; }       // alta
                                        elseif ($urg == 3) { $urgClass = 'warning'; }  // media
                                        else { $urgClass = 'info'; }                   // baja (1-2)
                                    } else {
                                        $urgClass = 'secondary';
                                    }
                                @endphp
                                <div class="col-md-4">
                                    <div class="card card-outline card-secondary h-100 report-card">
                                        @if($report->imagen_url)
                                            <img class="report-card-img" src="{{ asset('storage/' . $report->imagen_url) }}" alt="imagen hallazgo">
                                        @endif
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h3 class="card-title mb-0" title="{{ $report->condicionInicial?->nombre }}">
                                                {{ \Illuminate\Support\Str::limit($report->condicionInicial?->nombre ?? __('Condición no especificada'), 26) }}
                                            </h3>
                                            <div class="card-tools d-flex align-items-center">
                                                <span class="small text-muted mr-1">{{ __('Urgencia') }}:</span>
                                                <span class="badge badge-{{ $urgClass }}" title="{{ __('Urgencia') }}">
                                                    {{ is_null($urg) ? __('N/A') : $urg }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-1"><strong>{{ __('Incidente:') }}</strong> {{ $report->incidentType?->nombre ?? '-' }}</p>
                                            <p class="mb-1"><strong>{{ __('Reportante:') }}</strong> {{ $report->person?->nombre ?? '-' }}</p>
                                            <p class="mb-1"><strong>{{ __('Aprobado:') }}</strong> {{ (int)$report->aprobado === 1 ? __('Sí') : __('No') }}</p>
                                            <!--<p class="mb-1"><strong>{{ __('Tamaño:') }}</strong> {{ $report->tamano ?? '-' }}</p>
                                            <p class="mb-1"><strong>{{ __('¿Puede moverse?:') }}</strong> {{ is_null($report->puede_moverse) ? '-' : ($report->puede_moverse ? __('Sí') : __('No')) }}</p>-->
                                            <p class="mb-0"><strong>{{ __('Fecha:') }}</strong> {{ optional($report->created_at)->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="card-footer">
                                            <form action="{{ route('reports.destroy', $report->id) }}" method="POST" class="mb-0 d-flex w-100">
                                                <a class="btn btn-primary btn-sm" href="{{ route('reports.show', $report->id) }}">
                                                    <i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}
                                                </a>
                                                <a class="btn btn-success btn-sm" href="{{ route('reports.edit', $report->id) }}">
                                                    <i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}
                                                </a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm js-confirm-delete">
                                                    <i class="fa fa-fw fa-trash"></i> {{ __('Eliminar') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                {!! $reports->withQueryString()->links() !!}
            </div>
        </div>
    </section>
    @include('partials.page-pad')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.querySelector('form.js-auto-filter-form');
        if (form) {
            var selects = form.querySelectorAll('select');
            selects.forEach(function(sel){
                sel.addEventListener('change', function(){
                    form.submit();
                });
            });
        }
        if (window.$ && typeof window.$.fn.tooltip === 'function') {
            window.$('[data-toggle="tooltip"]').tooltip();
        }
    });
    </script>
@endsection
