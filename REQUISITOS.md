# Requisitos de TaskFlow 1.0

**Estado:** alcance funcional y calidad de la versión 1.0 acordados
**Fecha de referencia:** 10 de septiembre de 2026
**Objetivo de finalización en desarrollo:** antes del 31 de diciembre de 2026

## 1. Definición del producto

TaskFlow es una aplicación web colaborativa para organizar proyectos relativamente pequeños, formados habitualmente por entre 2 y 8 personas. Es un producto nuevo y no sustituye por ahora a un proceso o herramienta existente.

La versión 1.0 debe estar completamente funcional, probada y documentada en el entorno de desarrollo. El servidor permanente y el acceso desde Internet son objetivos posteriores y no forman parte de su alcance.

## 2. Usuarios y acceso

- Cualquier persona puede crear su propia cuenta.
- Una cuenta requiere nombre, apellidos, correo electrónico y contraseña.
- No pueden existir dos cuentas con el mismo correo electrónico.
- Todo el contenido exige autenticación y no existe contenido público.
- Cada usuario solo puede ver los proyectos a los que pertenece.
- Un usuario puede pertenecer a varios proyectos independientes.
- Los roles de proyecto son `Propietario` y `Miembro`.
- Cualquier usuario registrado puede crear un proyecto y se convierte en su primer propietario.
- Un proyecto puede tener más de un propietario y debe conservar siempre al menos uno.
- Cualquier propietario puede administrar el proyecto, incorporar cuentas registradas, retirar miembros y gestionar quién tiene el rol de propietario.
- Un miembro puede crear y modificar tareas y eventos y cambiar estados y asignaciones, pero no puede administrar el proyecto ni sus miembros.
- Todos los miembros pueden ver todas las tareas y eventos del proyecto, con independencia de quién tenga asignada cada tarea.
- El correo electrónico no necesita verificarse durante el registro en la versión 1.0.
- Un usuario autenticado puede cambiar su contraseña.
- Una persona que haya olvidado su contraseña puede solicitar un enlace de recuperación por correo electrónico.

## 3. Requisitos funcionales confirmados

### RF-01. Proyectos

La aplicación debe permitir crear y archivar proyectos e incorporar cuentas existentes como miembros. En `Mis proyectos`, cada proyecto activo administrado por el usuario muestra un botón `+` que abre un formulario específico para añadir miembros; este formulario no ocupa espacio permanente en el detalle del proyecto. Un proyecto archivado conserva su información y queda en modo de solo lectura.

### RF-02. Pertenencia obligatoria

Toda tarea debe pertenecer exactamente a un proyecto. No existirán tareas personales o sueltas fuera de un proyecto.

### RF-03. Tareas

La aplicación debe permitir crear y mantener tareas con, al menos, estos datos:

- nombre;
- descripción;
- fecha límite opcional;
- prioridad opcional;
- estado;
- una o varias personas asignadas;
- cero o más subtareas;
- configuración de repetición opcional.

Cada subtarea pertenece a una única tarea, incluye un nombre y solo puede estar `Pendiente` o `Completada`. Puede marcarse directamente desde la lista de tareas y también mantenerse al editar la tarea.

### RF-04. Asignaciones

Una tarea puede asignarse a una o varias personas, siempre que todas ellas pertenezcan al mismo proyecto que la tarea.

### RF-05. Estados

Los estados disponibles en la versión 1.0 son:

1. `Sin empezar`.
2. `En progreso`.
3. `Completada`.
4. `Cancelada`.

Cualquier miembro del proyecto puede cambiar una tarea libremente entre los cuatro estados, incluida la reapertura de tareas completadas o canceladas. Un proyecto archivado permanece en modo de solo lectura y no admite estos cambios.

Una tarea asignada a una sola persona utiliza el color estable de esa persona. Las tareas con varios responsables utilizan un color genérico de trabajo compartido. Las tareas completadas y canceladas conservan el indicador de responsable, pero muestran respectivamente un fondo claro y texto tachado verde o rojo.

La lista ofrece una casilla directa para completar o reabrir la tarea. Una tarea completada se muestra tachada en verde, una cancelada tachada en rojo y una tarea en progreso se destaca en amarillo.

### RF-06. Prioridades

Una tarea puede no tener prioridad o utilizar uno de estos valores:

1. `Baja`.
2. `Media`.
3. `Alta`.
4. `Urgente`.

### RF-07. Calendario

La aplicación debe disponer de una vista de calendario que permita consultar fechas pasadas y futuras y muestre:

- todos los eventos;
- las tareas con fecha límite que tengan activada la opción `Mostrar en calendario`.

Una tarea sin fecha límite no puede activar `Mostrar en calendario`. Las tareas completadas o canceladas y los eventos pasados siguen visibles en sus fechas correspondientes.

### RF-08. Recurrencia

La versión 1.0 debe admitir tareas repetibles con estas reglas:

- una tarea repetible debe tener fecha límite;
- la frecuencia puede ser diaria, semanal, mensual o anual;
- la serie puede no tener final o terminar en una fecha indicada;
- al completar o cancelar una ocurrencia se crea automáticamente la siguiente como `Sin empezar`;
- cancelar una ocurrencia no detiene la serie;
- la serie se detiene expresamente o cuando alcanza su fecha final;
- la siguiente ocurrencia conserva nombre, descripción, prioridad, responsables y la opción `Mostrar en calendario`;
- la nueva fecha límite se calcula desde la fecha límite de la ocurrencia anterior;
- cada ocurrencia puede generar como máximo una sucesora, aunque posteriormente se reabra y se vuelva a completar o cancelar.

### RF-09. Recuperación de contraseña

La recuperación de una contraseña olvidada debe funcionar así:

1. La persona introduce su correo electrónico.
2. La aplicación muestra siempre una respuesta genérica, exista o no la cuenta.
3. Si la cuenta existe, se envía a ese correo un enlace temporal y de un solo uso.
4. El enlace abre TaskFlow y permite establecer una contraseña nueva.
5. El enlace queda invalidado al utilizarse o al caducar.
6. La contraseña anterior deja de funcionar y se invalidan las sesiones que estuvieran abiertas.

Mientras TaskFlow solo esté disponible en desarrollo, el enlace funcionará únicamente desde un dispositivo que pueda acceder a ese entorno. La duración exacta del enlace y el servicio de correo saliente se decidirán durante el diseño de seguridad e infraestructura.

### RF-10. Eventos

Los eventos son elementos independientes de las tareas, pertenecen a un proyecto y siempre aparecen en el calendario. Deben conservarse después de su fecha para permitir la consulta histórica.

Como los eventos no tienen responsables en la versión 1.0, cada evento permite elegir su propio color entre ocho opciones: azul, verde, violeta, naranja, rosa, turquesa, rojo y gris. El color se conserva al editarlo y se utiliza en el calendario.

Un evento incluye:

- título obligatorio;
- descripción opcional;
- fecha de inicio obligatoria;
- hora de inicio obligatoria salvo que sea un evento de día completo;
- fecha y hora de finalización opcionales;
- opción de día completo.

Los eventos no son recurrentes en la versión 1.0.

### RF-11. Conservación e historial

- Los proyectos dejan de estar activos mediante archivado y conservan sus datos.
- Los proyectos archivados quedan en modo de solo lectura.
- Las tareas completadas o canceladas se conservan.
- Los eventos pasados se conservan.
- El calendario permite volver a periodos anteriores y consultar sus elementos.
- No existe eliminación permanente de proyectos, tareas ni eventos en la versión 1.0.

### RF-12. Filtros y búsqueda

La aplicación debe permitir:

- filtrar tareas por proyecto, estado, responsable, prioridad y fecha;
- buscar tareas por nombre;
- combinar los filtros aplicables.

Sin filtros activos, las tareas se ordenan primero por estado: `En progreso`, `Sin empezar`, `Completada` y `Cancelada`. Dentro de cada estado se ordenan por prioridad: `Urgente`, `Alta`, `Media`, `Baja` y, por último, sin prioridad.

Cada estado se presenta como un grupo desplegable con su número de tareas. Los grupos `En progreso` y `Sin empezar` aparecen abiertos inicialmente; `Completadas` y `Canceladas` permanecen recogidos. Al cambiar el estado de una tarea, esta pasa automáticamente al grupo correspondiente.

## 4. Recorridos esenciales

### RC-01. Crear y formar un proyecto

1. Un usuario registrado crea un proyecto.
2. El usuario obtiene el rol de propietario.
3. Desde `Mis proyectos`, pulsa el botón `+` del proyecto que desea administrar.
4. Introduce el correo de una cuenta registrada, elige su rol y la incorpora como miembro.
5. Otro propietario también puede incorporar miembros.
6. Los miembros incorporados pueden acceder al proyecto.
7. Una persona ajena al proyecto no puede verlo.

### RC-02. Crear y asignar una tarea

1. Un miembro con permiso crea una tarea dentro de un proyecto.
2. Introduce sus datos obligatorios.
3. Asigna una o varias personas que pertenecen al proyecto.
4. Puede añadir subtareas pendientes para dividir el trabajo.
5. La tarea queda visible para todos los miembros del proyecto, estén asignados o no.
6. No es posible asignarla a una persona ajena al proyecto.

### RC-03. Consultar el calendario

1. Un usuario abre el calendario.
2. Ve todos los eventos fechados de sus proyectos.
3. Ve las tareas con fecha límite que tengan activada la opción `Mostrar en calendario`.
4. No ve elementos de proyectos a los que no pertenece.
5. Puede navegar a fechas anteriores y seguir viendo tareas completadas o canceladas y eventos pasados.

### RC-04. Crear una cuenta

1. Una persona introduce su nombre, apellidos, correo electrónico y contraseña.
2. La cuenta se crea si el correo electrónico no está registrado.
3. La operación se rechaza si el correo ya pertenece a otra cuenta.
4. La persona puede autenticarse con su cuenta.

### RC-05. Archivar un proyecto

1. Un propietario archiva el proyecto.
2. El proyecto y su contenido se conservan.
3. Los miembros pueden consultar el proyecto archivado.
4. Ningún miembro puede modificar sus tareas o eventos mientras permanezca archivado.

### RC-06. Consultar y localizar tareas

1. Un miembro consulta las tareas de sus proyectos.
2. Filtra por proyecto, estado, responsable, prioridad o fecha.
3. Puede combinar filtros.
4. Puede buscar una tarea por su nombre.
5. Los resultados nunca incluyen proyectos ajenos.

### RC-07. Recuperar una contraseña olvidada

1. Una persona solicita recuperar la contraseña mediante su correo electrónico.
2. La aplicación responde sin revelar si existe una cuenta asociada.
3. La persona recibe el mensaje cuando la cuenta existe.
4. Abre el enlace desde un dispositivo con acceso a TaskFlow.
5. Establece una contraseña nueva.
6. No puede volver a utilizar el mismo enlace ni la contraseña anterior.

## 5. Criterios de aceptación iniciales

- Es posible completar los siete recorridos esenciales sin errores bloqueantes.
- Es posible registrar una cuenta con un correo no utilizado y se rechazan los correos duplicados.
- Los permisos impiden consultar proyectos ajenos y asignar tareas a personas que no pertenecen al proyecto.
- Todos los miembros ven todas las tareas y eventos de su proyecto.
- Cualquier usuario registrado puede crear un proyecto y se convierte en su propietario.
- El sistema impide que un proyecto se quede sin propietario.
- Un miembro no puede administrar el proyecto ni sus miembros.
- Solo los propietarios ven el botón `+` para añadir miembros; el formulario se abre en una pantalla propia y no aparece permanentemente en el detalle del proyecto.
- Una tarea no puede guardarse sin proyecto, nombre, descripción, estado y al menos una persona asignada.
- Una tarea puede guardarse sin fecha límite.
- `Mostrar en calendario` solo puede activarse en una tarea que tenga fecha límite.
- Una tarea admite más de una persona asignada.
- Una tarea admite subtareas y cada una conserva únicamente un estado pendiente o completado.
- Los cuatro estados acordados están disponibles y se guardan correctamente.
- Cualquier miembro puede cambiar el estado y reabrir una tarea mientras el proyecto esté activo.
- La casilla de una tarea permite completarla y reabrirla; los estilos diferencian claramente tareas en progreso, completadas y canceladas.
- El color de cada miembro se mantiene estable en avatares, tareas individuales y calendario; el trabajo con varios responsables y los eventos usan un color genérico identificado en la leyenda.
- Las tareas completadas y canceladas aparecen tachadas sobre fondos claros verdes y rojos, sin perder el indicador de responsable.
- La prioridad es opcional y solo admite los cuatro valores acordados.
- El calendario muestra siempre los eventos y aplica la regla acordada a las tareas.
- El formulario de eventos permite elegir uno de los ocho colores admitidos y el calendario representa el evento con esa selección.
- El calendario permite consultar elementos pasados sin que desaparezcan automáticamente.
- Los proyectos archivados se conservan y no admiten modificaciones.
- No es posible eliminar permanentemente proyectos, tareas ni eventos.
- Los filtros y la búsqueda solo devuelven tareas de proyectos visibles para el usuario.
- El orden predeterminado agrupa tareas por estado y después por prioridad según el orden acordado.
- Los grupos de estado pueden plegarse y una tarea cambia de grupo cuando se actualiza su estado.
- Una tarea recurrente exige fecha límite y cada ocurrencia genera como máximo una sucesora al completarse o cancelarse.
- La siguiente ocurrencia conserva los datos acordados, comienza `Sin empezar` y respeta la frecuencia y el final de la serie.
- La recuperación de contraseña no revela si una cuenta existe y utiliza un enlace temporal de un solo uso.
- Tras recuperar una contraseña, dejan de funcionar la contraseña anterior y las sesiones previas.
- Un usuario autenticado puede cambiar su contraseña desde `Mi cuenta` indicando primero su contraseña actual; las demás sesiones abiertas se cierran.
- La aplicación funciona de extremo a extremo en desarrollo y exige autenticación para todo su contenido.

HTTPS público, copias operativas, restauración de producción, supervisión y disponibilidad 24/7 se validarán en el futuro hito de despliegue.

## 6. Requisitos no funcionales de la versión 1.0

### RNF-01. Compatibilidad y diseño adaptable

- La aplicación debe funcionar con versiones recientes de Chrome, Edge, Firefox y Safari.
- La interfaz debe ser utilizable desde una anchura de 360 px hasta pantallas de escritorio.
- Los siete recorridos esenciales deben probarse al menos en un ordenador, una tableta y un móvil, utilizando dispositivos reales o emulación equivalente.

### RNF-02. Accesibilidad básica

- Las funciones esenciales deben poder utilizarse mediante teclado.
- El foco debe ser visible.
- Los controles y errores de formulario deben estar correctamente etiquetados y asociados.
- El texto y los controles deben mantener un contraste adecuado.
- El contenido debe seguir siendo utilizable con un zoom del 200 %.

### RNF-03. Rendimiento

Las operaciones habituales deben completarse en menos de dos segundos en el PC de desarrollo con un conjunto de datos representativo. El conjunto exacto y el procedimiento de medición se documentarán antes de ejecutar la prueba de aceptación.

### RNF-04. Pruebas automáticas

- Los siete recorridos esenciales deben tener cobertura automática.
- Deben existir pruebas negativas que demuestren el aislamiento entre proyectos y las restricciones de propietarios, miembros y asignaciones.
- Un fallo en estas pruebas impide etiquetar la versión 1.0.

### RNF-05. Correo de desarrollo

El recorrido completo de recuperación de contraseña debe comprobarse en Mailpit mediante la recepción local del mensaje, la apertura del enlace temporal y el establecimiento de una contraseña nueva.

La entrega por Internet no es un criterio de aceptación de la versión 1.0. Mailpit solo es válido mientras TaskFlow permanezca en el PC de desarrollo: su buzón contiene enlaces sensibles y está limitado a `127.0.0.1`. La entrega externa o el relé central del homelab se abordarán durante 2027, antes de ofrecer acceso remoto.

### RNF-06. Reproducibilidad

- Una instalación limpia debe poder completarse siguiendo únicamente la documentación del proyecto.
- La base de datos debe poder crearse desde cero mediante migraciones versionadas.
- La configuración específica y los secretos deben permanecer fuera del código y de la documentación versionada.

### RNF-07. Calidad de entrega

No puede haber errores críticos o graves conocidos al etiquetar TaskFlow 1.0. Las limitaciones menores aceptadas deben estar documentadas.

## 7. Exclusiones y asuntos pendientes

### Exclusiones confirmadas

- Acceso desde Internet en la versión 1.0.
- Despliegue en un servidor permanente durante la versión 1.0.
- Disponibilidad 24/7 y operación de producción.
- Contenido y proyectos públicos.
- Calendario de alimentación para organizar las comidas de la semana.
- Recurrencia de eventos.
- Eliminación permanente de proyectos, tareas o eventos.
- Verificación del correo electrónico durante el registro.

El calendario de alimentación se conserva como idea para una versión posterior.

### Decisiones necesarias para cerrar el alcance

El alcance funcional y los criterios de calidad están cerrados. El enlace de recuperación dura 60 minutos, su recorrido completo ha sido validado mediante Mailpit, el rendimiento ha sido aceptado con los datos representativos disponibles y una copia limpia ha reconstruido correctamente la aplicación mediante Docker y las migraciones versionadas. La infraestructura de producción se definirá en un hito posterior.

## 8. Registro de decisiones de producto

| Fecha | Decisión | Motivo |
|---|---|---|
| 10-09-2026 | Orientar TaskFlow a proyectos de 2 a 8 personas. | Es la escala de uso prevista para la primera versión. |
| 10-09-2026 | Limitar la visibilidad a los proyectos del usuario, mostrando dentro de ellos todas las tareas y eventos. | La asignación indica responsabilidad, no limita la información compartida del proyecto. |
| 10-09-2026 | Permitir autorregistro con correo electrónico único. | Cualquier persona debe poder crear su cuenta antes de que un propietario la incorpore a un proyecto. |
| 10-09-2026 | Conservar tareas finalizadas y eventos pasados. | El usuario debe poder navegar hacia atrás en el calendario y consultar el historial. |
| 10-09-2026 | Completar primero la aplicación en desarrollo y dejar el acceso por Internet fuera de la versión 1.0. | Se quiere validar primero toda la funcionalidad y abordar posteriormente su despliegue y exposición remota. |
| 10-09-2026 | Excluir el calendario de alimentación de la versión 1.0. | Se reserva para una actualización futura. |
| 10-09-2026 | Permitir que cualquier usuario cree proyectos y asignarle inicialmente el rol de propietario. | Simplifica el inicio de proyectos sin intervención administrativa. |
| 10-09-2026 | Separar los permisos de propietarios y miembros. | Los miembros colaboran con tareas y eventos, mientras los propietarios controlan la composición y el ciclo de vida del proyecto. |
| 10-09-2026 | Conservar siempre al menos un propietario por proyecto. | Evita proyectos activos sin una persona capaz de administrarlos. |
| 10-09-2026 | Archivar proyectos en modo de solo lectura y no permitir eliminación permanente. | Protege el historial y mantiene accesible la información anterior. |
| 10-09-2026 | Incluir prioridades, filtros combinables y búsqueda por nombre. | Facilita localizar y organizar tareas dentro de varios proyectos. |
| 10-09-2026 | Excluir la recurrencia de eventos de la versión 1.0. | Limita el alcance inicial; la recurrencia confirmada se aplica a tareas. |
| 10-09-2026 | Permitir cambios libres entre estados y la reapertura de tareas. | Los miembros deben poder corregir o reactivar el trabajo sin eliminar su historial. |
| 10-09-2026 | Crear la siguiente ocurrencia al completar o cancelar una tarea recurrente. | Mantiene una sola ocurrencia pendiente y permite que cancelar una fecha no detenga toda la serie. |
| 10-09-2026 | Recuperar contraseñas mediante un enlace temporal enviado por correo. | Permite que el propietario del correo recupere el acceso sin intervención manual. |
| 10-09-2026 | No verificar el correo durante el registro local de la versión 1.0. | Se confirmó el comportamiento simplificado para la primera versión local. |
| 10-09-2026 | Mantener un presupuesto de 0 € durante toda la fase de desarrollo. | La inversión se valorará cuando se prepare el despliegue en un servidor con acceso remoto. |
| 10-09-2026 | Considerar terminada la versión 1.0 cuando esté completamente funcional, probada y documentada en desarrollo. | El servidor permanente y el acceso remoto se abordarán después; si se termina antes de plazo, se decidirá entonces entre desplegar o iniciar otra web. |
| 10-09-2026 | Exigir compatibilidad multidispositivo, accesibilidad básica, respuesta inferior a dos segundos, pruebas automáticas y reconstrucción documentada. | Define una versión 1.0 verificable y no solo una lista de funciones implementadas. |
| 11-09-2026 | Añadir subtareas binarias, una casilla rápida de completado y ordenación predeterminada por estado y prioridad. | Permite dividir el trabajo, reconocer su situación de un vistazo y concentrarse primero en lo activo y urgente. |
| 11-09-2026 | Presentar las tareas en grupos desplegables por estado. | Reduce la altura del listado y mantiene las tareas finalizadas o canceladas disponibles sin que dominen la vista. |
| 11-09-2026 | Identificar responsables mediante colores estables y reservar un color genérico para elementos compartidos. | Permite reconocer rápidamente la responsabilidad tanto en las listas como en el calendario. |
| 11-09-2026 | Aceptar Mailpit como recuperación local de la versión 1.0 y aplazar el correo externo a 2027. | Evita depender de un proveedor durante el desarrollo local y conserva el recorrido completo por enlace temporal. |
| 11-09-2026 | Permitir elegir entre ocho colores al crear o editar un evento. | Facilita distinguir visualmente tipos de citas sin añadir nuevas categorías al modelo 1.0. |
| 11-09-2026 | Mover el alta de miembros a un botón `+` en cada proyecto administrable. | Mantiene despejado el detalle del proyecto y concentra la incorporación de personas en una acción contextual desde `Mis proyectos`. |
| 11-09-2026 | Incorporar una sección `Mi cuenta` con cambio seguro de contraseña. | Completa la gestión básica de la cuenta y permite renovar la credencial sin utilizar el recorrido de contraseña olvidada. |
| 11-09-2026 | Reservar SweetAlert2 para una versión posterior. | Las alertas actuales son suficientes para la versión 1.0 y la mejora visual no debe retrasar su cierre. |

## 9. Decisiones técnicas

Las tecnologías ratificadas son PHP 8.5, Laravel 13 con arquitectura MVC, vistas Blade, JavaScript modular, HTML semántico, CSS con BEM, MySQL 8.4 LTS y Docker Compose. Laravel utiliza su sistema de correo nativo con Mailpit como buzón local de la versión 1.0; la salida externa se decidirá en el hito de despliegue.

## 10. Restricciones y objetivos de entorno

- El desarrollo debe poder realizarse desde el PC actual y desde un posible portátil futuro.
- Desarrollo y futuro servidor deben ser entornos independientes.
- La versión 1.0 se ejecutará en el entorno de desarrollo del PC actual y deberá poder reproducirse en un posible portátil.
- El servidor definitivo será un equipo del homelab disponible las 24 horas, pero no forma parte del alcance de la versión 1.0.
- La aplicación debe ofrecer una interfaz web adaptable a ordenadores, tabletas y móviles.
- Toda la fase de desarrollo debe realizarse con un presupuesto de 0 €.
- El presupuesto del servidor, copias externas, alimentación eléctrica y otros elementos operativos se estudiará en el futuro hito de despliegue con acceso remoto.
- El entorno local debe ser reproducible para facilitar la incorporación de otro equipo de desarrollo.
- La documentación operativa debe permitir refrescar conocimientos de Linux y redes sin presuponer experiencia previa con contenedores.
