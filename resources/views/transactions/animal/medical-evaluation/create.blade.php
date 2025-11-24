@extends('adminlte::page')

@section('template_title')
    {{ __('Registrar Evaluación Médica (Transaccional)') }}
@endsection

@section('content')
    <section class="content container-fluid">
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
                                    <div class="form-group mb-2 mb20">
                                        <label for="animal_file_id" class="form-label">{{ __('Hoja de Animal') }}</label>
                                        <select name="animal_file_id" id="animal_file_id" class="form-control @error('animal_file_id') is-invalid @enderror">
                                            <option value="">{{ __('Seleccione') }}</option>
                                            @foreach(($animalFiles ?? []) as $af)
                                                <option value="{{ $af->id }}" {{ (string)old('animal_file_id') === (string)$af->id ? 'selected' : '' }}>
                                                    #{{ $af->id }} {{ $af->animal?->nombre ? '- ' . $af->animal->nombre : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('animal_file_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
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
                                                <select name="veterinario_id" id="veterinario_id" class="form-control @error('veterinario_id') is-invalid @enderror">
                                                    <option value="">{{ __('Seleccione') }}</option>
                                                    @foreach(($veterinarians ?? []) as $v)
                                                        <option value="{{ $v->id }}" {{ (string)old('veterinario_id') === (string)$v->id ? 'selected' : '' }}>
                                                            #{{ $v->id }} {{ $v->person?->nombre ?? '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
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
                                                    <div class="custom-file">
                                                        <input type="file" accept="image/*" name="imagen" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen">
                                                        <label class="custom-file-label" for="imagen">{{ __('Seleccionar imagen') }}</label>
                                                    </div>
                                                </div>
                                                {!! $errors->first('imagen', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- La fecha se asigna automáticamente en el servidor (UTC-4). --}}

                                    <div class="form-group mb-2 mb20">
                                        <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
                                        <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion') }}</textarea>
                                        {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>

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

                                    <!-- Observaciones: no necesarias en transaccional -->
                                </div>

                                <div class="col-md-12 mt20 mt-2">
                                    <button type="submit" class="btn btn-primary">{{ __('Guardar transacción') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


