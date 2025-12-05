@extends('adminlte::auth.login')
@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('register') }}">Crear cuenta nueva</a>
    </p>
    
    {{-- Usuarios de prueba --}}
    <div class="test-users-section">
        <h6 class="text-center"><i class="fas fa-info-circle"></i> Usuarios de Prueba</h6>
        <p class="password-info text-center">Contraseña para todos: <strong>rescate123</strong></p>
        <div class="d-flex flex-column">
            <div class="user-item d-flex justify-content-between align-items-center">
                <span class="user-email">rescateanimales25@gmail.com</span>
                <span class="badge badge-primary">admin</span>
            </div>
            <div class="user-item d-flex justify-content-between align-items-center">
                <span class="user-email">sofia.crespo.r@gmail.com</span>
                <span class="badge badge-warning">encargado</span>
            </div>
            <div class="user-item d-flex justify-content-between align-items-center">
                <span class="user-email">sofivcr01@gmail.com</span>
                <span class="badge badge-info">rescatista</span>
            </div>
            <div class="user-item d-flex justify-content-between align-items-center">
                <span class="user-email">lucasaguilarn@gmail.com</span>
                <span class="badge badge-success">cuidador</span>
            </div>
            <div class="user-item d-flex justify-content-between align-items-center">
                <span class="user-email">lasof0137@gmail.com</span>
                <span class="badge badge-danger">veterinario</span>
            </div>
        </div>
    </div>
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
    .login-logo .brand-image,
    .login-logo img { display: none !important; }
    .login-logo a::before {
        content: "\f1b0";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        color: #fff;
        background: linear-gradient(135deg, #3c8dbc 0%, #357ca5 100%);
        margin-right: 8px;
    }
    .login-logo a { display: inline-flex; align-items: center; }
    /* Ocultar checkbox "Recordarme" */
    .icheck-primary,
    .form-check,
    input[name="remember"],
    label[for="remember"] {
        display: none !important;
    }
    .test-users-section {
        margin-top: 15px;
        padding: 8px 10px;
        background-color: #f8f9fa;
        border-radius: 3px;
        border: 1px solid #e9ecef;
        opacity: 0.85;
    }
    .test-users-section h6 {
        font-size: 0.75rem;
        font-weight: 500;
        margin-bottom: 5px;
        color: #6c757d;
    }
    .test-users-section .user-item {
        padding: 3px 5px;
        margin-bottom: 2px;
        background-color: transparent;
        border-radius: 2px;
    }
    .test-users-section .user-item:last-child {
        margin-bottom: 0;
    }
    .test-users-section .user-email {
        font-size: 0.7rem;
        font-weight: 400;
        color: #6c757d;
    }
    .test-users-section .password-info {
        font-size: 0.65rem;
        color: #868e96;
        margin-bottom: 6px;
    }
    .test-users-section .badge {
        font-size: 0.65rem;
        padding: 2px 6px;
    }
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var logoLink = document.querySelector('.login-logo a');
    if (logoLink && !logoLink.querySelector('.brand-paw')) {
        var icon = document.createElement('span');
        icon.className = 'brand-paw rounded-circle d-inline-flex align-items-center justify-content-center';
        icon.innerHTML = '<i class="fas fa-paw"></i>';
        logoLink.insertBefore(icon, logoLink.firstChild);
    }
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
    
    // Refrescar token CSRF cada 30 minutos para evitar error 419
    setInterval(function() {
        fetch('/refresh-csrf', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function(response) {
            return response.json();
        }).then(function(data) {
            if (data.token) {
                var csrfInput = document.querySelector('input[name="_token"]');
                if (csrfInput) {
                    csrfInput.value = data.token;
                }
                // También actualizar meta tag si existe
                var metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', data.token);
                }
            }
        }).catch(function(error) {
            console.log('Error refreshing CSRF token:', error);
        });
    }, 30 * 60 * 1000); // 30 minutos
});
</script>
@endpush
