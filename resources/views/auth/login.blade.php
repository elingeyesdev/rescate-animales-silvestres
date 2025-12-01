@extends('adminlte::auth.login')
@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('register') }}">Crear cuenta nueva</a>
    </p>
@endsection
@push('css')
<style>
    .main-sidebar { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
    .login-box-msg {
        margin-bottom: 10px;
    }
    .alert-before-login {
        margin-bottom: 20px;
        margin-top: 10px;
    }
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('pending_report_id'))
    var alertHtml = `
        <div class="alert alert-info alert-dismissible alert-before-login">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-info-circle"></i> ¡Importante!</h5>
            Si gustas hacer seguimiento, inicia sesión o regístrate.
        </div>
    `;
    var loginBox = document.querySelector('.login-box');
    if (loginBox) {
        var loginCard = loginBox.querySelector('.card');
        if (loginCard) {
            loginCard.insertAdjacentHTML('afterbegin', alertHtml);
        } else {
            // Si no hay card, insertar antes del primer elemento
            loginBox.insertAdjacentHTML('afterbegin', alertHtml);
        }
    }
    @endif
});
</script>
@endpush