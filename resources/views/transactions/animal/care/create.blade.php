@extends('adminlte::page')

@section('template_title')
    {{ __('Registrar Cuidado (Transaccional)') }}
@endsection

@section('content')
    <section class="content container-fluid">
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
                                                <option value="{{ $af->id }}" {{ (string)old('animal_file_id') === (string)$af->id ? 'selected' : '' }}>#{{ $af->id }} {{ $af->animal?->nombre ? '- ' . $af->animal->nombre : '' }}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('animal_file_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
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
                                        <div class="custom-file">
                                            <input type="file" accept="image/*" name="imagen" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen">
                                            <label class="custom-file-label" for="imagen">{{ __('Seleccionar imagen') }}</label>
                                        </div>
                                        {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
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
                            <button type="submit" class="btn btn-primary">{{ __('Guardar transacción') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('imagen');
        input?.addEventListener('change', function(){
            const fileName = this.files && this.files[0] ? this.files[0].name : '{{ __('Seleccionar imagen') }}';
            const label = this.nextElementSibling;
            if (label) label.textContent = fileName;
        });
    });
    </script>
@endsection




