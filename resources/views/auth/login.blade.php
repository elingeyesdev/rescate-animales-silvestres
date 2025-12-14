@extends('adminlte::auth.login')
@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('register') }}">Crear cuenta nueva</a>
    </p>
    
    {{-- Sección de búsqueda por CI - Siempre visible encima de Usuarios de Prueba --}}
    <div class="mt-3 mb-2" id="ci-lookup-section">
        <p class="text-center text-muted mb-2" style="font-size: 0.9rem;">
            Si formas parte del sistema, anota tu CI
        </p>
        <div class="input-group">
            <input type="text" 
                   id="ci-lookup-input" 
                   class="form-control" 
                   placeholder="Ingrese su CI" 
                   maxlength="20">
            <div class="input-group-append">
                <button type="button" 
                        id="ci-lookup-btn" 
                        class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
    </div>
    
    {{-- Script inline para definir la función inmediatamente --}}
    @php
        $gatewayUrl = rtrim(env('GATEWAY_REGISTRO_SIMPLE_URL', 'http://gatealas.dasalas.shop/api/gateway/registro/ci'), '/');
    @endphp
    <script>
    (function() {
        var gatewayBaseUrl = @json($gatewayUrl);
        console.log('Gateway URL configurado (inline):', gatewayBaseUrl);
        
        window.handleCiLookup = async function() {
            console.log('=== handleCiLookup EJECUTADO ===');
            var ciLookupInput = document.getElementById('ci-lookup-input');
            console.log('Input encontrado:', ciLookupInput);
            var ci = (ciLookupInput ? ciLookupInput.value : '').trim();
            
            console.log('handleCiLookup llamado, CI:', ci);
            
            if (!ci || ci.length < 5) {
                alert('Por favor ingrese un CI válido (mínimo 5 caracteres)');
                return;
            }

            // Llamar al gateway antes de redirigir
            try {
                const gatewayUrl = gatewayBaseUrl + '/' + encodeURIComponent(ci);
                console.log('Llamando al gateway:', gatewayUrl);
                
                const response = await fetch(gatewayUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Client-System': 'rescate',
                    },
                });

                console.log('Respuesta del gateway:', response.status, response.statusText);

                if (response.ok) {
                    const json = await response.json();
                    console.log('Datos recibidos del gateway:', json);
                    
                    // Guardar los datos en sessionStorage para que el registro los pueda leer
                    if (json.success && json.found && json.data) {
                        sessionStorage.setItem('gatewayData', JSON.stringify(json.data));
                        console.log('Datos del gateway guardados en sessionStorage:', json.data);
                    } else {
                        // Si no hay datos, limpiar sessionStorage
                        sessionStorage.removeItem('gatewayData');
                        console.log('No se encontraron datos en el gateway');
                    }
                } else {
                    console.warn('Error al llamar al gateway:', response.status);
                    sessionStorage.removeItem('gatewayData');
                }
            } catch (error) {
                console.error('Error al llamar al gateway:', error);
                sessionStorage.removeItem('gatewayData');
            }

            // Redirigir al registro con el CI en la URL
            console.log('Redirigiendo al registro con CI:', ci);
            window.location.href = '{{ route("register") }}?ci=' + encodeURIComponent(ci);
        };
        
        // Agregar event listener cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                var btn = document.getElementById('ci-lookup-btn');
                var input = document.getElementById('ci-lookup-input');
                if (btn) {
                    btn.addEventListener('click', window.handleCiLookup);
                }
                if (input) {
                    input.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            window.handleCiLookup();
                        }
                    });
                }
            });
        } else {
            // DOM ya está listo
            var btn = document.getElementById('ci-lookup-btn');
            var input = document.getElementById('ci-lookup-input');
            if (btn) {
                btn.addEventListener('click', window.handleCiLookup);
            }
            if (input) {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        window.handleCiLookup();
                    }
                });
            }
        }
    })();
    </script>
    
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
    #toggle-ci-lookup:hover {
        color: #357ca5 !important;
        text-decoration: underline !important;
    }
    #ci-lookup-section {
        transition: all 0.3s ease;
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

    // CI lookup - Configuración adicional de event listeners
    function initCiLookup() {
        var ciLookupInput = document.getElementById('ci-lookup-input');
        var ciLookupBtn = document.getElementById('ci-lookup-btn');

        console.log('Inicializando CI lookup...', {
            input: ciLookupInput,
            btn: ciLookupBtn,
            gatewayUrl: gatewayBaseUrl
        });

        if (!ciLookupBtn || !ciLookupInput) {
            console.warn('Elementos no encontrados, reintentando...');
            setTimeout(initCiLookup, 100);
            return;
        }

        // Handle CI lookup and redirect to register - función local que llama a la global
        async function handleCiLookupLocal() {
            window.handleCiLookup();
        }

        // Agregar event listener al botón
        if (ciLookupBtn) {
            ciLookupBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Botón Buscar presionado');
                handleCiLookupLocal();
            });
            
            // También agregar onclick directo como respaldo
            ciLookupBtn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Botón Buscar presionado - onclick');
                handleCiLookupLocal();
                return false;
            };
        }
        
        // Agregar event listener al input para Enter
        if (ciLookupInput) {
            ciLookupInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Enter presionado en el input');
                    handleCiLookupLocal();
                }
            });
        }
        
        console.log('Event listeners agregados correctamente');
    }

    // Inicializar inmediatamente y también después de un delay
    console.log('Iniciando setup de CI lookup...');
    initCiLookup();
    setTimeout(function() {
        console.log('Reintentando inicialización de CI lookup...');
        initCiLookup();
    }, 500);
    
    // También intentar después de que todo esté cargado
    window.addEventListener('load', function() {
        console.log('Página completamente cargada, verificando CI lookup...');
        initCiLookup();
    });

});
</script>
@endpush
