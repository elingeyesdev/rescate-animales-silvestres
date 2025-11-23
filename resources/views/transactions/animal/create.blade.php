@extends('adminlte::page')

@section('template_title')
    {{ __('Registrar Animal (Transaccional)') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Registrar Animal') }}</span>
                    </div>
                    <form method="POST" action="{{ route('animal-transactions.store') }}" role="form" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body bg-white pb-1 pt-2">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('animal.form', [
                                        'animal' => $animal ?? null,
                                        'reports' => $reports ?? [],
                                        'showSubmit' => false
                                    ])
                                </div>
                                <div class="col-md-6">
                                    @include('animal-file.form', [
                                        'animalFile' => $animalFile ?? null,
                                        'animalTypes' => $animalTypes ?? [],
                                        'species' => $species ?? [],
                                        'animalStatuses' => $animalStatuses ?? [],
                                        'showAnimalSelect' => false,
                                        'showSubmit' => false
                                    ])
                                </div>
                            </div>
                        </div>
                        <div class="card-footer pt-2">
                            <a href="{{ route('animal-files.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection


