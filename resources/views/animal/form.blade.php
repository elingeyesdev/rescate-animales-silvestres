<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $animal?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="sexo" class="form-label">{{ __('Sexo') }}</label>
            <select name="sexo" id="sexo" class="form-control @error('sexo') is-invalid @enderror">
                @php($current = old('sexo', $animal?->sexo))
                @foreach(['Hembra','Macho','Desconocido'] as $opt)
                    <option value="{{ $opt }}" {{ $current === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
            {!! $errors->first('sexo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="descripcion" class="form-label">{{ __('Descripcion') }}</label>
            <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion', $animal?->descripcion) }}" id="descripcion" placeholder="Descripcion">
            {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="reporte_id" class="form-label">{{ __('Número de reporte') }}</label>
            <select name="reporte_id" id="reporte_id" class="form-control @error('reporte_id') is-invalid @enderror">
                <option value="">{{ __('Seleccione') }}</option>
                @foreach(($reports ?? []) as $r)
                    <option value="{{ $r->id }}" {{ (string)old('reporte_id', $animal?->reporte_id) === (string)$r->id ? 'selected' : '' }}>#{{ $r->id }}</option>
                @endforeach
            </select>
            {!! $errors->first('reporte_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>