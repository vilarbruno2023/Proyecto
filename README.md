# SIGSM — versión con encuestas simplificadas

## Requisitos
- XAMPP con Apache, PHP y MariaDB/MySQL.
- Copiar `Proyecto` a `htdocs`.
- Ajustar `Datos/conexion.php` si el usuario o contraseña de MariaDB difieren.
- Para una instalación nueva, importar `Datos/bdd.sql` en phpMyAdmin.

## Encuestas
- `Presentacion/index.html`: buscador público y botón Encuestas.
- `Presentacion/formulario.html`: formulario anónimo breve: tipo, segmento y satisfacción de 1 a 5.
- `Negocio/guardar_encuesta.php`: valida y guarda las respuestas.
- `Presentacion/administrar_encuestas.php`: vista exclusiva para administrador, dividida en tres segmentos (trasplantados, estándar y FNR).
- El identificador de una respuesta nueva es `fechaEncuesta`, fecha y hora con microsegundos. No se guarda nombre ni cédula.

## Acceso administrativo
El panel y la página de resultados validan la sesión en PHP. El usuario debe tener rol `admin` en la tabla `funcionario`. La autenticación existente debe tener una contraseña válida según el método usado por el proyecto.

## Base existente
`Datos/migracion_encuestas_segmentadas.sql` está pensada para una tabla de encuestas anterior. Hacé una copia de seguridad antes de ejecutarla. Para instalaciones nuevas, importá `bdd.sql` y no ejecutes la migración.
