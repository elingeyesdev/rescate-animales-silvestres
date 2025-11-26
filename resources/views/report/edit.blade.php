@extends('adminlte::page')

@section('template_title')
    {{ __('Update') }} {{ __('Report') }}
@endsection

@section('content')
    <section class="content container-fluid page-pad">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} {{ __('Report') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <div class="font-weight-bold mb-1">{{ __('No se pudo actualizar el hallazgo. Revisa los errores:') }}</div>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('reports.update', $report->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('report.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@include('partials.page-pad')
@endsection
