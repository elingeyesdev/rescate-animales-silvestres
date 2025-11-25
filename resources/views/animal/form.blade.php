<div class="padding-1 p-1">
    @if(($showName ?? true))
        <div class="form-group mb-2">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $animal?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    @endif
    <div class="form-group mb-2">
        <label for="sexo" class="form-label">{{ __('Sexo') }}</label>
        <select name="sexo" id="sexo" class="form-control @error('sexo') is-invalid @enderror">
            @php($current = old('sexo', $animal?->sexo))
            @foreach(['Hembra','Macho','Desconocido'] as $opt)
                <option value="{{ $opt }}" {{ $current === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
        {!! $errors->first('sexo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
    </div>
    <div class="form-group mb-2">
        <label for="descripcion" class="form-label">{{ __('Descripcion') }}</label>
        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" rows="3" placeholder="Descripcion">{{ old('descripcion', $animal?->descripcion) }}</textarea>
        {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
    </div>
    @if(empty($hideInitialState))
        <div class="form-group mb-2">
            <label for="estado_id" class="form-label">{{ __('Estado (al crear)') }}</label>
            <select name="estado_id" id="estado_id" class="form-control @error('estado_id') is-invalid @enderror">
                <option value="">{{ __('Seleccione') }}</option>
                @foreach(collect($animalStatuses ?? [])->sortBy('nombre') as $s)
                    <option value="{{ $s->id }}" {{ (string)old('estado_id') === (string)$s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                @endforeach
            </select>
            {!! $errors->first('estado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    @endif
    @if(empty($hideReportSelect))
        <div class="form-group mb-2">
            <label for="reporte_id" class="form-label">{{ __('Número de reporte') }}</label>
            <select name="reporte_id" id="reporte_id" class="form-control @error('reporte_id') is-invalid @enderror">
                <option value="">{{ __('Seleccione') }}</option>
                @foreach(($reports ?? []) as $r)
                    <option value="{{ $r->id }}" {{ (string)old('reporte_id', $animal?->reporte_id) === (string)$r->id ? 'selected' : '' }}>#{{ $r->id }}</option>
                @endforeach
            </select>
            {!! $errors->first('reporte_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    @else
        <input type="hidden" name="reporte_id" id="reporte_id" value="{{ old('reporte_id', $animal?->reporte_id) }}">
    @endif
    @if(($showSubmit ?? true))
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
        </div>
    @endif
</div>