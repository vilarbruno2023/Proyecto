# SIGSM — Sistema de indicaciones

## Estructura principal

- `Presentacion/index.html`: página pública, sin validación de sesión. Muestra únicamente indicaciones vigentes y permite filtrar por texto y área.
- `Presentacion/inicio.html`: formulario de inicio de sesión para acceder al área interna.
- `Presentacion/SIGSM.html`: panel administrativo protegido; requiere una sesión válida con rol `admin`.
- `Presentacion/estilos.css`: hoja de estilos compartida por la página pública y el panel administrativo.
- `Presentacion/cargar_indicaciones_publicas.js`: carga y filtra el listado público.
- `Presentacion/cargar_tabla_indicaciones.js`: carga la tabla administrativa.
- `Negocio/obtener_indicaciones_vigentes.php`: consulta exclusivamente las indicaciones con estado `Vigente`.
- `Negocio/obtener_tabla_indicaciones.php`: consulta la tabla completa para administración.
- `Negocio/verificar_sesion.php`: devuelve el estado de la sesión para proteger el panel administrativo.
- `Datos/conexion.php`: conexión PDO a MariaDB.
- `Datos/bdd.sql`: esquema para crear una base de datos nueva, sin campos QR.
- `Datos/migracion_eliminar_qr.sql`: migración para bases existentes que aún tienen la columna `codigoQR`.

## Acceso

La página `index.html` es pública: no ejecuta comprobaciones de sesión ni exige iniciar sesión. El enlace **Uso interno** lleva al formulario de acceso. Al abrir directamente `SIGSM.html`, el sistema verifica la sesión en el servidor y redirige al inicio de sesión si no hay una sesión válida o si el usuario no tiene rol administrativo.

## Base de datos

Configurá las credenciales de MariaDB en `Datos/conexion.php`. Para una base ya existente que aún contiene la columna QR, ejecutá una sola vez `Datos/migracion_eliminar_qr.sql` desde phpMyAdmin.
