<div class="padding-1 p-1">
    <div class="form-group mb-2">
        <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $animal?->nombre) }}" id="nombre" placeholder="Nombre">
        {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
    </div>
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
    <div class="form-group mb-2">
        <label class="form-label">{{ __('Estado (al crear)') }}</label>
        @php
            $current = (string)old('estado_id');
            $statusList = collect($animalStatuses ?? [])->sortBy(fn($x) => $x->nombre);
            function uiColor($n){ $n = (string)$n; $n = mb_strtolower($n); if (strpos($n,'crít')!==false || strpos($n,'crit')!==false) return 'danger'; if (strpos($n,'atenc')!==false) return 'warning'; if (strpos($n,'recuper')!==false) return 'info'; if (strpos($n,'liber')!==false || strpos($n,'listo')!==false) return 'success'; if (strpos($n,'falle')!==false) return 'dark'; return 'secondary'; }
        @endphp
        <div class="btn-group d-flex flex-wrap" data-toggle="buttons">
            @forelse($statusList as $s)
                @php($color = uiColor($s->nombre))
                @php($checked = $current === (string)$s->id)
                <label class="btn btn-outline-{{ $color }} m-1 {{ $checked ? 'active' : '' }}">
                    <input type="radio" name="estado_id" value="{{ $s->id }}" autocomplete="off" {{ $checked ? 'checked' : '' }}> {{ $s->nombre }}
                </label>
            @empty
                <div class="text-muted">{{ __('No hay estados configurados') }}</div>
            @endforelse
        </div>
        {!! $errors->first('estado_id', '<div class="invalid-feedback d-block" role="alert"><strong>:message</strong></div>') !!}
    </div>
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
    @if(($showSubmit ?? true))
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
        </div>
    @endif
</div>