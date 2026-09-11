# Guía rápida de TaskFlow 1.0

## Acceso y cuenta

1. Abre `http://localhost:8000`.
2. Registra una cuenta con nombre, apellidos, correo único y contraseña, o inicia sesión si ya tienes una.
3. Desde `Mi cuenta` puedes consultar tus datos y cambiar la contraseña confirmando la actual.

Si olvidas la contraseña, selecciona `He olvidado mi contraseña`, introduce tu correo y abre `http://localhost:8025`. El mensaje aparecerá en Mailpit y contendrá un enlace de un solo uso válido durante 60 minutos.

## Proyectos y miembros

- Cualquier usuario puede crear un proyecto y pasa a ser su propietario.
- El propietario puede añadir a otro usuario ya registrado mediante el botón `+` de la tarjeta del proyecto.
- Los propietarios administran miembros y roles; los miembros colaboran con tareas y eventos.
- Todos los miembros ven el contenido completo del proyecto.
- Un proyecto archivado queda en modo de solo lectura y puede restaurarse.

## Tareas y subtareas

1. Entra en un proyecto y selecciona `Nueva tarea`.
2. Indica nombre, descripción, fecha límite opcional, prioridad, responsables y, si corresponde, recurrencia.
3. Activa `Mostrar en calendario` para incluir una tarea con fecha límite en el calendario.
4. Añade las subtareas necesarias; cada una puede estar pendiente o completada.

La casilla de una tarea permite completarla rápidamente. El selector permite cambiarla entre `Sin empezar`, `En progreso`, `Completada` y `Cancelada`. Las tareas se agrupan por estado y se ordenan después por prioridad. Los filtros permiten buscar por texto, estado, responsable, prioridad y fecha.

Las tareas recurrentes crean su siguiente ocurrencia al completar o cancelar la actual. En la versión 1.0, las tareas y eventos enviados a la papelera pueden restaurarse y no se eliminan permanentemente.

## Eventos y calendario

- Los eventos siempre aparecen en el calendario.
- Al crear o editar un evento puede elegirse uno de ocho colores.
- Las tareas solo aparecen cuando tienen fecha límite y está activada la opción `Mostrar en calendario`.
- Se puede navegar hacia meses anteriores para consultar tareas finalizadas y eventos pasados.

## Limitaciones conocidas de la versión 1.0

- Funciona únicamente en el entorno local; no hay acceso desde Internet ni servicio permanente 24/7.
- Mailpit recibe los mensajes solo dentro del PC de desarrollo y no entrega correo a Internet.
- Los eventos no tienen recurrencia.
- El correo no se verifica durante el registro.
- No existe eliminación permanente de proyectos, tareas o eventos.
- El calendario de alimentación queda reservado para una versión futura.
- La reproducción de la instalación en un segundo ordenador se verificará cuando esté disponible el portátil.

## Mejoras reservadas para versiones futuras

- Despliegue en el homelab y acceso remoto seguro.
- Servicio central de correo para varias aplicaciones.
- Calendario de alimentación.
- Recurrencia de eventos.
- Alertas y confirmaciones visuales mediante SweetAlert2.
