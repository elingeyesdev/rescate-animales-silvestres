@extends('adminlte::page')

@section('template_title')
    {{ __('Create') }} {{ __('Adoption') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} {{ __('Adoption') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('adoptions.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('adoption.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@include('partials.page-pad')
@endsection
