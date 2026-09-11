# Registro de decisiones de TaskFlow

## ADR-001 — Aplicación monolítica MVC

- **Fecha:** 10 de septiembre de 2026.
- **Estado:** aceptada.
- **Decisión:** utilizar PHP 8.5 y Laravel 13 con MVC, vistas Blade, HTML semántico, JavaScript modular y CSS BEM.
- **Motivo:** se ajusta a la experiencia del responsable, incorpora mecanismos maduros de autenticación, permisos, correo, migraciones y pruebas, y reduce la cantidad de piezas de la versión 1.0.

## ADR-002 — MySQL 8.4 LTS

- **Fecha:** 10 de septiembre de 2026.
- **Estado:** aceptada.
- **Decisión:** utilizar MySQL 8.4 LTS y mantener bases independientes para desarrollo y pruebas.
- **Motivo:** satisface el modelo relacional previsto, aprovecha experiencia previa y evita aprender otro motor sin una ventaja necesaria para TaskFlow 1.0.

## ADR-003 — Entorno local con Docker Compose

- **Fecha:** 10 de septiembre de 2026.
- **Estado:** aceptada.
- **Decisión:** ejecutar PHP, MySQL y las herramientas de recursos web mediante Docker Compose.
- **Motivo:** fija versiones, evita depender de XAMPP, separa desarrollo y pruebas y permite reproducir el entorno en un futuro portátil y, más adelante, reutilizar conocimientos en el homelab.

## ADR-004 — Sin despliegue público en la versión 1.0

- **Fecha:** 10 de septiembre de 2026.
- **Estado:** aceptada.
- **Decisión:** completar, probar y documentar la versión 1.0 en desarrollo local; diseñar servidor, acceso remoto y presupuesto en un hito posterior.
- **Motivo:** concentra el trabajo hasta el 31 de diciembre en el producto y evita cerrar prematuramente decisiones de infraestructura aún sin inventario.

## ADR-005 — Recuperación de contraseña durante 60 minutos

- **Fecha:** 10 de septiembre de 2026.
- **Estado:** aceptada.
- **Decisión:** el enlace de recuperación será temporal, válido durante 60 minutos y utilizable una sola vez. La respuesta de solicitud no revelará si el correo pertenece a una cuenta y, tras cambiar la contraseña, se cerrarán sus sesiones anteriores.
- **Motivo:** ofrece tiempo suficiente para completar el recorrido local sin prolongar innecesariamente la validez de una credencial sensible.

## ADR-006 — Correo nativo de Laravel y buzón SMTP local

- **Fecha:** 11 de septiembre de 2026.
- **Estado:** aceptada.
- **Decisión:** utilizar el sistema de correo de Laravel, basado en Symfony Mailer, y Mailpit como buzón SMTP del entorno local. La entrega a una bandeja externa se configurará después con un proveedor SMTP gratuito, inicialmente Brevo, sin cambiar el recorrido de la aplicación.
- **Motivo:** mantiene el correo integrado con la configuración, las notificaciones y las pruebas de Laravel; permite validar el mensaje y su enlace sin exponer credenciales ni depender de Internet durante el desarrollo cotidiano.

## ADR-007 — Recuperación local en 1.0 y relé central de correo en 2027

- **Fecha:** 11 de septiembre de 2026.
- **Estado:** aceptada; sustituye la obligación de entrega externa incluida inicialmente en el RNF-05.
- **Decisión:** aceptar Mailpit, limitado al PC de desarrollo, como canal de recuperación de TaskFlow 1.0. Mantener Brevo como alternativa opcional. Durante 2027 se diseñará un servicio SMTP central en el homelab para varias aplicaciones, preferiblemente como relé autenticado con credenciales independientes por aplicación y una salida externa especializada.
- **Motivo:** la versión 1.0 no tendrá acceso remoto y Mailpit ya permite validar el mensaje y el enlace completo con coste cero. Un relé central simplifica la administración futura; delegar la entrega pública evita asumir desde el primer día los problemas de reputación de IP, DNS inverso, autenticación del dominio y filtrado de spam de un servidor de correo completo.
