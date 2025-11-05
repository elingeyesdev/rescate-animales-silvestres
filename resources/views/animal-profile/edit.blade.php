@extends('adminlte::page')

@section('template_title')
    {{ __('Update') }} Animal Profile
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Animal Profile</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('animal-profiles.update', $animalProfile->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('animal-profile.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
