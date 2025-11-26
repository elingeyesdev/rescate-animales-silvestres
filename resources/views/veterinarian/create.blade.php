@extends('adminlte::page')

@section('template_title')
    {{ __('Create') }} {{ __('Veterinarian') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} {{ __('Veterinarian') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('veterinarians.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('veterinarian.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
