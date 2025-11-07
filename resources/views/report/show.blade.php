@extends('adminlte::page')

@section('template_title')
    {{ $report->name ?? __('Show') . " " . __('Report') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} {{ __('Report') }}</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('reports.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Reportante:</strong>
                                    {{ $report->person?->nombre ?? '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Aprobado:</strong>
                                    {{ (int)$report->aprobado === 1 ? 'Sí' : 'No' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Imagen:</strong>
                                    @if($report->imagen_url)
                                        <div><img src="{{ asset('storage/' . $report->imagen_url) }}" alt="img" style="max-height:180px;"></div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Observaciones:</strong>
                                    {{ $report->observaciones ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cantidad Animales:</strong>
                                    {{ $report->cantidad_animales }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Dirección:</strong>
                                    {{ $report->direccion ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Latitud:</strong>
                                    {{ $report->latitud ?: '-' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Longitud:</strong>
                                    {{ $report->longitud ?: '-' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
