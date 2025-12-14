@extends('adminlte::auth.register')

@section('auth_header')
@endsection

@push('css')
<style>
    /* --- NUEVO: HACER LA CAJA DE REGISTRO ANCHA --- */
    .register-box {
        width: 900px !important;
        max-width: 95% !important;
    }
    
    /* --- TUS ESTILOS ORIGINALES --- */
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
    .custom-file-label::after { content: 'Subir'; }
    
    #foto-preview-container {
        margin-top: 15px;
        visibility: visible !important;
        display: block !important;
    }
    #foto-placeholder {
        display: block;
    }
    #foto-preview {
        margin: 0 auto;
        visibility: visible !important;
    }
</style>
@endpush

@push('scripts')
<script>
// Logo logic
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

@section('auth_body')
    <form action="{{ route('register') }}" method="post" enctype="multipart/form-data">
        @csrf
 
        <div class="row">
            
            <div class="col-md-8 pr-md-4" style="border-right: 1px solid #eee;">
                
                <h5 class="text-muted mb-3">Datos Personales</h5>

                <div class="input-group mb-3">
                    <input type="text" name="nombre" id="nombre"
                        class="form-control @error('nombre') is-invalid @enderror"
                        placeholder="Nombre completo" value="{{ old('nombre') }}" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-user"></span></div>
                    </div>
                    @error('nombre') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                </div>
         
                <div class="input-group mb-3">
                    <input type="text" name="ci" id="ci"
                        class="form-control @error('ci') is-invalid @enderror"
                        placeholder="CI" value="{{ old('ci', request('ci')) }}" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-id-card"></span></div>
                    </div>
                    @error('ci') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                </div>
         
                <div class="input-group mb-3">
                    <input type="text" name="telefono" id="telefono"
                        class="form-control @error('telefono') is-invalid @enderror"
                        placeholder="Teléfono" value="{{ old('telefono') }}">
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

            </div>
            <div class="col-md-4">
                
                <h5 class="text-muted mb-3">Fotografía</h5>

                <div class="mb-3">
                    <label for="foto" class="form-label" style="font-size: 0.9rem;">Subir imagen <span class="text-danger">*</span></label>
                    <div class="custom-file">
                        <input type="file" 
                            name="foto" 
                            id="foto" 
                            class="custom-file-input @error('foto') is-invalid @enderror" 
                            accept="image/jpeg,image/jpg,image/png"
                            required>
                        <label class="custom-file-label" for="foto" data-browse="Subir" id="foto-label" style="overflow: hidden;"></label>
                    </div>
                    @error('foto') 
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span> 
                    @enderror
                    <small class="form-text text-muted">Max: 5MB (JPG, PNG)</small>
                </div>
        
                <div class="mb-3" id="foto-preview-container">
                    <div class="text-center p-2" style="border: 2px dashed #dee2e6; border-radius: 8px; background-color: #f8f9fa; min-height: 200px; display: flex; align-items: center; justify-content: center;">
                        <div id="foto-placeholder" style="color: #6c757d;">
                            <i class="fas fa-image" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                            <span style="font-size: 14px;">Vista previa</span>
                        </div>
                        <img id="foto-preview" src="" alt="Vista previa" style="max-width: 100%; max-height: 200px; width: auto; height: auto; object-fit: contain; border-radius: 4px; display: none;">
                    </div>
                </div>

            </div>
            </div>
        <div class="row mt-3">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">
                    Registrarme
                </button>
            </div>
        </div>

    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var fotoInput = document.getElementById('foto');
        var fotoPreview = document.getElementById('foto-preview');
        var fotoPlaceholder = document.getElementById('foto-placeholder');
        var fotoLabel = document.getElementById('foto-label');
        var currentObjectURL = null;

        if (fotoInput && fotoPreview && fotoPlaceholder) {
            fotoInput.addEventListener('change', function () {
                var file = this.files && this.files[0];
                
                if (!file) {
                    if (currentObjectURL) {
                        URL.revokeObjectURL(currentObjectURL);
                        currentObjectURL = null;
                    }
                    fotoPlaceholder.style.display = 'block';
                    fotoPreview.style.display = 'none';
                    fotoPreview.removeAttribute('src');
                    if (fotoLabel) fotoLabel.textContent = 'Seleccionar imagen';
                    return;
                }

                var validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!file.type || (!validTypes.includes(file.type) || file.type === 'image/webp')) {
                    if (file.type === 'image/webp') {
                        alert('El formato de imagen .webp no está permitido. Por favor, usa JPG, JPEG o PNG.');
                    } else {
                        alert('Por favor, selecciona una imagen en formato JPG, JPEG o PNG.');
                    }
                    this.value = '';
                    fotoPlaceholder.style.display = 'block';
                    fotoPreview.style.display = 'none';
                    if (fotoLabel) fotoLabel.textContent = 'Seleccionar imagen';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('La imagen no puede superar los 5MB.');
                    this.value = '';
                    fotoPlaceholder.style.display = 'block';
                    fotoPreview.style.display = 'none';
                    if (fotoLabel) fotoLabel.textContent = 'Seleccionar imagen';
                    return;
                }

                if (fotoLabel) {
                    fotoLabel.textContent = file.name;
                }

                if (currentObjectURL) {
                    URL.revokeObjectURL(currentObjectURL);
                }

                currentObjectURL = URL.createObjectURL(file);
                fotoPreview.src = currentObjectURL;
                fotoPlaceholder.style.display = 'none';
                fotoPreview.style.display = 'block';
                fotoPreview.style.visibility = 'visible';
            });
        }
    });
    </script>
@endsection
 
@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('login') }}">Ya tengo una cuenta</a>
    </p>
    @php
        $gatewayLookupUrl = rtrim(env('GATEWAY_REGISTRO_SIMPLE_URL', 'http://gatealas.dasalas.shop/api/gateway/registro/ci'), '/');
    @endphp

    @if($gatewayLookupUrl)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ciInput       = document.getElementById('ci');
                const nombreInput   = document.getElementById('nombre');
                const telefonoInput = document.getElementById('telefono');

                const lookupBaseUrl = @json($gatewayLookupUrl);

                if (!ciInput || !lookupBaseUrl) {
                    return;
                }

                let lastLookupCi = null;
                let isFetching   = false;

                // Función para buscar en el gateway
                async function lookupCi(ci) {
                    const ciValue = (ci || '').trim();

                    // Evitar llamadas con CI muy corto o repetidas
                    if (ciValue.length < 5 || ciValue === lastLookupCi || isFetching) {
                        return;
                    }

                    lastLookupCi = ciValue;
                    isFetching   = true;

                    try {
                        const url = `${lookupBaseUrl}/${encodeURIComponent(ciValue)}`;

                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Client-System': 'rescate',
                            },
                        });

                        if (!response.ok) {
                            console.warn('Gateway lookup failed with status', response.status);
                            return;
                        }

                        const json = await response.json();

                        // Si el gateway no encontró nada, no tocamos el formulario
                        if (!json.success || !json.found || !json.data) {
                            return;
                        }

                        const data = json.data;

                        // Fusionar nombre y apellido para el campo nombre
                        let nombreCompleto = '';
                        if (data.nombre) {
                            nombreCompleto = data.nombre;
                            if (data.apellido) {
                                nombreCompleto += ' ' + data.apellido;
                            }
                        }

                        // Solo rellenar campos vacíos para no pisar lo que el usuario ya escribió
                        if (nombreInput && !nombreInput.value.trim() && nombreCompleto) {
                            nombreInput.value = nombreCompleto.trim();
                        }
                        if (telefonoInput && !telefonoInput.value.trim() && data.telefono) {
                            telefonoInput.value = data.telefono;
                        }

                        // Opcional: normalizar el CI si viene formateado distinto
                        if (data.ci && ciInput.value.trim() !== data.ci) {
                            ciInput.value = data.ci;
                        }

                    } catch (error) {
                        console.error('Error llamando al gateway para autocompletar', error);
                    } finally {
                        isFetching = false;
                    }
                }

                // Leer datos del gateway desde sessionStorage (viene del login)
                const gatewayDataStr = sessionStorage.getItem('gatewayData');
                if (gatewayDataStr) {
                    try {
                        const gatewayData = JSON.parse(gatewayDataStr);
                        console.log('Datos recibidos del gateway:', gatewayData);
                        
                        // Mostrar en consola los datos recibidos
                        if (gatewayData && Object.keys(gatewayData).length > 0) {
                            console.log('=== DATOS DEL GATEWAY ===');
                            console.log('CI:', gatewayData.ci);
                            console.log('Nombre:', gatewayData.nombre);
                            console.log('Apellido:', gatewayData.apellido);
                            console.log('Teléfono:', gatewayData.telefono);
                            console.log('========================');
                            
                            // Por ahora solo mostrar en consola, más adelante se cargarán en el formulario
                        }
                        
                        // Limpiar sessionStorage después de leerlo
                        sessionStorage.removeItem('gatewayData');
                    } catch (e) {
                        console.error('Error al parsear datos del gateway:', e);
                    }
                }

                // Si hay un CI en la URL (viene del login), buscar automáticamente
                const urlParams = new URLSearchParams(window.location.search);
                const ciFromUrl = urlParams.get('ci');
                if (ciFromUrl && ciInput.value === ciFromUrl) {
                    lookupCi(ciFromUrl);
                }

                // También buscar cuando el usuario sale del campo CI (blur)
                ciInput.addEventListener('blur', function () {
                    if (ciInput.value.trim().length >= 5) {
                        lookupCi(ciInput.value);
                    }
                });
            });
        </script>
    @endif
@endsection
@include('partials.custom-file')
