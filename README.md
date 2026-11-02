# SIGSM

SIGSM es una aplicación web en PHP para gestionar indicaciones, encuestas de satisfacción y el seguimiento de traslados. La interfaz usa HTML, CSS y JavaScript; los datos se guardan en MySQL o MariaDB.

## Requisitos

- Un servidor web con PHP y soporte para sesiones.
- Las extensiones PHP PDO MySQL, Fileinfo y mbstring.
- MySQL o MariaDB.
- Apache/PHP configurado para aceptar cargas de archivos de hasta 12 MB.

## Instalación

1. Colocá el proyecto en el servidor web (por ejemplo, en 'htdocs' de XAMPP).
2. Importá 'Privado/Datos/bdd.sql' en MySQL/MariaDB. El archivo crea la base 'proyecto', sus tablas y las categorías iniciales.
3. Configurá host, puerto, nombre de base, usuario y contraseña de la base en 'Privado/Datos/conexion.php'.
4. 'Negocio/configuracion.php' resuelve 'Privado' como una carpeta hermana de 'Negocio'. Si ubicás 'Privado' en otro lugar, modificá allí la constante 'DIRECTORIO_PRIVADO' para que apunte a su ruta real.
5. Creá la primera cuenta con rol 'admin' en la tabla 'funcionario'. No hay una cuenta inicial incluida en el SQL. Para una instalación local, podés insertar una contraseña temporal en texto plano:

   INSERT INTO funcionario (nombreUsuario, contrasenia, rol)
   VALUES ('admin', 'REEMPLAZAR_POR_UNA_CLAVE_TEMPORAL', 'admin');

   Al iniciar sesión correctamente, 'Negocio/login.php' convierte esa contraseña a un hash. Cambiala después desde la sección **Usuarios**.
6. Abrí 'Presentacion/index.html' desde el servidor web.

La carpeta 'Documentos' debe existir y permitir que PHP guarde allí los PDF subidos.

## Funcionalidades disponibles

- **Indicaciones:** el rol 'documentacion' puede cargar PDF de hasta 12 MB; se registran inicialmente con estado 'Revisar'. El rol 'doctor' puede cambiar el estado a 'Vigente' o 'Rechazada'. El rol 'admin' puede administrar categorías y eliminar indicaciones. Las indicaciones vigentes se muestran en la parte pública del sitio.
- **Encuestas:** el formulario público guarda tipo de encuesta, servicio o programa, satisfacción de 1 a 5 y fecha. No pide nombre ni cédula. La consulta de respuestas está disponible para administradores.
- **Traslados:** los usuarios con sesión iniciada pueden registrar traslados de pacientes o elementos y actualizar su estado. La interfaz permite iniciar, demorar, reanudar, completar o cancelar un traslado según su estado actual. Origen, destino y ruta se ingresan manualmente. El esquema también define el estado 'programado', pero la interfaz actual no ofrece una acción para programar un traslado.
- **Usuarios:** el administrador puede crear cuentas con los roles 'admin', 'documentacion', 'doctor', 'operador' y 'funcionario', y cambiar sus contraseñas. Las contraseñas creadas desde el panel se almacenan con 'password_hash'.
- **Personal y vehículos:** el administrador puede registrar y activar o desactivar personal y vehículos. El formulario actual de traslados no permite asignarlos; las asignaciones que aparezcan en la base no se gestionan desde esa pantalla.

## Acceso por rol

- 'admin': panel, usuarios, encuestas, personal y vehículos, indicaciones y traslados.
- 'documentacion': indicaciones y traslados; puede crear indicaciones.
- 'doctor': indicaciones y traslados; puede revisar indicaciones.
- 'operador' y 'funcionario': traslados.

Las páginas privadas se sirven mediante 'Presentacion/pagina.php', que verifica sesión y rol. Para que los archivos de 'Privado' no queden accesibles directamente desde el servidor web, ubicá esa carpeta fuera del directorio público y ajustá 'DIRECTORIO_PRIVADO'.

## Estructura principal

- 'Negocio/': endpoints PHP, conexión/configuración y lógica de traslados.
- 'Presentacion/': páginas públicas, JavaScript y estilos.
- 'Privado/Datos/': esquema SQL, conexión a la base y clase para documentos.
- 'Privado/Presentacion/': HTML de las páginas protegidas.
- 'Documentos/': PDF de indicaciones cargados por la aplicación.
