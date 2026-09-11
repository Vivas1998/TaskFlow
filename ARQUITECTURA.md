# Arquitectura de TaskFlow 1.0

**Fecha:** 10 de septiembre de 2026
**Estado:** base aprobada; evolucionará junto al código

## 1. Enfoque

TaskFlow será una aplicación web monolítica modular. Laravel aplicará Modelo-Vista-Controlador y generará HTML en el servidor con Blade. JavaScript añadirá únicamente la interacción necesaria en el navegador y el CSS seguirá BEM.

Este enfoque permite mantener una sola aplicación desplegable, conserva una separación clara de responsabilidades y evita crear una API y una aplicación cliente independientes sin necesidad para la versión 1.0.

## 2. Componentes de desarrollo

| Componente | Tecnología | Responsabilidad |
|---|---|---|
| Navegador | HTML, CSS BEM y JavaScript modular | Interfaz adaptable y accesible |
| Aplicación | PHP 8.5 y Laravel 13 | Rutas, controladores, reglas, permisos, correo y vistas |
| Persistencia | MySQL 8.4 LTS | Datos de usuarios, proyectos, tareas, subtareas, eventos y sesiones |
| Pruebas de integración | MySQL 8.4 LTS independiente | Ejecutar pruebas sin alterar datos de desarrollo |
| Recursos web | Vite y Node.js 24 | Compilar CSS y JavaScript |
| Orquestación | Docker Compose | Fijar versiones y conectar los servicios locales |

El navegador accede a la aplicación mediante `http://localhost:8000`. MySQL de desarrollo se publica en el puerto `3307` del PC para no interferir con MariaDB de XAMPP. La base de pruebas solo está disponible dentro de la red de contenedores.

## 3. Capas MVC

- **Modelos:** representan el dominio y sus relaciones; las reglas que deban cumplirse desde cualquier entrada viven en servicios de dominio o acciones específicas.
- **Controladores:** reciben solicitudes, aplican autorización y validación, coordinan casos de uso y eligen la respuesta.
- **Vistas Blade:** presentan datos y formularios con HTML semántico; no contienen consultas ni reglas de negocio.
- **Políticas:** centralizan permisos de propietarios y miembros.
- **Migraciones:** constituyen la fuente versionada del esquema de base de datos.
- **Tareas en cola:** enviarán correo y ejecutarán trabajos que no deban retrasar una solicitud web.

## 4. Módulos funcionales implementados

1. Identidad y recuperación de contraseña.
2. Proyectos, miembros y roles.
3. Tareas, subtareas, asignaciones, estados y recurrencia.
4. Eventos y calendario unificado.
5. Archivado, papelera e historial.
6. Búsqueda y filtros.

## 5. Entornos

- **Desarrollo:** contenedores locales y datos persistentes en un volumen Docker.
- **Pruebas:** base MySQL efímera e independiente.
- **Producción:** fuera del alcance de la versión 1.0; se diseñará cuando se conozcan servidor, red y presupuesto.

Los secretos reales no se guardarán en Git. El archivo `.env.example` documenta nombres y valores de desarrollo no sensibles; cada equipo tendrá su propio `.env` ignorado.

## 6. Sección vertical operativa

La aplicación ya conecta las vistas con el dominio y MySQL. Un usuario puede registrarse, crear un proyecto, incorporar cuentas existentes, gestionar tareas y eventos y consultar el calendario unificado. Las políticas de Laravel aplican los permisos y las pruebas de integración ejercitan cada recorrido contra una base independiente.
