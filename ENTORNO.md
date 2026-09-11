# Entorno de TaskFlow

**Fecha del inventario:** 10 de septiembre de 2026
**Estado:** entorno de desarrollo operativo

## 1. Modelo de trabajo

- Desarrollo inicial desde el PC actual.
- Posible incorporación de un portátil como segundo equipo de desarrollo.
- La versión 1.0 se completará y validará en desarrollo, sin servidor permanente.
- El futuro entorno de servidor estará separado del desarrollo.
- El servidor del homelab, disponible las 24 horas y con acceso remoto, corresponde a un hito posterior.
- Clientes previstos: ordenadores, tabletas y móviles.

## 2. PC de desarrollo comprobado

| Elemento | Valor |
|---|---|
| Sistema | Windows 10 Pro de 64 bits, compilación 19045 |
| Procesador | Intel Core i5-12400F, 6 núcleos y 12 hilos |
| Memoria | 31,8 GB de RAM |
| Virtualización en firmware | Habilitada |
| Unidad C: | 1.863 GB totales; 454,4 GB libres durante el inventario |
| Unidad D: | 931,4 GB totales; 922 GB libres durante el inventario |

Este equipo tiene capacidad suficiente para el desarrollo local, la ejecución de pruebas y entornos virtualizados ligeros de TaskFlow. No se utilizará como servidor definitivo disponible las 24 horas.

## 3. Herramientas detectadas

| Herramienta | Estado comprobado |
|---|---|
| Git | 2.52.0 para Windows |
| PHP | 8.2.12 mediante XAMPP |
| Composer | 2.10.2 |
| Node.js | 24.13.0 |
| npm | 11.6.2 |
| Apache | 2.4.58 mediante XAMPP |
| Base de datos incluida en XAMPP | MariaDB 10.4.32 |
| Docker Desktop | 4.90.0, motor Linux operativo |
| Docker Engine / Compose | Engine 29.7.2 y Compose 5.5.1 operativos |
| WSL | 2.7.13.0, núcleo 6.18.33.2-2 y versión predeterminada WSL 2 |

Las versiones inventariadas describen el estado actual del PC; no constituyen las versiones seleccionadas para TaskFlow.

## 4. Experiencia y preferencias

- Cuatro años de experiencia como programador.
- Preferencia por una arquitectura Modelo-Vista-Controlador.
- Tecnologías consideradas inicialmente: PHP, JavaScript, HTML y CSS.
- Preferencia por la metodología BEM para organizar CSS.
- MySQL como candidato preferido para persistencia debido a su uso habitual en proyectos anteriores.
- Experiencia anterior puntual con Oracle, actualmente poco reciente.
- Formación previa en Linux y redes, especialmente orientada a servidores, que necesita refrescarse.
- Conocimientos más asentados sobre copias de seguridad y restauraciones.
- Sin experiencia práctica con contenedores; Docker es conocido solo a nivel conceptual.
- Disposición a aprender otras tecnologías si aportan una ventaja justificada.

## 5. Presupuesto

- Fase de desarrollo completa: presupuesto de 0 €, utilizando herramientas gratuitas y de código abierto.
- Servidor y operación: fuera del alcance de la versión 1.0; el presupuesto se estudiará cuando se prepare el despliegue con acceso remoto.

## 6. Inventario pendiente del servidor

- Hardware disponible o presupuesto de compra.
- Sistema operativo.
- Almacenamiento principal y destino de copias.
- Conexión de red y dirección local estable.
- Consumo eléctrico y comportamiento ante cortes.
- Servicio de correo saliente para recuperar contraseñas.
- Supervisión, actualizaciones y acceso administrativo.

## 7. Implicaciones para la selección técnica

- El entorno de desarrollo debe poder ponerse en marcha sin servicios de pago.
- La tecnología elegida debe favorecer el mantenimiento autónomo y aprovechar la experiencia existente.
- Si se adoptan contenedores, la documentación debe explicar su uso desde cero y justificar la ventaja operativa.
- No debe confundirse la base incluida en XAMPP, MariaDB 10.4.32, con una instalación de MySQL.
- MySQL 8.4 LTS queda elegido para TaskFlow. La propuesta provisional de PostgreSQL de la infraestructura común se revisará como una coordinación documental separada.

## 8. Estado de preparación

- El reinicio de Windows y la actualización del núcleo de WSL 2 se completaron correctamente.
- Docker Desktop ejecuta contenedores Linux y Docker Compose mantiene separados aplicación, recursos web, base de desarrollo y base de pruebas.
- La imagen de PHP 8.5 se construyó, MySQL 8.4.11 arrancó y todas las migraciones se aplicaron correctamente.
- El proyecto está disponible en `http://localhost:8000` y los recursos JavaScript y CSS se compilan con Vite.
- La suite automatizada utiliza una base MySQL independiente y supera 35 pruebas con 151 aserciones.
