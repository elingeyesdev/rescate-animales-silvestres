# Configuración de Gmail para Envío de Correos

Este proyecto está configurado para usar Gmail como servicio de correo electrónico.

## Pasos para Configurar Gmail

### 1. Habilitar la Verificación en Dos Pasos

1. Ve a tu cuenta de Google: https://myaccount.google.com/
2. Ve a **Seguridad**
3. Activa la **Verificación en dos pasos** (si no la tienes activada)

### 2. Generar una Contraseña de Aplicación

1. Ve a: https://myaccount.google.com/apppasswords
2. Selecciona **Aplicación**: "Correo"
3. Selecciona **Dispositivo**: "Otro (nombre personalizado)"
4. Escribe: "Laravel Rescate Integrado"
5. Haz clic en **Generar**
6. **Copia la contraseña de 16 caracteres** que aparece (la necesitarás para el archivo .env)

### 3. Configurar el archivo .env

Abre tu archivo `.env` y actualiza las siguientes variables:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="Sistema de Rescate Integrado"
```

**Importante:**
- Reemplaza `tu-email@gmail.com` con tu dirección de Gmail completa
- Reemplaza `xxxx xxxx xxxx xxxx` con la contraseña de aplicación de 16 caracteres (sin espacios o con espacios, ambos funcionan)
- El `MAIL_FROM_NAME` puede ser el nombre que quieras que aparezca como remitente

### 4. Probar la Configuración

Ejecuta el siguiente comando para probar el envío de correos:

```bash
php artisan test:email tu-email@destino.com
```

O simplemente:

```bash
php artisan test:email
```

Este comando enviará un correo de prueba y te mostrará si hay algún problema con la configuración.

## Solución de Problemas

### Error: "Authentication failed"

- Verifica que la **Verificación en dos pasos** esté activada
- Asegúrate de estar usando la **Contraseña de aplicación** (no tu contraseña normal de Gmail)
- Verifica que no haya espacios extra en `MAIL_PASSWORD` en el archivo .env

### Error: "Connection timeout"

- Verifica que `MAIL_HOST=smtp.gmail.com`
- Verifica que `MAIL_PORT=587`
- Verifica que `MAIL_ENCRYPTION=tls`
- Asegúrate de que tu firewall no esté bloqueando el puerto 587

### Los correos no llegan

- Revisa la carpeta de **Spam** del destinatario
- Verifica que el correo de destino sea válido
- Revisa los logs en `storage/logs/laravel.log` para más detalles

## Notas Importantes

- **No uses tu contraseña normal de Gmail**, siempre usa una Contraseña de aplicación
- La contraseña de aplicación es específica para esta aplicación y puede revocarse en cualquier momento
- Si cambias tu contraseña de Gmail, necesitarás generar una nueva contraseña de aplicación
- Gmail tiene límites de envío: 500 correos por día para cuentas gratuitas

## Emails que se Envían Automáticamente

El sistema envía automáticamente los siguientes correos desde `resources/views/emails/`:

- `rescuer-application-response.blade.php` - Respuesta a solicitudes de rescatistas
- `veterinarian-application-response.blade.php` - Respuesta a solicitudes de veterinarios
- `new-rescuer-application-notification.blade.php` - Notificación de nueva solicitud de rescatista
- `new-veterinarian-application-notification.blade.php` - Notificación de nueva solicitud de veterinario
- `new-report-notification.blade.php` - Notificación de nuevo reporte/hallazgo
- `caregiver-commitment-confirmation.blade.php` - Confirmación de compromiso de cuidador

Todos estos correos ahora se enviarán usando Gmail SMTP.

