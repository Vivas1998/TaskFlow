# Instalación y puesta a punto de TaskFlow en otro equipo

**Versión de referencia:** TaskFlow 1.0 (`v1.0.0`)
**Sistema validado:** Windows 10/11 de 64 bits con Docker Desktop y WSL 2
**Última actualización:** 13 de septiembre de 2026

Esta guía permite preparar un ordenador nuevo, descargar TaskFlow, arrancarlo y comprobar que funciona. También explica cómo trasladar los datos existentes desde otro equipo.

## 1. Qué se necesita

### Equipo

- Windows 10 22H2 de 64 bits o una versión compatible de Windows 11.
- Virtualización habilitada en BIOS/UEFI.
- Procesador de 64 bits.
- Al menos 8 GB de RAM; 16 GB o más es recomendable para desarrollar con comodidad.
- Al menos 10 GB de espacio libre para el proyecto, imágenes y volúmenes de Docker.
- Conexión a Internet durante la instalación y el primer arranque.

La virtualización puede comprobarse en `Administrador de tareas > Rendimiento > CPU`. Debe aparecer como `Habilitada`.

### Aplicaciones obligatorias

- Git para descargar y actualizar el repositorio.
- WSL 2 como base de los contenedores Linux en Windows.
- Docker Desktop con el motor Linux y Docker Compose.

No hay que instalar directamente PHP, Composer, Node.js, npm ni MySQL. Las versiones correctas están proporcionadas por Docker.

### Aplicaciones opcionales

- Visual Studio Code u otro editor para modificar el proyecto.
- Windows Terminal para trabajar más cómodamente con PowerShell.

## 2. Instalar Git

Abrir PowerShell y ejecutar:

```powershell
winget install --exact --id Git.Git --source winget
```

Si `winget` no está disponible, instalar Git for Windows desde `https://git-scm.com/install/windows`.

Cerrar y volver a abrir PowerShell. Comprobar la instalación:

```powershell
git --version
```

Solo si el equipo también se utilizará para desarrollar y crear commits, configurar la identidad:

```powershell
git config --global user.name "Pablo Vivas"
git config --global user.email "pablovivasgarcia1@gmail.com"
```

Estas opciones identifican los commits; no sirven para iniciar sesión en GitHub.

## 3. Instalar y preparar WSL 2

Abrir PowerShell como administrador y ejecutar:

```powershell
wsl --install --no-distribution
wsl --update
wsl --set-default-version 2
```

Reiniciar Windows si lo solicita. Después comprobar el estado:

```powershell
wsl --status
wsl --version
```

TaskFlow no necesita una distribución Ubuntu independiente: Docker Desktop utiliza WSL 2 para ejecutar sus contenedores Linux.

Si `wsl --install` muestra la ayuda en lugar de instalar, WSL probablemente ya está presente. Ejecutar `wsl --update`, reiniciar y continuar.

## 4. Instalar Docker Desktop

La instalación mediante PowerShell puede iniciarse con:

```powershell
winget install --exact --id Docker.DockerDesktop --source winget
```

Como alternativa, descargar el instalador oficial desde `https://docs.docker.com/desktop/setup/install/windows-install/` y seleccionar el motor basado en WSL 2.

Después de instalar:

1. Abrir Docker Desktop.
2. Aceptar sus condiciones de uso si aparecen.
3. En `Settings > General`, comprobar que está habilitado el motor WSL 2.
4. Esperar a que Docker indique que el motor está iniciado.
5. Mantener seleccionados los contenedores Linux. TaskFlow no utiliza contenedores de Windows.

Comprobar desde una PowerShell nueva:

```powershell
docker version
docker compose version
docker info
```

Los tres comandos deben responder sin errores relacionados con el motor o con permisos.

## 5. Instalar un editor opcional

Visual Studio Code no es necesario para ejecutar TaskFlow, pero sí resulta útil para desarrollarlo:

```powershell
winget install --exact --id Microsoft.VisualStudioCode --source winget
```

## 6. Descargar TaskFlow

Crear o elegir una carpeta de proyectos. Por ejemplo:

```powershell
New-Item -ItemType Directory -Path C:\Proyectos -Force
Set-Location C:\Proyectos
git clone https://github.com/Vivas1998/TaskFlow.git
Set-Location .\TaskFlow
```

Si GitHub solicita autenticación, iniciar sesión con una cuenta que tenga permiso para acceder al repositorio.

Comprobar la versión descargada:

```powershell
git status
git log -1 --oneline
git tag --points-at HEAD
```

Para instalar exactamente la versión 1.0 en lugar de la rama más reciente:

```powershell
git checkout v1.0.0
```

Esto deja el repositorio en modo de consulta sobre esa versión. Para continuar desarrollando deben utilizarse `main` o una rama de trabajo.

## 7. Primer arranque

Con Docker Desktop iniciado y PowerShell situada en la carpeta `TaskFlow`, ejecutar:

```powershell
docker compose up --build -d
```

El primer arranque puede tardar varios minutos. Automáticamente:

1. Se descargan o construyen las imágenes necesarias.
2. Se instalan las dependencias PHP y JavaScript.
3. Se crea `web/.env` desde `web/.env.example`.
4. Laravel genera una clave única para esa instalación.
5. MySQL crea la base de datos.
6. Laravel aplica todas las migraciones y genera la estructura completa.
7. Se inician TaskFlow, Vite y Mailpit.

Consultar el progreso:

```powershell
docker compose ps
docker compose logs -f app
```

Cuando aparezca `Server running`, pulsar `Ctrl+C`. Esto deja los contenedores funcionando; solamente cierra la visualización del registro.

Abrir:

- TaskFlow: `http://localhost:8000`
- Mailpit: `http://localhost:8025`

Crear la primera cuenta desde `Crear una cuenta`. El primer usuario podrá crear proyectos y será propietario de los que cree.

## 8. Comprobación de la instalación

Ejecutar:

```powershell
docker compose ps
docker compose exec app php artisan about
docker compose exec app php artisan migrate:status
docker compose exec app php artisan test
docker compose exec node npm run build
```

El resultado esperado es:

- Servicios `app`, `node`, `mysql`, `mysql_test` y `mailpit` iniciados.
- Todas las migraciones con estado `Ran`.
- 35 pruebas y 151 aserciones superadas en la versión 1.0.
- Compilación de Vite terminada sin errores.

Realizar además esta comprobación breve desde la web:

1. Registrar una cuenta e iniciar y cerrar sesión.
2. Crear un proyecto.
3. Crear una tarea y un evento.
4. Abrir el calendario y confirmar que aparece el evento.
5. Solicitar una recuperación de contraseña.
6. Abrir Mailpit, entrar en el mensaje y cambiar la contraseña.

## 9. Puertos y direcciones

Los puertos predeterminados son:

| Servicio | Dirección o puerto |
|---|---|
| TaskFlow | `http://localhost:8000` |
| Vite | `5173` |
| MySQL | `127.0.0.1:3307` |
| Mailpit | `http://localhost:8025` |

Si alguno está ocupado, se pueden cambiar antes de arrancar los contenedores:

```powershell
$env:TASKFLOW_APP_PORT = "8080"
$env:TASKFLOW_VITE_PORT = "15173"
$env:TASKFLOW_MYSQL_PORT = "13307"
$env:TASKFLOW_MAILPIT_PORT = "18025"
docker compose up --build -d
```

Estas variables duran hasta cerrar esa ventana de PowerShell. Si se cambia el puerto web, editar después `web/.env`:

```dotenv
APP_URL=http://localhost:8080
```

Y recargar la configuración:

```powershell
docker compose exec app php artisan config:clear
```

## 10. Operaciones habituales

Los comandos se ejecutan desde la carpeta raíz `TaskFlow`.

```powershell
# Arrancar en segundo plano
docker compose up -d

# Ver el estado
docker compose ps

# Ver los últimos mensajes
docker compose logs --tail 200 app mysql node mailpit

# Seguir los mensajes en tiempo real
docker compose logs -f

# Reiniciar los servicios
docker compose restart

# Parar sin borrar los datos
docker compose down

# Reconstruir después de un cambio de configuración
docker compose up --build -d
```

`docker compose down` conserva la base de datos. No añadir `--volumes` salvo que se quiera borrar expresamente toda la base local.

## 11. Actualizar TaskFlow

Antes de actualizar, realizar una copia de seguridad si existen datos importantes. Después:

```powershell
Set-Location C:\Proyectos\TaskFlow
git status
git pull --ff-only
docker compose up --build -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan test
```

Si `git status` muestra archivos modificados, revisarlos antes de descargar cambios. No deben descartarse automáticamente porque podrían contener trabajo sin guardar.

## 12. Trasladar los datos desde otro equipo

Una instalación nueva comienza con la base de datos vacía. Las migraciones crean las tablas, pero no trasladan usuarios, proyectos, tareas ni eventos. Para conservarlos hay que exportar MySQL en el equipo antiguo e importarlo en el nuevo.

### 12.1 Crear la copia en el equipo antiguo

Desde la carpeta de TaskFlow:

```powershell
docker compose exec mysql sh -c 'mysqldump -utaskflow -ptaskflow_dev --single-transaction --routines --triggers taskflow > /tmp/taskflow-backup.sql'
docker compose cp mysql:/tmp/taskflow-backup.sql .\taskflow-backup.sql
docker compose exec mysql rm /tmp/taskflow-backup.sql
```

Confirmar que `taskflow-backup.sql` existe y tiene contenido. Este archivo contiene datos personales y hashes de contraseñas; debe guardarse y trasladarse de forma privada.

### 12.2 Restaurar en el equipo nuevo

Primero instalar y arrancar TaskFlow normalmente. Copiar `taskflow-backup.sql` a la carpeta del proyecto y ejecutar:

```powershell
docker compose cp .\taskflow-backup.sql mysql:/tmp/taskflow-backup.sql
docker compose exec mysql sh -c 'mysql -utaskflow -ptaskflow_dev taskflow < /tmp/taskflow-backup.sql'
docker compose exec mysql rm /tmp/taskflow-backup.sql
docker compose exec app php artisan migrate --force
```

Después iniciar sesión con una cuenta que existiera en el equipo anterior. Es normal que las sesiones antiguas no continúen abiertas.

No subir `taskflow-backup.sql` a GitHub. Puede borrarse de la carpeta del proyecto cuando se haya guardado una copia segura y se haya comprobado la restauración.

## 13. Configuración de correo local

La configuración predeterminada utiliza Mailpit y no necesita cuentas ni contraseñas:

```dotenv
MAIL_MAILER=mailpit
MAIL_FROM_ADDRESS="no-reply@taskflow.local"
MAILPIT_HOST=mailpit
MAILPIT_PORT=1025
```

El correo se consulta en `http://localhost:8025`. Mailpit no entrega mensajes a Internet y solo es válido para el entorno local. La futura configuración SMTP externa se explica en `CONFIGURACION_CORREO.md`.

## 14. Seguridad del entorno

- No subir `web/.env`, copias SQL ni credenciales a GitHub.
- Mantener Mailpit limitado al propio equipo.
- No abrir los puertos de TaskFlow en el router.
- `APP_DEBUG=true` es apropiado para desarrollo local, pero deberá desactivarse antes de un despliegue real.
- Mantener Windows, WSL y Docker Desktop actualizados.
- Realizar una copia antes de actualizar si la base contiene información importante.
- El acceso por Internet y la operación 24/7 necesitan una configuración de producción diferente; no basta con abrir el puerto 8000.

## 15. Resolución de problemas

### Docker no responde

Abrir Docker Desktop y esperar a que el motor esté iniciado. Después:

```powershell
docker info
docker compose ps
```

Si continúa fallando:

```powershell
wsl --update
wsl --shutdown
```

Volver a abrir Docker Desktop.

### Un puerto está ocupado

Comprobar, por ejemplo, el puerto 8000:

```powershell
Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue
```

Cerrar la aplicación que lo utiliza o cambiar los puertos como se explica en el apartado 9.

### TaskFlow no abre

```powershell
docker compose ps -a
docker compose logs --tail 200 app mysql node
```

Si MySQL todavía se está preparando, esperar y volver a consultar los registros. Para reiniciar sin borrar datos:

```powershell
docker compose down
docker compose up --build -d
```

### No aparecen los estilos o el calendario

```powershell
docker compose restart node
docker compose exec node npm install
docker compose exec node npm run build
```

Actualizar la página con `Ctrl+F5`.

### No aparece el correo de recuperación

```powershell
docker compose ps mailpit
docker compose logs --tail 100 mailpit app
docker compose exec app php artisan config:clear
```

Confirmar que se está utilizando el correo exacto de una cuenta registrada y abrir `http://localhost:8025`.

### GitHub rechaza la descarga

Comprobar que la cuenta iniciada tiene acceso al repositorio. Git for Windows incluye Git Credential Manager y puede abrir el navegador para autorizar el equipo. Después repetir:

```powershell
git clone https://github.com/Vivas1998/TaskFlow.git
```

## 16. Desinstalación o reinicio total

Para parar TaskFlow sin perder información:

```powershell
docker compose down
```

El siguiente comando elimina también los volúmenes, incluida la base de datos. Solo debe utilizarse después de comprobar una copia de seguridad o cuando se quiera empezar completamente desde cero:

```powershell
docker compose down --volumes
```

Después se puede borrar la carpeta `TaskFlow`. Las imágenes de Docker pueden conservarse porque no afectan a los datos y aceleran instalaciones futuras.

## 17. Referencias oficiales

- [Instalar WSL](https://learn.microsoft.com/windows/wsl/install)
- [Instalar Docker Desktop en Windows](https://docs.docker.com/desktop/setup/install/windows-install/)
- [Instalar Git en Windows](https://git-scm.com/install/windows)
- [Clonar un repositorio de GitHub](https://docs.github.com/repositories/creating-and-managing-repositories/cloning-a-repository)
