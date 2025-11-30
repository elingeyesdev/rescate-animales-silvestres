# 📘 Documentación del Proyecto (Docker + Laravel + Nginx + PostgreSQL)

Este documento explica de forma simple y profesional cómo ejecutar el proyecto con Docker, cómo funcionan los puertos, cómo se usa el entrypoint y cómo manejar los seeds.

---

## 📑 Índice

1. [⚙️ Puertos para Pruebas Locales](#️-puertos-para-pruebas-locales)
2. [🟣 Contenedor Laravel y Nginx](#-contenedor-laravel-y-nginx)
3. [📝 Script de Inicio (entrypoint)](#-script-de-inicio-docker-entrypointsh)
4. [📄 Importancia de `.env.example`](#-importancia-de-envexample)
5. [🌱 Seeds en Laravel](#-seeds-en-laravel)
6. [🚀 Despliegue del Proyecto](#-despliegue-del-proyecto)
7. [📜 Ver Logs del Contenedor](#-ver-logs-del-contenedor)

---

# ⚙️ Puertos para Pruebas Locales

Este proyecto usa Docker (Laravel + Nginx + PostgreSQL).  
Solo necesitas ajustar los **puertos** y el **nombre del contenedor Laravel**.

---

<details>
<summary><strong>🔵 Puerto de Nginx (Acceso en el Navegador)</strong></summary>

```yaml
nginx:
  ports:
    - "8080:80"
````

* **8080** = puerto local (puede cambiarse)
* **80** = puerto interno (no cambiar)

Si 8080 está ocupado:

```yaml
"8081:80"
"3000:80"
```

Acceso:

```
http://localhost:8080
```

</details>

---

<details>
<summary><strong>🟠 Puerto de PostgreSQL</strong></summary>

```yaml
"5432:5432"
```

Cambio recomendado si tienes otro PostgreSQL activo:

```yaml
"5440:5432"
```

</details>

---

# 🟣 Contenedor Laravel y Nginx

Debe coincidir el nombre del contenedor:

```yaml
container_name: <Proyecto>-laravel
```

En `nginx.conf`:

```nginx
fastcgi_pass <Proyecto>-laravel:9000;
```

Si cambias el nombre del proyecto, cambia ambos.

---

# ✔ Resumen Rápido

* **Cambias:** `8080`, `5432 externo`, nombre del contenedor Laravel
* **No cambias:** `80`, `9000`, `5432 interno`

---

# 📝 Script de Inicio (`docker-entrypoint.sh`)

Este script automatiza la puesta en marcha del proyecto:

* Crea `.env` si no existe
* Instala dependencias con Composer
* Genera la `APP_KEY`
* Ajusta permisos
* Ejecuta migraciones
* Inicia PHP-FPM

Esto evita configuraciones manuales cada vez que inicia el contenedor.

---

# 📄 Importancia de `.env.example`

El `.env.example` actúa como **plantilla** para generar el `.env`.

Permite:

* Tener una configuración base para cualquier entorno
* Evitar subir credenciales reales
* Crear un `.env` válido automáticamente

Sin este archivo, el contenedor no sabría qué variables inicializar.

---

# 🌱 Seeds en Laravel

<details>
<summary><strong>Seeders comentados en el entrypoint</strong></summary>

En el script:

```sh
# echo "🌱 Ejecutando Seeder..."
# php artisan db:seed --force || true
```

Descomentar **solo si necesitas cargar datos iniciales**.

</details>

---

<details>
<summary><strong>Registrar Seeders en Laravel</strong></summary>

Los archivos dentro de:

```
database/seeders/
```

**no se ejecutan automáticamente**.
Debes registrarlos en `DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        UserSeeder::class,
        RoleSeeder::class,
        ProductoSeeder::class,
    ]);
}
```

Si no están ahí, el comando:

```
php artisan db:seed --force
```

no ejecutará nada.

</details>

---

# 🚀 Despliegue del Proyecto

Para iniciar todo:

```
docker compose up --build -d
```

El proceso puede tardar porque se ejecuta todo el `docker-entrypoint.sh`.

---

# 🐢 ¿Se queda atascado en “📦 Instalando dependencias de Composer…”?

Si ves:

```
📦 Instalando dependencias de Composer...
Nothing to install, update or remove
```

y no avanza, es porque la carpeta `vendor/` fue copiada desde tu máquina.

### ✔ Solución

1. Elimina `vendor/`:

```
rm -rf vendor
```

2. Reconstruye:

```
docker compose up --build -d
```

---

# 📜 Ver Logs del Contenedor

```
docker logs <Proyecto>-laravel -f
```

Ejemplo:

```
docker logs rescate-laravel -f
```

También puedes usar **Docker Desktop** → *Containers*.
