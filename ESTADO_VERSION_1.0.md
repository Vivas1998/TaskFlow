# Estado de aceptación de TaskFlow 1.0

**Última revisión:** 11 de septiembre de 2026
**Estado:** versión 1.0 aceptada

## Aceptado

- Interfaz comprobada en móvil desde 360 px, tableta y escritorio.
- Navegación lateral compacta hasta 900 px, incluido el ancho de tableta de 768 px.
- Compatibilidad visual aceptada en Chrome, Edge, Firefox y Safari.
- Navegación por teclado, foco visible, formularios, contraste y zoom al 200 % aceptados.
- Los siete recorridos funcionales, permisos y aislamiento entre proyectos están cubiertos por la suite automatizada y la revisión manual.
- Rendimiento aceptado con los datos representativos disponibles en desarrollo.
- Suite completa: 35 pruebas y 151 aserciones superadas.
- Formato PHP: 61 archivos conformes.
- Compilación de CSS y JavaScript finalizada correctamente.
- Composer: ningún aviso de seguridad conocido.
- npm, incluidas las dependencias de desarrollo: 0 vulnerabilidades conocidas.
- `composer.json` válido.
- `web/.env` está excluido del repositorio y no se han detectado credenciales reales, claves privadas ni tokens en los archivos publicables.
- El enlace de recuperación está configurado durante 60 minutos.
- El recorrido completo de recuperación se ha validado manualmente en Mailpit: recepción del mensaje, apertura del enlace, cambio de contraseña e inicio de sesión.
- La guía de usuario y las limitaciones conocidas están documentadas.
- Una copia limpia del commit de entrega ha creado su configuración, instalado las dependencias, generado su clave, aplicado las nueve migraciones y respondido correctamente.
- La suite de 35 pruebas y la compilación se han repetido correctamente dentro de esa copia limpia.

## Entrega

La versión se identifica mediante el commit `Release TaskFlow 1.0` y la etiqueta `v1.0.0`. Puede descargarse y arrancarse siguiendo únicamente `INSTALACION.md`.

La validación en un segundo ordenador se realizará cuando el portátil esté disponible y se conserva como limitación conocida. No es necesario crear datos de ejemplo para usar TaskFlow: las migraciones reconstruyen toda la estructura y las semillas son opcionales.
