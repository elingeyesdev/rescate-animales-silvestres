@extends('adminlte::page')

@section('template_title')
    {{ __('Update') }} {{ __('Care Feeding') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} {{ __('Care Feeding') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('care-feedings.update', $careFeeding->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('care-feeding.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
