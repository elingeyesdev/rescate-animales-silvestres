@extends('adminlte::page')

@section('template_title')
    {{ __('Conservar Reporte') }}
@endsection

@if (!Auth::check())
@push('css')
<style>
    .main-sidebar { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
    .main-header { display: none !important; }
    .navbar { display: none !important; }
</style>
@endpush
@endif

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ __('Reporte Registrado Exitosamente') }}
                        </h3>
                    </div>
                    <div class="card-body bg-white text-center py-5">
                        @if(session('success'))
                            <div class="alert alert-success mb-4">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($report)
                            <div class="mb-4">
                                <i class="fas fa-clipboard-check fa-4x text-success mb-3"></i>
                                <h4 class="mb-3">{{ __('¿Deseas conservar este reporte como tuyo?') }}</h4>
                                <p class="text-muted mb-4">
                                    {{ __('Si conservas este reporte, podrás verlo en tu perfil después de iniciar sesión o registrarte.') }}
                                </p>

                                <div class="card bg-light mb-4 text-left">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-info-circle text-info mr-2"></i>
                                            {{ __('Detalles del Reporte') }}
                                        </h5>
                                        <p class="mb-1">
                                            <strong>{{ __('Estado inicial:') }}</strong> 
                                            {{ $report->condicionInicial?->nombre ?? '-' }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>{{ __('Tipo de incidente:') }}</strong> 
                                            {{ $report->incidentType?->nombre ?? '-' }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>{{ __('Fecha:') }}</strong> 
                                            {{ $report->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('reports.claim.store') }}">
                                @csrf
                                <div class="d-flex justify-content-center gap-3">
                                    <button type="submit" name="action" value="yes" class="btn btn-success btn-lg">
                                        <i class="fas fa-user-check mr-2"></i>
                                        {{ __('Sí, conservar reporte') }}
                                    </button>
                                    <button type="submit" name="action" value="no" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-times mr-2"></i>
                                        {{ __('No, continuar sin cuenta') }}
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="mb-4">
                                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                                <h4 class="mb-3">{{ __('No hay reportes pendientes') }}</h4>
                                <p class="text-muted mb-4">
                                    {{ __('No se encontró ningún reporte pendiente de asociar.') }}
                                </p>
                            </div>
                            <a href="{{ route('landing') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-home mr-2"></i>
                                {{ __('Volver al inicio') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection

