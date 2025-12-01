<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $person?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        @if($person && $person->user)
        <div class="form-group mb-2 mb20">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $person->user->email) }}" id="email" placeholder="Email">
            {!! $errors->first('email', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            <small class="form-text text-muted">Puedes cambiar el correo electrónico del usuario asociado a esta persona.</small>
        </div>
        @endif
        <div class="form-group mb-2 mb20">
            <label for="ci" class="form-label">{{ __('Ci') }}</label>
            <input type="text" name="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci', $person?->ci) }}" id="ci" placeholder="Ci">
            {!! $errors->first('ci', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="telefono" class="form-label">{{ __('Telefono') }}</label>
            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $person?->telefono) }}" id="telefono" placeholder="Telefono">
            {!! $errors->first('telefono', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="es_cuidador" class="form-label">{{ __('Es Cuidador') }}</label>
            <select name="es_cuidador" id="es_cuidador" class="form-control @error('es_cuidador') is-invalid @enderror">
                @php($val = (string)old('es_cuidador', $person?->es_cuidador))
                <option value="0" {{ $val === '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ $val === '1' ? 'selected' : '' }}>Sí</option>
            </select>
            {!! $errors->first('es_cuidador', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        @if($person && $person->es_cuidador)
            <div class="form-group mb-2 mb20">
                <label for="cuidador_center_id" class="form-label">{{ __('Centro de Cuidador') }}</label>
                <select name="cuidador_center_id" id="cuidador_center_id" class="form-control @error('cuidador_center_id') is-invalid @enderror">
                    <option value="">Seleccione un centro</option>
                    @foreach(\App\Models\Center::orderBy('nombre')->get() as $center)
                        <option value="{{ $center->id }}" {{ old('cuidador_center_id', $person?->cuidador_center_id) == $center->id ? 'selected' : '' }}>
                            {{ $center->nombre }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('cuidador_center_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>

            <div class="form-group mb-2 mb20">
                <label for="cuidador_aprobado" class="form-label">{{ __('Cuidador Aprobado') }}</label>
                <select name="cuidador_aprobado" id="cuidador_aprobado" class="form-control @error('cuidador_aprobado') is-invalid @enderror">
                    @php($aprobado = old('cuidador_aprobado', $person?->cuidador_aprobado))
                    <option value="">Pendiente</option>
                    <option value="1" {{ $aprobado === '1' || $aprobado === 1 ? 'selected' : '' }}>Aprobado</option>
                    <option value="0" {{ $aprobado === '0' || $aprobado === 0 ? 'selected' : '' }}>Rechazado</option>
                </select>
                {!! $errors->first('cuidador_aprobado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>

            <div class="form-group mb-2 mb20">
                <label for="cuidador_motivo_revision" class="form-label">{{ __('Motivo de Revisión (Cuidador)') }}</label>
                <textarea name="cuidador_motivo_revision" id="cuidador_motivo_revision" class="form-control @error('cuidador_motivo_revision') is-invalid @enderror" rows="3" placeholder="Comentarios sobre la aprobación/rechazo del cuidador...">{{ old('cuidador_motivo_revision', $person?->cuidador_motivo_revision) }}</textarea>
                <small class="form-text text-muted">Completa este campo para habilitar el rol de cuidador. Si está vacío, el rol no se asignará aunque es_cuidador sea true.</small>
                {!! $errors->first('cuidador_motivo_revision', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            </div>
        @endif

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>