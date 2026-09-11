# TaskFlow

**Año objetivo:** 2026
**Estado:** Versión 1.0 completada
**Prioridad:** crítica

## Objetivo

Crear una aplicación web colaborativa para organizar proyectos pequeños de entre 2 y 8 personas y tener su versión 1.0 completamente funcional, probada y documentada en desarrollo antes del 31 de diciembre de 2026.

## Alcance funcional acordado

- Todo el trabajo se organiza dentro de proyectos independientes.
- Cualquier persona puede registrar una cuenta con nombre, apellidos, correo electrónico no duplicado y contraseña.
- Los usuarios autenticados solo pueden consultar los proyectos a los que pertenecen.
- Los roles de proyecto son `Propietario` y `Miembro`.
- Cualquier usuario registrado puede crear un proyecto y se convierte en su primer propietario.
- Los propietarios administran el proyecto, sus miembros y sus propietarios. Todo proyecto conserva al menos un propietario.
- Los miembros pueden crear y modificar tareas y eventos, cambiar estados y asignaciones, pero no administrar el proyecto.
- Todos los miembros ven todas las tareas y eventos de sus proyectos, aunque no tengan asignada una tarea concreta.
- Cada tarea pertenece obligatoriamente a un proyecto.
- Una tarea puede asignarse a una o varias personas del mismo proyecto.
- Las tareas incluyen, al menos, nombre, descripción, fecha límite opcional, prioridad opcional, estado, personas asignadas y recurrencia opcional.
- Las tareas pueden dividirse en subtareas, cada una pendiente o completada.
- Los estados de la versión 1.0 son `Sin empezar`, `En progreso`, `Completada` y `Cancelada`.
- Cualquier miembro puede cambiar libremente el estado de una tarea y reabrir una tarea completada o cancelada.
- Las prioridades disponibles son `Baja`, `Media`, `Alta` y `Urgente`.
- Los eventos son elementos independientes de las tareas y siempre aparecen en el calendario.
- Los eventos incluyen título, descripción opcional, inicio, final opcional y opción de día completo. No son recurrentes en la versión 1.0.
- Una tarea solo aparece en el calendario cuando se activa `Mostrar en calendario`; para ello debe tener una fecha límite.
- Las tareas con fecha límite pueden repetirse con frecuencia diaria, semanal, mensual o anual, con final opcional. Al completar o cancelar una ocurrencia se crea la siguiente como `Sin empezar` sin detener la serie.
- La aplicación permite filtrar por proyecto, estado, responsable, prioridad y fecha, y buscar tareas por nombre.
- Por defecto, las tareas aparecen ordenadas por estado —en progreso, sin empezar, completadas y canceladas— y después por prioridad.
- Cada estado dispone de un grupo desplegable y las tareas cambian de grupo al actualizarse.
- Las tareas de una sola persona usan su color estable y las compartidas usan un color genérico. Cada evento permite elegir uno de ocho colores. Las tareas completadas y canceladas se muestran con fondos claros y texto tachado verde o rojo.
- Los propietarios pueden añadir miembros desde el botón `+` de cada tarjeta en `Mis proyectos`; el formulario se abre en una pantalla propia.
- Los proyectos se archivan y quedan en modo de solo lectura. Las tareas completadas o canceladas y los eventos pasados se conservan y siguen accesibles al navegar por el historial del calendario.
- No existe eliminación permanente de proyectos, tareas ni eventos en la versión 1.0.
- La versión 1.0 se ejecutará en desarrollo desde el PC actual. El despliegue en un servidor permanente y el acceso a través de Internet se abordarán posteriormente.
- No habrá proyectos ni contenido públicos. El registro, el inicio de sesión y la recuperación de contraseña serán las únicas funciones accesibles sin autenticación dentro del entorno local.
- Una contraseña olvidada se recupera mediante un enlace temporal y de un solo uso enviado al correo electrónico de la cuenta. El correo no se verifica durante el registro en la versión 1.0.
- La sección `Mi cuenta` permite consultar los datos personales y cambiar la contraseña después de confirmar la actual.
- El calendario de alimentación para organizar las comidas semanales queda fuera de la versión 1.0.

Los requisitos detallados y sus criterios de aceptación se mantienen en [REQUISITOS.md](REQUISITOS.md). El uso del buzón local y la alternativa SMTP futura se documentan en [CONFIGURACION_CORREO.md](CONFIGURACION_CORREO.md). El funcionamiento cotidiano se resume en [GUIA_USUARIO.md](GUIA_USUARIO.md) y el cierre de la entrega en [ESTADO_VERSION_1.0.md](ESTADO_VERSION_1.0.md).

## Entorno y preferencias

- El desarrollo se realizará inicialmente desde el PC actual y podrá incorporarse un portátil en el futuro.
- El entorno de desarrollo y el futuro servidor serán independientes.
- El servidor permanente del homelab y el acceso remoto quedan fuera del hito de la versión 1.0.
- La interfaz debe adaptarse a ordenadores, tabletas y móviles mediante una aplicación web.
- El presupuesto de toda la fase de desarrollo es de 0 €. El presupuesto de infraestructura se estudiará cuando se plantee desplegar TaskFlow en un servidor con acceso remoto.
- El responsable tiene cuatro años de experiencia como programador.
- Tiene conocimientos previos de Linux y redes de servidores que necesita refrescar, más experiencia con copias y restauraciones y ninguna experiencia práctica con contenedores.
- Tecnologías ratificadas: PHP 8.5, Laravel 13 con MVC, Blade, JavaScript modular, HTML semántico y CSS BEM.
- MySQL 8.4 LTS es el motor elegido para TaskFlow, con bases independientes para desarrollo y pruebas.
- Docker Compose mantiene las versiones y servicios aislados de XAMPP y permitirá repetir el entorno en un futuro portátil.
- El inventario comprobado del equipo se mantiene en [ENTORNO.md](ENTORNO.md).
- La comparativa y recomendación técnica se mantienen en [COMPARATIVA_TECNOLOGICA.md](COMPARATIVA_TECNOLOGICA.md).

## Dependencias

- Base de datos local de desarrollo y modelo coordinado con [Bases de datos web](../../infraestructura/2026-bases-de-datos/README.md).
- Entorno de desarrollo reproducible en el PC actual y en un posible portátil futuro.
- Buzón SMTP local Mailpit para comprobar correos de desarrollo y configuración preparada para la entrega externa mediante Brevo.

## Criterios de calidad de la versión 1.0

- Compatibilidad con versiones recientes de Chrome, Edge, Firefox y Safari.
- Interfaz utilizable desde 360 px de ancho hasta escritorio.
- Accesibilidad básica mediante teclado, foco visible, formularios etiquetados, contraste adecuado y zoom al 200 %.
- Operaciones habituales completadas en menos de dos segundos en el PC de desarrollo con datos representativos.
- Pruebas automáticas de los siete recorridos esenciales y de los permisos denegados.
- Recuperación de contraseña comprobada mediante un correo capturado localmente y un enlace temporal funcional.
- Instalación limpia reproducible mediante la documentación y migraciones de base de datos.
- Ausencia de errores críticos o graves conocidos al etiquetar la versión 1.0.

## Estado de implementación

Ya funcionan en el entorno local el registro, inicio y cierre de sesión, la sección de cuenta, el cambio y la recuperación segura de contraseña, los proyectos y sus miembros, los permisos por rol, las tareas asignables, subtareas, estados visuales, colores por responsable, prioridades, filtros, ordenación, recurrencia, papeleras, eventos con color seleccionable y el calendario unificado. El correo de recuperación se entrega mediante el SMTP local de Mailpit. Docker, MySQL y la compilación de recursos están verificados, y la suite actual supera 35 pruebas con 151 aserciones.

## Próximo hito

La versión 1.0 está funcional, probada y documentada en desarrollo. El siguiente hito se decidirá entre preparar el despliegue en el homelab o comenzar una aplicación nueva. El acceso remoto, la operación permanente y el servicio central de correo continúan fuera de esta entrega.

## Decisiones pendientes

- Qué dominio, salida SMTP y política de credenciales utilizará el servicio central de correo del homelab durante 2027.
- Qué hardware, dominio, red y presupuesto estarán disponibles en el futuro hito de despliegue.

## Documentación

Esta ficha resume el proyecto. Se actualizará al cerrar hitos, tras cambios relevantes, cuando se solicite y antes de dar el proyecto por finalizado.

Las instrucciones de uso y las limitaciones conocidas se mantienen en [GUIA_USUARIO.md](GUIA_USUARIO.md). La entrega reproducible queda registrada mediante el commit `Release TaskFlow 1.0` y la etiqueta `v1.0.0`.

## Historial documental

- **10 de septiembre de 2026:** registrada la primera definición funcional de TaskFlow 1.0 y concretados usuarios, roles, visibilidad, calendario, recurrencia, conservación del historial y exclusión del calendario de alimentación.
- **10 de septiembre de 2026:** confirmados los permisos de propietarios y miembros, campos de eventos, prioridades, filtros, búsqueda, archivado de proyectos y ausencia de eliminación permanente. El hito de requisitos continúa abierto.
- **10 de septiembre de 2026:** cerrada la definición funcional de TaskFlow 1.0 con las reglas de recurrencia, cambios de estado y recuperación de contraseña por correo. Comienza la definición de criterios no funcionales y restricciones.
- **10 de septiembre de 2026:** inventariado el PC de desarrollo y registradas la separación entre desarrollo y servidor, los dispositivos objetivo, el presupuesto inicial y las preferencias tecnológicas.
- **10 de septiembre de 2026:** fijado un presupuesto de 0 € durante el desarrollo y registrado el perfil operativo y MySQL como candidato preferido, pendiente de resolver frente a la propuesta provisional de PostgreSQL.
- **10 de septiembre de 2026:** redefinido el objetivo del 31 de diciembre como una versión 1.0 completamente funcional en desarrollo. El servidor permanente y el acceso remoto pasan a un hito posterior.
- **10 de septiembre de 2026:** acordados los criterios no funcionales y de calidad que deberá cumplir TaskFlow 1.0 en desarrollo.
- **10 de septiembre de 2026:** comparadas las tecnologías candidatas y documentada una recomendación pendiente de ratificación.
- **10 de septiembre de 2026:** ratificados Laravel 13, PHP 8.5, Blade, JavaScript modular, CSS BEM, MySQL 8.4 LTS y Docker Compose.
- **10 de septiembre de 2026:** creado el proyecto Laravel, el repositorio Git, el entorno de contenedores y la primera interfaz adaptable del panel. La compilación de recursos web finaliza correctamente; la ejecución completa queda pendiente del reinicio solicitado por Windows.
- **10 de septiembre de 2026:** completados el reinicio, WSL 2 y Docker; implementados autenticación, permisos, proyectos, miembros, tareas, recurrencia, papeleras, eventos y calendario unificado. El entorno local y 23 pruebas automáticas funcionan correctamente.
- **11 de septiembre de 2026:** corregida la carga del calendario en desarrollo y añadidas subtareas, casilla rápida de completado, colores por estado y ordenación predeterminada por estado y prioridad. La suite aumenta a 27 pruebas y 101 aserciones.
- **11 de septiembre de 2026:** agrupadas las tareas en secciones desplegables por estado para mantener compacto el listado incluso con datos abundantes.
- **11 de septiembre de 2026:** trasladada la incorporación de miembros a un botón `+` contextual en `Mis proyectos`, con un formulario independiente y adaptable.
- **11 de septiembre de 2026:** añadida la sección `Mi cuenta`, con consulta del perfil, cambio seguro de contraseña y cierre de las demás sesiones abiertas.
- **11 de septiembre de 2026:** configurado el correo nativo de Laravel con Mailpit como buzón SMTP local y redactado en español el mensaje de recuperación; Brevo queda reservado para la prueba posterior con una bandeja externa.
- **11 de septiembre de 2026:** separadas las configuraciones de Mailpit y Brevo para alternar entre pruebas locales y entrega externa sin versionar credenciales.
- **11 de septiembre de 2026:** añadidos colores estables por responsable, un color genérico para tareas compartidas y eventos, y tratamientos claros verde o rojo para tareas completadas y canceladas.
- **11 de septiembre de 2026:** aceptada la recuperación mediante Mailpit para la versión 1.0 local; la salida externa y el servicio central de correo del homelab pasan al trabajo de 2027.
- **11 de septiembre de 2026:** añadida una paleta de ocho colores seleccionables para los eventos, conservada al editar y aplicada al calendario.
- **11 de septiembre de 2026:** aceptadas las comprobaciones visuales, responsive, de accesibilidad y rendimiento realizadas en el PC de desarrollo; el menú compacto se amplía a tabletas de 768 px.
- **11 de septiembre de 2026:** documentada la guía rápida, las limitaciones conocidas y SweetAlert2 como mejora para una versión futura.
- **11 de septiembre de 2026:** auditorías de Composer y npm sin vulnerabilidades conocidas, revisión de secretos limpia, formato PHP conforme, compilación correcta y 35 pruebas con 151 aserciones superadas.
- **11 de septiembre de 2026:** validado manualmente el recorrido completo de recuperación mediante Mailpit, incluida la apertura del enlace y el cambio de contraseña.
- **11 de septiembre de 2026:** validada una instalación limpia desde el commit de entrega: configuración automática, clave, dependencias, nueve migraciones, respuesta web, 35 pruebas y compilación correctas. Se corrigió además la conservación del formato Linux del script al clonar desde Windows.
