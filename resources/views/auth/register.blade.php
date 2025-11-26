@extends('adminlte::auth.register')
@push('css')
<style>
    .main-sidebar { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
</style>
@endpush

@section('auth_body')
    <form action="{{ route('register') }}" method="post">
        @csrf

        <div class="input-group mb-3">
            <input type="text" name="nombre"
                class="form-control @error('nombre') is-invalid @enderror"
                placeholder="Nombre completo" value="{{ old('nombre') }}" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-user"></span></div>
            </div>
            @error('nombre') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <div class="input-group mb-3">
            <input type="text" name="ci"
                class="form-control @error('ci') is-invalid @enderror"
                placeholder="CI" value="{{ old('ci') }}" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-id-card"></span></div>
            </div>
            @error('ci') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <div class="input-group mb-3">
            <input type="text" name="telefono"
                class="form-control @error('telefono') is-invalid @enderror"
                placeholder="Teléfono (opcional)" value="{{ old('telefono') }}">
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-phone"></span></div>
            </div>
            @error('telefono') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <div class="input-group mb-3">
            <input type="email" name="email"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="Correo electrónico" value="{{ old('email') }}" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>
            @error('email') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Contraseña" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
            @error('password') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password_confirmation"
                class="form-control"
                placeholder="Confirmar contraseña" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
        </div>

        

        <button type="submit" class="btn btn-primary btn-block">
            Registrarme
        </button>
    </form>
@endsection

@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('login') }}">Ya tengo una cuenta</a>
    </p>
@endsection