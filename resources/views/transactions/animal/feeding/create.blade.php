@extends('adminlte::page')

@section('template_title')
    {{ __('Registrar Alimentación (Transaccional)') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Registrar Alimentación') }}</span>
                    </div>
                    <form method="POST" action="{{ route('animal-feeding-transactions.store') }}" role="form">
                        @csrf
                        <div class="card-body bg-white">
                            <div class="row padding-1 p-1">
                                <div class="col-md-4">
                                    <div class="form-group mb-1">
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
                                <div class="col-md-4">
                                    <div class="form-group mb-1">
                                        <label for="feeding_type_id" class="form-label">{{ __('Tipo de Alimentación') }}</label>
                                        <select name="feeding_type_id" id="feeding_type_id" class="form-control @error('feeding_type_id') is-invalid @enderror">
                                            <option value="">{{ __('Seleccione') }}</option>
                                            @foreach(($feedingTypeOptions ?? []) as $id => $name)
                                                <option value="{{ $id }}" {{ (string)old('feeding_type_id') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('feeding_type_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-1">
                                        <label for="feeding_frequency_id" class="form-label">{{ __('Frecuencia') }}</label>
                                        <select name="feeding_frequency_id" id="feeding_frequency_id" class="form-control @error('feeding_frequency_id') is-invalid @enderror">
                                            <option value="">{{ __('Seleccione') }}</option>
                                            @foreach(($feedingFrequencyOptions ?? []) as $id => $name)
                                                <option value="{{ $id }}" {{ (string)old('feeding_frequency_id') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('feeding_frequency_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-1">
                                        <label for="feeding_portion_id" class="form-label">{{ __('Porción') }}</label>
                                        <select name="feeding_portion_id" id="feeding_portion_id" class="form-control @error('feeding_portion_id') is-invalid @enderror">
                                            <option value="">{{ __('Seleccione') }}</option>
                                            @foreach(($feedingPortionOptions ?? []) as $id => $name)
                                                <option value="{{ $id }}" {{ (string)old('feeding_portion_id') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('feeding_portion_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-1">
                                        <label for="fecha" class="form-label">{{ __('Fecha') }}</label>
                                        <input type="datetime-local" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha') }}">
                                        {!! $errors->first('fecha', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group mb-1">
                                        <label for="descripcion" class="form-label">{{ __('Descripción del Cuidado') }}</label>
                                        <input type="text" name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion') }}">
                                        {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="observaciones" class="form-label">{{ __('Observaciones (Historial)') }}</label>
                                        <textarea name="observaciones" id="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="2">{{ old('observaciones') }}</textarea>
                                        {!! $errors->first('observaciones', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer" style="position: sticky; bottom: 0; background: #fff; z-index: 10;">
                            <a href="{{ route('care-feedings.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
<style>
.row.padding-1 .form-group{width:48%;display:inline-block;vertical-align:top;margin-right:2%}
@media (max-width: 768px){.row.padding-1 .form-group{width:100%;margin-right:0}}
</style>
@endsection


