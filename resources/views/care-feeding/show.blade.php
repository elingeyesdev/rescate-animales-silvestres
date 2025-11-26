@extends('adminlte::page')

@section('template_title')
    {{ $careFeeding->name ?? __('Show') . " " . __('Care Feeding') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} {{ __('Care Feeding') }}</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('care-feedings.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Care Id') }}:</strong>
                                    {{ $careFeeding->care_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Feeding Type Id') }}:</strong>
                                    {{ $careFeeding->feeding_type_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Feeding Frequency Id') }}:</strong>
                                    {{ $careFeeding->feeding_frequency_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Feeding Portion Id') }}:</strong>
                                    {{ $careFeeding->feeding_portion_id }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
