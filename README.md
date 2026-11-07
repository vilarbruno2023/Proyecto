# SIGSM — Sistema de Gestión de Servicios Médicos

Proyecto Full Stack de **PHP, MariaDB/, HTML, CSS y JavaScript**. La aplicación permite consultar indicaciones para pacientes, responder encuestas anónimas y gestionar traslados desde una base de datos compartida.


## 1. Requisitos de instalación

- XAMPP con **Apache y PHP**, y ** o MariaDB** activo.
- PHP con extensiones **pdo_mysql**, **fileinfo** y **mbstring**.
- Navegador web moderno. No abrir los HTML haciendo doble clic; usar la dirección `http://localhost/...`.
- Permisos de escritura para Apache en la carpeta `Proyecto/Documentos/`.
- Para subir archivos de hasta 12 MB, configurar en `php.ini` `upload_max_filesize = 12M` y `post_max_size = 14M` (o valores mayores). Reiniciar Apache después de cambiarlo.

## 2. Instalar desde cero en XAMPP

1. Descomprimir el ZIP en `C:\xampp\htdocs\`. Debe quedar `C:\xampp\htdocs\Proyecto\` con las carpetas `Negocio`, `Presentacion`, `Privado` y `Documentos`.
2. Iniciar **Apache** y **MySQL/MariaDB** en XAMPP (o iniciar la instalación independiente de MariaDB si se utiliza esa alternativa).
3. Abrir `http://localhost/phpmyadmin`.
4. Importar **primero** `Privado/Datos/schema.sql`, que crea la base de datos `proyecto` y sus tablas. Después importar **una sola vez** `Privado/Datos/data.sql`, que agrega categorías, usuarios y datos ficticios para pruebas.
5. Abrir `Privado/Datos/conexion.php` y comprobar host `127.0.0.1`, puerto `3306`, base `proyecto`, usuario y contraseña. Los valores incluidos son los habituales de XAMPP, pero pueden no coincidir con MariaDB instalada por separado.
6. Abrir `http://localhost/Proyecto/Presentacion/index.html` para consultar la parte pública.
7. Para acceder al sistema interno, entrar a **Uso interno**, o abrir `http://localhost/Proyecto/Presentacion/inicio.html`.

**Importante:** si ya tenés una base `proyecto` con datos que necesitás conservar, **no importes `schema.sql` y `data.sql` por encima sin hacer un respaldo**. Revisá la sección de actualización más abajo.

## 3. Credenciales de demostración

Las cuentas se generan **solo si se importa `data.sql`**. Todas usan la contraseña temporal **`Cambiar12345!`**:

| Usuario | Rol | Uso principal |
|---|---|---|
| `admin` | admin | Panel, usuarios, encuestas, flota, categorías e indicaciones |
| `documentacion_demo` | documentacion | Crear y editar indicaciones |
| `doctor_demo` | doctor | Aprobar o rechazar indicaciones |
| `operador_demo` | operador | Registrar y actualizar traslados |
| `funcionario_demo` | funcionario | Registrar y actualizar traslados |

Las contraseñas de la base están guardadas como **hash**. El administrador puede cambiarlas en **Usuarios**. Los datos son ficticios: antes de publicar el sistema hay que cambiar las contraseñas y quitar las cuentas de demostración.

## 4. Cómo funciona

### Indicaciones y documentos

1. Ingresar con `documentacion_demo` o `admin`.
2. Ir a **Indicaciones**, completar título, categoría, resumen y seleccionar un **PDF** (máximo 12 MB).
3. El sistema guarda el archivo físico dentro de `Documentos/`, y en la tabla `documento` guarda solamente el **nombre relativo del archivo**. La indicación empieza con estado **Revisar** y no aparece en el listado público.
4. Ingresar con `doctor_demo`, abrir **Indicaciones** y cambiar el estado a **Vigente** o **Rechazada**.
5. El público consulta solamente las indicaciones **Vigentes** desde `index.html`, puede buscar por texto o categoría y abrir el PDF.
6. El administrador puede crear categorías y eliminar indicaciones; la edición de título, categoría y resumen se hace desde la misma pantalla.

Los documentos no se leen directamente por su ruta física: la aplicación utiliza `Negocio/ver_documento.php?id=N`, que permite acceso público solamente si el estado es **Vigente**; para las indicaciones no vigentes exige sesión. Los archivos físicos y las vistas privadas tienen un `.htaccess` que bloquea el acceso directo por Apache. **Comprobar este bloqueo en XAMPP** antes de usar documentos reales.

### Encuestas

La opción **Encuestas** del inicio público permite enviar una respuesta sin nombre ni cédula. Se guardan tipo (`trasplantado`, `estandar`, `fnr`), segmento, puntuación de 1 a 5 y fecha. `admin` ve cantidad, promedio y distribución desde **Encuestas**. El formulario mantiene las opciones que tenía el proyecto original.

### Traslados

Cualquier funcionario con sesión iniciada puede registrar traslados y consultar la base de datos compartida. El formulario existente permite datos de paciente o elemento, cédula, zona roja, prioridad, origen, destino, ruta, chofer, enfermero, vehículo, horarios y observaciones. Los choferes **no usan la aplicación**: aparecen como recursos asignables.

Los únicos estados utilizados en esta entrega son **Pendiente**, **En curso**, **Finalizado** y **Cancelado**. Al registrar, queda **Pendiente**; puede pasar a **En curso** o **Cancelado**. Uno **En curso** puede pasar a **Finalizado** o **Cancelado**. Los estados finales ya no se modifican. Al comenzar y finalizar se guardan las horas correspondientes. Los filtros de historial permiten buscar por nombre de paciente y código del vehículo.

El administrador puede registrar cuentas, personal y vehículos desde las pantallas ya existentes.

## 5. Organización de carpetas

```text
Proyecto/
├── Presentacion/           HTML, CSS y JavaScript (sin cambios en el CSS original)
│   ├── index.html          Consulta pública
│   ├── inicio.html         Inicio de sesión
│   ├── formulario.html     Encuesta anónima
│   ├── pagina.php          Permite servir las vistas privadas con sesión y rol
├── Negocio/                Operaciones PHP y respuestas JSON
│   ├── api_administracion.php
│   ├── api_traslados.php
│   ├── guardar_documento.php
│   ├── guardar_encuesta.php
│   ├── login.php / logout.php
│   └── ver_documento.php
├── Privado/
│   ├── Datos/
│   │   ├── conexion.php
│   │   ├── schema.sql / data.sql / migracion_estados.sql
│   │   └── Clases/Documento.php / Clases/Traslado.php
│   └── Presentacion/       Vistas HTML internas existentes
├── Documentos/             Archivos PDF subidos
├── Documentacion/          Manual, modelo de datos, pruebas, hoja testigo
└── README.md
```

Las páginas privadas se sirven por `Presentacion/pagina.php?pagina=...`. `Privado/` no debe ser accesible directamente por URL. El archivo `Negocio/configuracion.php` contiene la ruta de esa carpeta. PHP utiliza **sesiones** y roles; JavaScript utiliza `fetch()` y `FormData`; la base se consulta con **PDO** y sentencias preparadas cuando hay datos variables.

Las dos clases PHP simples pedidas en la entrega son `Documento` (eliminar archivo y registro) y `Traslado` (consultar, crear, editar y actualizar estado). No se introdujeron frameworks.

## 6. Actualizar una instalación anterior sin borrar registros

Si tu base ya usa la estructura de `Privado/Datos/bdd.sql` incluida en `Proyecto(2).zip`, hacé una **exportación de respaldo** desde phpMyAdmin y ejecutá **solo** `Privado/Datos/migracion_estados.sql`. Convierte los estados anteriores: `solicitado/programado` a `Pendiente`, `en_curso/demorado` a `En curso`, `completado` a `Finalizado` y `cancelado` a `Cancelado`. La conversión conserva los registros, pero reduce los estados según la nueva consigna.

**No ejecutar `data.sql` de nuevo** sobre una base con usuarios de demostración ya existentes. No reemplazar `Documentos/` si allí tenés archivos subidos de la instalación anterior.

Si la base tiene otra estructura, hay que comparar tablas y campos **antes** de migrarla: este script no pretende resolver diferencias de instalaciones desconocidas.

## 7. Seguridad y limitaciones

- Autenticación: `password_hash()` para registrar/cambiar claves y `password_verify()` para ingresar.
- Sesiones: se verifican en PHP al acceder a vistas y operaciones privadas.
- SQL: consultas preparadas PDO con parámetros en operaciones que reciben datos del usuario.
- Archivos: solo se admiten PDF y se comprueba el límite de 12 MB en PHP; los nombres se generan de forma única. La aplicación guarda en SQL una ruta/nombre relativo, no BLOB.
- No cargar datos personales reales en una demo escolar. El sistema **no debe presentarse como software clínico listo para producción** sin auditoría adicional de privacidad y seguridad.
- Se conserva el diseño visual original; no se agregaron frameworks ni se reestructuraron las páginas.

## 8. Pruebas y entrega

Abrir `Documentacion/Casos_de_prueba.md` para la lista de comprobaciones manuales; `Documentacion/Manual_de_usuario.md` para usar la aplicación; `Documentacion/Modelo_de_datos.md` para el DER y las relaciones; y `Documentacion/Hoja_testigo.md` para completar con fotos reales.

Las **capturas del funcionamiento**, las fotos del equipo y las correcciones escritas de la primera y segunda entrega **no estaban dentro del ZIP fuente**; deben incorporarse con evidencia real antes de entregar. Tampoco se crean commits a nombre de otros integrantes. Para usar GitHub, revisar los cambios y realizar commits auténticos desde las cuentas del equipo.

### Cambios de esta versión

- **Sin modificación de ninguno de los cinco archivos `.css` originales** ni del diseño de las pantallas.
- Unificada la conexión activa en `Privado/Datos/conexion.php` y separado `schema.sql` de `data.sql`; retirado el esquema antiguo duplicado de `Datos/`.
- Mantenida la organización del HTML original. Solo se cambia el selector de archivos a PDF y se mantienen las opciones de estado de los traslados.
- `guardar_documento.php` devuelve JSON para trabajar con el `fetch()` existente.
- Los archivos de indicaciones se entregan mediante `ver_documento.php` (sin acceso público a los pendientes).
- Se mantiene `Documento.php` y las funciones de traslado se agrupan en una sola clase simple `Traslado.php`, eliminando el archivo duplicado de funciones.
- Eliminados scripts y endpoints antiguos **sin referencias en las páginas activas**. El historial `.git` original se conserva en el ZIP, pero los cambios nuevos no están comprometidos.

### Comprobaciones realizadas en este entorno

Se verificaron la sintaxis de los archivos PHP mediante `php -l`, la sintaxis JavaScript mediante `node --check`, que los CSS son idénticos a los del ZIP de partida y que la vista pública enlaza al PDF. **No hay servidor MariaDB ni extensión `pdo_mysql` operativa aquí**, así que la instalación y las pruebas integradas de base de datos deben realizarse en el XAMPP del equipo según `Documentacion/Casos_de_prueba.md`.
