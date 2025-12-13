# Sistema de Rescate Animal Integrado

## Descripción del Proyecto

El Sistema de Rescate Animal Integrado es una plataforma de software diseñada para optimizar y digitalizar la gestión operativa de dedicadas al bienestar animal. Esta solución permite la administración centralizada de expedientes médicos, seguimiento de rescates, control de inventario veterinario y gestión de procesos de adopción.

El objetivo principal es proporcionar una herramienta robusta y escalable que facilite la toma de decisiones basada en datos, garantizando la trazabilidad de cada animal desde su ingreso hasta su adopción final.

## Tecnologías Utilizadas

El sistema ha sido desarrollado utilizando un stack tecnológico moderno y robusto:

*   **Framework Backend**: Laravel 11 (PHP 8.2+)
*   **Base de Datos**: PostgreSQL 16
*   **Frontend**: Blade Templating Engine con AdminLTE 3 (Bootstrap 4)
*   **Contenedorización**: Docker & Docker Compose
*   **Gestión de Dependencias**: Composer (PHP) y NPM (Node.js)
*   **Control de Acceso**: Laravel Sanctum y Spatie Permission
*   **Compilación de Assets**: Vite

## Requisitos del Sistema

Antes de iniciar la instalación, asegúrese de cumplir con los siguientes requisitos según el entorno de despliegue seleccionado:

### Entorno con Docker (Recomendado)
*   Docker Engine 20.10+
*   Docker Compose V2+
*   Git

### Entorno Local (Manual)
*   PHP >= 8.2
*   Composer >= 2.0
*   Node.js >= 18.0
*   PostgreSQL >= 14
*   Extensiones de PHP requeridas: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML.

## Guía de Instalación y Configuración

### Opción 1: Despliegue en Contenedores (Docker)

Este método garantiza que todas las dependencias y servicios se ejecuten en un entorno aislado y preconfigurado.

1.  **Clonación del Repositorio**
    Descargue el código fuente en su directorio de trabajo:
    ```bash
    git clone https://github.com/tu-usuario/rescate-integrado.git
    cd rescate-integrado
    ```

2.  **Configuración de Variables de Entorno**
    Copie el archivo de configuración de ejemplo. Las credenciales predeterminadas están optimizadas para el entorno Docker.
    ```bash
    cp .env.example .env
    ```

3.  **Inicio de Servicios**
    Ejecute el siguiente comando para construir las imágenes e iniciar los contenedores en segundo plano:
    ```bash
    docker compose up -d --build
    ```

4.  **Instalación de Dependencias y Configuración Inicial**
    Una vez que los contenedores estén activos, ejecute los siguientes comandos para finalizar la configuración:
    ```bash
    # Instalar dependencias de backend
    docker compose exec app composer install

    # Generar clave de encriptación de la aplicación
    docker compose exec app php artisan key:generate

    # Ejecutar migraciones de base de datos y cargar datos de prueba
    docker compose exec app php artisan migrate --seed

    # Instalar y compilar dependencias de frontend
    docker compose exec app npm install
    docker compose exec app npm run build
    ```

5.  **Acceso al Sistema**
    La aplicación estará disponible en: `http://localhost:8000`

### Opción 2: Instalación Manual en Servidor Local

1.  **Instalación de Dependencias PHP**
    ```bash
    composer install
    ```

2.  **Configuración de Base de Datos**
    Cree una base de datos PostgreSQL vacía y configure el archivo `.env` con sus credenciales locales:
    ```ini
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=nombre_de_su_base_datos
    DB_USERNAME=su_usuario
    DB_PASSWORD=su_contraseña
    ```

3.  **Inicialización del Proyecto**
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```

4.  **Compilación de Assets**
    ```bash
    npm install
    npm run build
    ```

5.  **Ejecución del Servidor de Desarrollo**
    ```bash
    php artisan serve
    ```

## Arquitectura y Módulos

El sistema se divide en módulos funcionales interconectados:

### 1. Gestión de Expedientes
Permite el registro detallado de cada animal, incluyendo fotografías, características físicas, historial de comportamiento y ubicación actual (refugio o casa cuna).

### 2. Módulo Veterinario
Control integral de la salud del animal. Incluye:
*   Registro de vacunas y desparasitaciones.
*   Historial de consultas médicas y cirugías.
*   Seguimiento de tratamientos activos.

### 3. Operaciones de Rescate
Gestión de reportes ciudadanos, asignación de unidades de rescate y documentación del estado inicial del animal al momento del ingreso.

### 4. Gestión de Adopciones
Flujo de trabajo para el procesamiento de solicitudes de adopción, evaluación de candidatos y seguimiento post-adopción.

## Control de Acceso y Roles

El sistema implementa un estricto control de acceso basado en roles (RBAC). A continuación se detallan las credenciales predeterminadas para el entorno de desarrollo:

| Rol | Correo Electrónico | Contraseña Predeterminada | Nivel de Acceso |
| :--- | :--- | :--- | :--- |
| **Administrador** | `rescateanimales25@gmail.com` | `rescate123` | Acceso total al sistema y configuración. |
| **Veterinario** | (Creación manual requerida) | - | Acceso a expedientes médicos y tratamientos. |
| **Rescatista** | (Creación manual requerida) | - | Acceso a módulo de reportes y traslados. |

*Nota: Se recomienda cambiar estas credenciales inmediatamente después del despliegue en un entorno de producción.*

## Solución de Problemas Frecuentes

### Error: "Vite manifest not found"
Este error indica que los archivos estáticos (CSS/JS) no han sido compilados.
**Solución:** Ejecute `npm run build` en su entorno (local o dentro del contenedor).

### Error de Conexión a Base de Datos (SQLSTATE[08006])
Generalmente ocurre cuando el puerto 5432 ya está en uso por otra instancia de PostgreSQL en el sistema anfitrión.
**Solución:** Detenga el servicio local de PostgreSQL o modifique el puerto externo en el archivo `docker-compose.yml`.

### Permisos de Escritura
Si la aplicación reporta errores al escribir logs o guardar imágenes.
**Solución:** Otorgue permisos a las carpetas de almacenamiento:
```bash
chmod -R 775 storage bootstrap/cache
```


