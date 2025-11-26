<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
			<label for="care_id" class="form-label">{{ __('Care') }}</label>
			<select name="care_id" id="care_id" class="form-control @error('care_id') is-invalid @enderror">
				<option value="">{{ __('Seleccione un cuidado') }}</option>
				@foreach(($careOptions ?? []) as $id => $label)
					<option value="{{ $id }}" {{ (string)old('care_id', $careFeeding?->care_id) === (string)$id ? 'selected' : '' }}>{{ $label }}</option>
				@endforeach
			</select>
            {!! $errors->first('care_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
			<label for="feeding_type_id" class="form-label">{{ __('Tipo de alimentación') }}</label>
			<select name="feeding_type_id" id="feeding_type_id" class="form-control @error('feeding_type_id') is-invalid @enderror">
				<option value="">{{ __('Seleccione un tipo') }}</option>
				@foreach(($feedingTypeOptions ?? []) as $id => $nombre)
					<option value="{{ $id }}" {{ (string)old('feeding_type_id', $careFeeding?->feeding_type_id) === (string)$id ? 'selected' : '' }}>{{ $nombre }}</option>
				@endforeach
			</select>
            {!! $errors->first('feeding_type_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
			<label for="feeding_frequency_id" class="form-label">{{ __('Frecuencia de alimentación') }}</label>
			<select name="feeding_frequency_id" id="feeding_frequency_id" class="form-control @error('feeding_frequency_id') is-invalid @enderror">
				<option value="">{{ __('Seleccione una frecuencia') }}</option>
				@foreach(($feedingFrequencyOptions ?? []) as $id => $nombre)
					<option value="{{ $id }}" {{ (string)old('feeding_frequency_id', $careFeeding?->feeding_frequency_id) === (string)$id ? 'selected' : '' }}>{{ $nombre }}</option>
				@endforeach
			</select>
            {!! $errors->first('feeding_frequency_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
			<label for="feeding_portion_id" class="form-label">{{ __('Porción de alimentación') }}</label>
			<select name="feeding_portion_id" id="feeding_portion_id" class="form-control @error('feeding_portion_id') is-invalid @enderror">
				<option value="">{{ __('Seleccione una porción') }}</option>
				@foreach(($feedingPortionOptions ?? []) as $id => $label)
					<option value="{{ $id }}" {{ (string)old('feeding_portion_id', $careFeeding?->feeding_portion_id) === (string)$id ? 'selected' : '' }}>{{ $label }}</option>
				@endforeach
			</select>
            {!! $errors->first('feeding_portion_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>