# Instalación local de TaskFlow

**Fecha:** 11 de septiembre de 2026
**Estado:** instrucciones verificadas en el PC de desarrollo

## Requisitos

- Windows 10 u 11 con virtualización habilitada.
- Docker Desktop configurado para contenedores Linux.
- Git.
- Al menos 8 GB de RAM disponibles y 10 GB de espacio libre recomendados.

PHP, Composer, Node.js y MySQL no necesitan instalarse directamente en un equipo nuevo: Docker proporciona las versiones del proyecto.

## Primer arranque

1. Abrir Docker Desktop y esperar a que indique que el motor está iniciado.
2. Abrir una terminal en la carpeta donde se quiera guardar la aplicación.
3. Descargar el proyecto y entrar en su carpeta:

   ```powershell
   git clone https://github.com/Vivas1998/TaskFlow.git
   cd TaskFlow
   ```

4. Ejecutar `docker compose up --build`.
5. Esperar a que la aplicación instale dependencias, cree su clave, conecte con MySQL y aplique las migraciones.
6. Abrir `http://localhost:8000` y crear la primera cuenta desde la pantalla de registro.

Las migraciones incluidas crean toda la estructura de la base de datos. Las semillas solo añaden datos de ejemplo y no son necesarias para utilizar una instalación limpia de TaskFlow.

El primer arranque tarda más porque descarga y construye las imágenes. Los siguientes reutilizan esas capas y los volúmenes locales.

## Servicios

| Servicio | Uso | Acceso desde el PC |
|---|---|---|
| `app` | Laravel con PHP 8.5 | `http://localhost:8000` |
| `node` | Vite para CSS y JavaScript | Puerto `5173` |
| `mysql` | Base de desarrollo | `127.0.0.1:3307` |
| `mysql_test` | Base exclusiva de pruebas | Solo desde los contenedores |
| `mailpit` | Buzón SMTP de desarrollo | `http://localhost:8025` |

## Operaciones habituales

- Arrancar y ver los mensajes: `docker compose up`.
- Arrancar en segundo plano: `docker compose up -d`.
- Parar sin borrar datos: `docker compose down`.
- Consultar el estado: `docker compose ps`.
- Ejecutar pruebas PHP: `docker compose exec app php artisan test`.
- Aplicar migraciones: `docker compose exec app php artisan migrate`.
- Compilar los recursos para una comprobación de producción: `docker compose exec node npm run build`.

## Probar la recuperación de contraseña

1. Abrir `http://localhost:8000/contrasena/olvidada`.
2. Introducir el correo de una cuenta registrada y enviar la solicitud.
3. Abrir el buzón de desarrollo en `http://localhost:8025`.
4. Abrir el mensaje de TaskFlow y pulsar `Cambiar contraseña`.
5. Establecer una contraseña nueva e iniciar sesión con ella.

Mailpit captura el correo dentro del equipo: no entrega mensajes a Internet ni necesita credenciales. La configuración y la prueba con una bandeja externa se describen en [CONFIGURACION_CORREO.md](CONFIGURACION_CORREO.md). Los secretos se guardarán únicamente en `web/.env`, que no se versiona.

No debe usarse la opción `--volumes` al parar salvo que se quiera borrar expresamente la base local. El volumen `mysql_data` conserva los datos aunque se paren o reconstruyan los contenedores.

## Verificación realizada

El 11 de septiembre de 2026 se verificaron el motor Linux de Docker, la construcción de la imagen PHP, el arranque de MySQL, las migraciones, la compilación de recursos y la suite automatizada completa. También se reprodujo el arranque desde una copia limpia y aislada del commit de entrega: se creó `.env`, se generó la clave, se instalaron las dependencias, se aplicaron las nueve migraciones y la pantalla de acceso respondió correctamente. La suite volvió a superar 35 pruebas con 151 aserciones.
