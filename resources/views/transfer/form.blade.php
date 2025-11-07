<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="rescatista_id" class="form-label">{{ __('Rescatista Id') }}</label>
            <input type="text" name="rescatista_id" class="form-control @error('rescatista_id') is-invalid @enderror" value="{{ old('rescatista_id', $transfer?->rescatista_id) }}" id="rescatista_id" placeholder="Rescatista Id">
            {!! $errors->first('rescatista_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="centro_id" class="form-label">{{ __('Centro Id') }}</label>
            <input type="text" name="centro_id" class="form-control @error('centro_id') is-invalid @enderror" value="{{ old('centro_id', $transfer?->centro_id) }}" id="centro_id" placeholder="Centro Id">
            {!! $errors->first('centro_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>