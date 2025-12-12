# 🔐 Guía para Manejar Secretos en Docker Compose

## ⚠️ Problema Resuelto

El archivo `docker-compose.yml` ahora usa variables de entorno en lugar de valores hardcodeados. Los secretos ya no están expuestos en el código.

## 📝 Configuración

### 1. Crear archivo `.env` en la raíz del proyecto

Docker Compose lee automáticamente un archivo `.env` en el directorio raíz. Crea este archivo con tus valores reales:

```bash
# Base de datos
DB_DATABASE=rescate_db
DB_USERNAME=admin
DB_PASSWORD=admin123
POSTGRES_DB=rescate_db
POSTGRES_USER=admin
POSTGRES_PASSWORD=admin123

# Aplicación
APP_NAME=RescateAnimales
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_BO

# NASA FIRMS API
NASA_FIRMS_API_KEY=tu_api_key_aqui
NASA_FIRMS_API_BASE=https://firms.modaps.eosdis.nasa.gov/api/area/csv

# Hotspots Integration
HOTSPOTS_INTEGRATION_API_URL=http://brigadas.dasalas.shop/api/v1/hotspots/live

# Configuración de correo (Sendinblue/Brevo)
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario_smtp@brevo.com
MAIL_PASSWORD=tu_password_smtp_aqui
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME=RescateAnimales
```

### 2. El archivo `.env` ya está en `.gitignore`

No se subirá al repositorio. Solo tú tendrás acceso a los valores reales.

## 🗑️ Remover Secreto del Historial de Git

El secreto ya está removido del código actual, pero **aún existe en el historial de Git**. Para removerlo completamente:

### Opción 1: Usar GitHub Secret Scanning (Recomendado)

1. Ve a: https://github.com/sofia1210/rescate-integrado/security/secret-scanning/unblock-secret/36lDUq8b42rzsAs4VmoYVi6Vx1e
2. Sigue las instrucciones para invalidar el secreto expuesto
3. Genera una nueva clave SMTP en Sendinblue/Brevo

### Opción 2: Remover del Historial (Avanzado)

Si necesitas remover el secreto del historial completo de Git:

```bash
# Usar git filter-branch (puede ser lento en repos grandes)
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch docker-compose.yml" \
  --prune-empty --tag-name-filter cat -- --all

# O usar BFG Repo-Cleaner (más rápido)
# Descargar de: https://rtyley.github.io/bfg-repo-cleaner/
bfg --replace-text passwords.txt docker-compose.yml

# Después de cualquiera de los dos métodos:
git push origin --force --all
git push origin --force --tags
```

**⚠️ ADVERTENCIA**: Esto reescribe el historial. Todos los colaboradores necesitarán hacer un `git pull --rebase` o clonar de nuevo.

## ✅ Verificación

Después de hacer los cambios:

1. Verifica que `docker-compose.yml` no contenga secretos hardcodeados
2. Crea tu archivo `.env` con los valores reales
3. Prueba que Docker Compose funcione:
   ```bash
   docker compose config
   ```
4. Haz commit y push:
   ```bash
   git add docker-compose.yml .gitignore
   git commit -m "fix: remover secretos de docker-compose.yml"
   git push
   ```

## 🔄 Para Colaboradores

Si alguien clona el repositorio:

1. Copia `.env.example` a `.env` (si existe)
2. O crea `.env` manualmente con los valores necesarios
3. Ejecuta `docker compose up`

