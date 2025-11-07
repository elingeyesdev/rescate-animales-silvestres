@extends('adminlte::page')

@section('template_title')
    {{ $transfer->name ?? __('Show') . " " . __('Transfer') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Transfer</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('transfers.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Rescatista Id:</strong>
                                    {{ $transfer->rescatista_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Centro Id:</strong>
                                    {{ $transfer->centro_id }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
