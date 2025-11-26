@extends('adminlte::page')

@section('template_title')
    {{ __('Create') }} {{ __('Feeding Portion') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} {{ __('Feeding Portion') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('feeding-portions.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('feeding-portion.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
