# SIGSM

SIGSM es una aplicación web en PHP para gestionar indicaciones, encuestas de satisfacción y el seguimiento de traslados. La interfaz usa HTML, CSS y JavaScript; los datos se guardan en MySQL o MariaDB.

## Requisitos

- Un servidor web con PHP y soporte para sesiones.
- Las extensiones PHP PDO MySQL, Fileinfo y mbstring.
- MySQL o MariaDB.
- PHP configurado para aceptar archivos de hasta 12 MB y solicitudes POST mayores a ese límite.

## Instalación

1. Colocá el proyecto en el servidor web (por ejemplo, en 'htdocs' de XAMPP).
2. Importá 'Privado/Datos/bdd.sql' en MySQL/MariaDB. El archivo crea la base 'proyecto', sus tablas, las categorías iniciales y datos de ejemplo para probar encuestas y traslados.
3. Configurá host, puerto, nombre de base, usuario y contraseña de la base en 'Privado/Datos/conexion.php'.
4. El SQL incluye la cuenta inicial 'admin' con la contraseña 'Cambiar12345!'. Cambiala después del primer inicio de sesión desde la sección **Usuarios**.
5. 'Negocio/configuracion.php' resuelve 'Privado' como una carpeta hermana de 'Negocio'. Para evitar el acceso directo a sus archivos, ubicá 'Privado' fuera del directorio público y modificá allí la constante 'DIRECTORIO_PRIVADO' para que apunte a su ruta real.
6. La carpeta 'Documentos' debe existir y permitir que PHP guarde allí los PDF subidos.
7. Abrí 'Presentacion/index.html' desde el servidor web.

Para probar los permisos, el SQL también crea las cuentas 'documentacion_demo', 'doctor_demo', 'operador_demo' y 'funcionario_demo'. Todas usan la contraseña temporal 'Cambiar12345!'. Son cuentas y registros ficticios para desarrollo; no los uses en una instalación pública y eliminá las cuentas de prueba o cambiá sus contraseñas antes de publicar.

Los PDF se guardan en 'Documentos', que está dentro del directorio público del proyecto; por lo tanto, cualquier persona que tenga la dirección de un PDF puede abrirlo. No guardes allí documentos que deban permanecer privados.

## Funcionalidades disponibles

- **Indicaciones:** el rol 'documentacion' puede cargar PDF de hasta 12 MB; se registran inicialmente con estado 'Revisar'. El rol 'doctor' puede cambiar el estado a 'Vigente' o 'Rechazada'. El rol 'admin' puede administrar categorías y eliminar indicaciones. Las indicaciones vigentes se muestran en la parte pública del sitio.
- **Encuestas:** el formulario público guarda tipo de encuesta, servicio o programa, satisfacción de 1 a 5 y fecha. No pide nombre ni cédula. El administrador ve la cantidad de respuestas, el promedio y la distribución de puntajes para cada tipo de encuesta; no se muestran las respuestas individuales.
- **Traslados:** los usuarios con sesión iniciada pueden registrar y editar traslados de pacientes o elementos, asignar personal y vehículos activos, y actualizar su estado según las transiciones permitidas. Se puede programar un traslado, iniciarlo, demorar, reanudar, completar o cancelar. Origen, destino y ruta se ingresan manualmente.
- **Usuarios:** el administrador puede crear cuentas con los roles 'admin', 'documentacion', 'doctor', 'operador' y 'funcionario', y cambiar sus contraseñas. Las contraseñas creadas desde el panel se almacenan con 'password_hash'.
- **Personal y vehículos:** el administrador puede registrar y activar o desactivar personal y vehículos. En los traslados solo se pueden asignar recursos activos y compatibles con el tipo de traslado.

## Acceso por rol

- 'admin': panel, usuarios, encuestas, personal y vehículos, indicaciones y traslados.
- 'documentacion': indicaciones y traslados; puede crear indicaciones.
- 'doctor': indicaciones y traslados; puede revisar indicaciones.
- 'operador' y 'funcionario': traslados.

Las páginas privadas se sirven mediante 'Presentacion/pagina.php', que verifica sesión y rol en el servidor. La seguridad de estas páginas depende también de ubicar 'Privado' fuera del directorio público y configurar correctamente 'DIRECTORIO_PRIVADO'.

## Estructura principal

- 'Negocio/': endpoints PHP, conexión/configuración y lógica de traslados.
- 'Presentacion/': páginas públicas, JavaScript y estilos.
- 'Privado/Datos/': esquema SQL, conexión a la base y clase para documentos.
- 'Privado/Presentacion/': HTML de las páginas protegidas.
- 'Documentos/': PDF de indicaciones cargados por la aplicación.
