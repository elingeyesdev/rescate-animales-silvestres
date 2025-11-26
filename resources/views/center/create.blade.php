@extends('adminlte::page')

@section('template_title')
    {{ __('Create') }} {{ __('Center') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} {{ __('Center') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('centers.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('center.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
