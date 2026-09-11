# Comparativa tecnológica de TaskFlow 1.0

**Fecha:** 10 de septiembre de 2026
**Estado:** decisión ratificada el 10 de septiembre de 2026

## 1. Criterios

Las opciones se comparan por:

1. ajuste al patrón MVC solicitado;
2. aprovechamiento de la experiencia existente;
3. velocidad para completar la versión 1.0;
4. autenticación, autorización y recuperación de contraseña;
5. migraciones y compatibilidad con MySQL;
6. pruebas automáticas y seguridad por defecto;
7. interfaz adaptable con HTML, JavaScript y CSS BEM;
8. coste cero durante el desarrollo;
9. mantenimiento y soporte;
10. reproducibilidad en el PC actual y un posible portátil.

## 2. Framework de servidor

| Opción | Ventajas para TaskFlow | Inconvenientes | Valoración |
|---|---|---|---|
| Laravel 13 | MVC claro, desarrollo rápido, autenticación y recuperación de contraseña disponibles, ORM, migraciones, correo, validación, tareas programadas y buen soporte de pruebas. | Cadencia principal anual y necesidad de actualizar el PHP instalado. Sus kits visuales oficiales no siguen BEM, por lo que se crearán vistas propias. | Recomendado. |
| Symfony 7.4 LTS | MVC explícito, soporte prolongado, componentes maduros de seguridad, correo, pruebas y Doctrine. Compatible con PHP 8.2 o posterior. | Exige ensamblar más piezas y escribir más configuración para alcanzar el mismo producto inicial. | Alternativa estable. |
| PHP sin framework | Control total y uso directo de conocimientos del lenguaje. | Obliga a diseñar y mantener autenticación, CSRF, sesiones, permisos, correo, migraciones, validación y estructura MVC; aumenta plazo y riesgo. | Descartado para la 1.0. |

Laravel 13 admite PHP 8.3–8.5 y recibe correcciones de seguridad hasta marzo de 2028. Symfony 7.4 es LTS, requiere PHP 8.2 o posterior y recibe correcciones de seguridad hasta noviembre de 2029.

## 3. Versión de PHP

| Opción | Situación | Valoración |
|---|---|---|
| PHP 8.2 instalado actualmente | Permite Symfony 7.4 y Laravel 12, pero su soporte de seguridad finaliza el 31 de diciembre de 2026. | No iniciar la 1.0 sobre esta rama. |
| PHP 8.4 | Compatible con los frameworks candidatos y con soporte de seguridad hasta el 31 de diciembre de 2028. | Viable. |
| PHP 8.5 | Compatible con Laravel 13 y Symfony mantenido, con soporte activo hasta finales de 2027 y de seguridad hasta finales de 2029. | Recomendado para un proyecto nuevo. |

Se instalará la última revisión disponible de la rama elegida y se fijará la versión del proyecto.

## 4. Persistencia

| Opción | Ajuste a TaskFlow | Valoración |
|---|---|---|
| MySQL 8.4 LTS | Cubre relaciones, transacciones, restricciones, índices y volumen previstos. Aprovecha la experiencia del responsable y ofrece una rama LTS con cambios funcionales contenidos. | Recomendado. |
| PostgreSQL 18 | También cubre todos los requisitos y aporta capacidades avanzadas que podrían ser útiles en otros proyectos. Requeriría aprender otro motor sin que TaskFlow 1.0 obtenga una ventaja imprescindible. | Alternativa válida, no preferida para TaskFlow. |
| MariaDB 10.4 incluida en XAMPP | Está disponible actualmente, pero no es MySQL y usarla ocultaría diferencias entre desarrollo y el motor elegido. | No utilizar como base oficial de TaskFlow. |

MySQL 8.4 LTS queda ratificado para TaskFlow 1.0. La propuesta provisional de PostgreSQL de la documentación común deberá revisarse sin modificar esta decisión del producto.

## 5. Interfaz

- Plantillas HTML renderizadas en el servidor mediante Blade.
- JavaScript modular sin un framework de aplicación cliente para la versión 1.0.
- CSS propio organizado mediante BEM.
- Diseño adaptable desde 360 px.
- FullCalendar Standard como candidato para la vista de calendario; sus funciones estándar usan licencia MIT y no requieren presupuesto.
- No utilizar las funciones Premium de FullCalendar.

Esta aproximación evita mantener dos aplicaciones separadas —API y cliente— y conserva una progresión sencilla desde los conocimientos actuales.

## 6. Pruebas

- Pruebas unitarias y funcionales del servidor con PHPUnit.
- Pruebas de integración contra una base MySQL exclusiva para pruebas.
- Pruebas de navegador y tamaños de pantalla con Playwright.
- Comprobaciones automáticas de formato y análisis estático de PHP.
- Datos de prueba reproducibles mediante factorías y semillas.

## 7. Correo en desarrollo

- El código utilizará una interfaz de correo configurable por entorno.
- Durante el desarrollo podrá utilizarse un capturador local para inspección habitual.
- La aceptación de la recuperación de contraseña se realizará con Mailpit dentro del PC de desarrollo. La entrega mediante un servicio SMTP externo es opcional y queda fuera de la versión 1.0.
- Las credenciales permanecerán fuera del repositorio y de la documentación.

## 8. Entorno reproducible

### Opción A — Contenedores

Un entorno basado en Docker Compose o Laravel Sail puede fijar PHP, MySQL, base de pruebas y capturador de correo en una configuración reproducible. Es la opción preferida para igualar PC, futuro portátil y posterior servidor, pero introduce una herramienta nueva para el responsable.

### Opción B — Instalación nativa

Instalar PHP y MySQL directamente en Windows reduce el aprendizaje inicial, pero hace más difícil reproducir versiones, separar servicios y trasladar el proyecto a otro equipo.

Se adopta Docker Compose. Su aprendizaje y sus operaciones básicas forman parte de la documentación del proyecto.

## 9. Recomendación completa

- PHP 8.5, en la última revisión disponible.
- Laravel 13 con arquitectura MVC.
- Blade, HTML semántico, JavaScript modular y CSS BEM.
- MySQL 8.4 LTS, separado para desarrollo y pruebas.
- FullCalendar Standard, sin extensiones Premium.
- PHPUnit y Playwright.
- Composer y npm para dependencias.
- Git para control de versiones.
- Mailpit para la recuperación local de contraseña; un SMTP externo queda como alternativa futura.
- Docker Compose para aislar y reproducir los servicios.

## 10. Fuentes oficiales consultadas

- [Versiones y soporte de Laravel](https://laravel.com/framework/docs/releases)
- [Kits de inicio y autenticación de Laravel](https://laravel.com/starter-kits)
- [Instalación, bases de datos y configuración de Laravel](https://laravel.com/framework/docs)
- [Laravel Sail](https://laravel.com/framework/docs/sail)
- [Versiones soportadas de PHP](https://www.php.net/supported-versions.php)
- [Symfony 7.4 LTS](https://symfony.com/releases/7.4)
- [Seguridad de Symfony](https://symfony.com/doc/7.4/security.html)
- [Doctrine y bases de datos en Symfony](https://symfony.com/doc/7.4/doctrine.html)
- [Modelo LTS e Innovation de MySQL](https://dev.mysql.com/doc/refman/8.4/en/mysql-releases.html)
- [Licencia de FullCalendar](https://fullcalendar.io/license)
