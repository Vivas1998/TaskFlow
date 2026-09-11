# Configuración de correo de TaskFlow

TaskFlow dispone de dos salidas de correo intercambiables:

- `mailpit`: buzón local para el desarrollo diario, sin cuentas ni credenciales.
- `brevo`: alternativa opcional para entregar mensajes a direcciones de Internet mediante SMTP.

La opción activa se indica en `web/.env` mediante `MAIL_MAILER`. El repositorio conserva Mailpit como valor predeterminado para que una instalación nueva funcione sin secretos. Mailpit es el mecanismo aceptado para la versión 1.0 local; Brevo no es obligatorio para cerrar ese hito.

## Preparar Brevo

1. Crear una cuenta gratuita en Brevo y activar los correos transaccionales.
2. En `Configuración > Remitentes`, crear un remitente con el nombre `TaskFlow` y un correo al que se tenga acceso.
3. Introducir en Brevo el código de seis cifras recibido en ese correo para verificar el remitente.
4. En `Configuración > SMTP y API > SMTP`, copiar el **login SMTP**. No es necesariamente el correo de acceso a Brevo.
5. Generar una clave SMTP estándar con el nombre `TaskFlow desarrollo` y guardarla en un gestor de contraseñas. Brevo solo muestra la clave completa al crearla.

## Activar la entrega externa

Editar únicamente `web/.env` y completar estas variables:

```dotenv
MAIL_MAILER=brevo
MAIL_FROM_ADDRESS="correo-remitente-verificado@example.com"

BREVO_MAIL_SCHEME=null
BREVO_MAIL_HOST=smtp-relay.brevo.com
BREVO_MAIL_PORT=587
BREVO_MAIL_USERNAME="login-smtp-proporcionado-por-brevo"
BREVO_MAIL_PASSWORD="clave-smtp-proporcionada-por-brevo"
```

Después, recargar la configuración:

```powershell
docker compose exec app php artisan config:clear
```

La clave SMTP es equivalente a una contraseña. No debe pegarse en conversaciones, capturas, documentación ni archivos versionados. `web/.env` está excluido del repositorio.

## Prueba de aceptación externa

1. Registrar o utilizar en TaskFlow una cuenta cuyo correo pueda consultarse.
2. Solicitar la recuperación desde `http://localhost:8000/contrasena/olvidada`.
3. Confirmar que el mensaje llega a la bandeja externa y que el remitente es el verificado en Brevo.
4. Abrir el enlace, establecer una contraseña nueva y acceder con ella.
5. Comprobar en `Transaccional > Registros` de Brevo que el mensaje figura como entregado.

## Volver al buzón local

Cambiar estas variables en `web/.env` y recargar la configuración:

```dotenv
MAIL_MAILER=mailpit
MAIL_FROM_ADDRESS="no-reply@taskflow.local"
```

Los mensajes volverán a aparecer en `http://localhost:8025`.

## Servicio central futuro en el homelab

Durante 2027 puede añadirse un servidor SMTP interno al que se conecten TaskFlow y las demás aplicaciones. La opción recomendada es un **relé central**, no un servidor de correo público completo:

1. Cada aplicación se autentica con credenciales propias en el relé del homelab.
2. El relé limita envíos, conserva registros y permite revocar una aplicación sin afectar a las demás.
3. Los mensajes externos se reenvían mediante un proveedor especializado o la cuenta de correo del dominio.
4. Las aplicaciones nunca guardan la contraseña del buzón personal del administrador.

Un servidor completo que entregue directamente a Gmail, Outlook y otros proveedores necesitaría además dominio, dirección pública adecuada, DNS directo e inverso, SPF, DKIM, DMARC, TLS, vigilancia de listas de bloqueo, filtrado y copias de seguridad. Se evaluará cuando exista el inventario y la red definitiva del homelab.

Para emergencias locales también puede añadirse un comando administrativo que establezca una contraseña nueva desde la consola del servidor. No debe convertirse en una función web ni utilizar preguntas de seguridad; solo sería válido mientras el acceso físico o administrativo al servidor sea de confianza y cada uso quede registrado.
