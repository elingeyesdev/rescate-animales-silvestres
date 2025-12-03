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
// Logo
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

        <div class="mb-3">
            <label for="foto" class="form-label">Foto de perfil <span class="text-danger">*</span></label>
            <div class="custom-file">
                <input type="file" 
                    name="foto" 
                    id="foto" 
                    class="custom-file-input @error('foto') is-invalid @enderror" 
                    accept="image/jpeg,image/jpg,image/png"
                    required>
                <label class="custom-file-label" for="foto" id="foto-label">Seleccionar imagen</label>
            </div>
            @error('foto') 
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span> 
            @enderror
            <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG. Tamaño máximo: 5MB</small>
        </div>

        <div class="mb-3" id="foto-preview-container">
            <label class="form-label"><strong>Vista previa:</strong></label>
            <div class="text-center p-4" style="border: 2px dashed #dee2e6; border-radius: 8px; background-color: #f8f9fa; min-height: 200px; display: flex; align-items: center; justify-content: center;">
                <div id="foto-placeholder" style="color: #6c757d;">
                    <i class="fas fa-image" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                    <span style="font-size: 14px;">Aquí se mostrará tu foto de perfil</span>
                </div>
                <img id="foto-preview" src="" alt="Vista previa de foto de perfil" style="max-width: 200px; max-height: 200px; width: auto; height: auto; object-fit: contain; border-radius: 4px; display: none;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            Registrarme
        </button>
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
                    // Limpiar vista previa si no hay archivo
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

                // Validar tipo de archivo
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

                // Validar tamaño (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('La imagen no puede superar los 5MB.');
                    this.value = '';
                    fotoPlaceholder.style.display = 'block';
                    fotoPreview.style.display = 'none';
                    if (fotoLabel) fotoLabel.textContent = 'Seleccionar imagen';
                    return;
                }

                // Actualizar label
                if (fotoLabel) {
                    fotoLabel.textContent = file.name;
                }

                // Limpiar URL anterior si existe
                if (currentObjectURL) {
                    URL.revokeObjectURL(currentObjectURL);
                }

                // Crear nueva URL y mostrar vista previa
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
@endsection