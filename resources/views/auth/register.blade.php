@extends('adminlte::auth.register')
@section('auth_header')
@endsection
@push('css')
<style>
    .main-sidebar { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
    .login-logo .brand-image,
    .login-logo img,
    .register-logo .brand-image,
    .register-logo img { display: none !important; }
    .login-logo a::before,
    .register-logo a::before {
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
    .register-logo a { display: inline-flex; align-items: center; }
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var logoLink = document.querySelector('.register-logo a');
    if (logoLink && !logoLink.querySelector('.brand-paw')) {
        var icon = document.createElement('span');
        icon.className = 'brand-paw rounded-circle d-inline-flex align-items-center justify-content-center';
        icon.innerHTML = '<i class="fas fa-paw"></i>';
        logoLink.insertBefore(icon, logoLink.firstChild);
    }
});
</script>
@endpush
