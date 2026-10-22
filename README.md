# SIGSM — encuestas y módulo de traslados

## Instalación
1. Copiar `Proyecto` a la carpeta `htdocs` de XAMPP.
2. Ajustar `Datos/conexion.php` para MariaDB.
3. Para una base nueva, importar `Datos/bdd.sql` en phpMyAdmin. Esta versión incluye encuestas, personal, vehículos y traslados.
4. Crear o conservar un usuario en `funcionario` con rol `admin` para administrar el sistema. Si la base está vacía, insertá temporalmente un administrador desde phpMyAdmin con:
   ```sql
   INSERT INTO funcionario (nombreUsuario, contrasenia, rol)
   VALUES ('admin', 'Cambiar12345!', 'admin');
   ```
   Iniciá sesión con esa contraseña temporal y cambiala inmediatamente desde «Usuarios y credenciales». El primer acceso convertirá automáticamente la contraseña de texto plano a un hash seguro.

## Encuestas
Formulario público breve: tipo, segmento y satisfacción (1–5). No solicita ni guarda nombre o cédula. El identificador es la fecha-hora con microsegundos. Las respuestas se ven en tres segmentos en `Presentacion/administrar_encuestas.php`, solo para administradores.

## Traslados
- `Presentacion/traslados.php`: usuarios con sesión pueden crear solicitudes; los roles `admin` y `operador` pueden asignar recursos y actualizar estado/horarios.
- `Presentacion/administrar_flota.php`: solo administradores registran y activan/desactivan doctores, choferes, enfermeros, administrativos y vehículos.
- La cédula se ingresa manualmente; no se consume API. El origen, destino y ruta se ingresan manualmente. No hay integración con mapas ni cálculo automático de rutas.
- El formulario separa solicitud de la gestión operativa. El enfermero se asigna como responsable del paciente y el chofer puede conducir cualquier vehículo compatible.
- Se valida que el chofer seleccionado sea personal con función de chofer, el enfermero sea personal con función de enfermero y que un utilitario no sea asignado a un traslado de paciente. Las reglas reales de compatibilidad deberán ajustarse a las normas del hospital.

## Importante
Es un prototipo funcional inicial, no un sistema clínico certificado. Antes de usar datos reales, revisar autenticación, contraseñas, autorización, auditoría, copias de seguridad y privacidad. Probar en una base de pruebas antes de migrar información existente.

## Cambios de esta versión preliminar corregida
- Roles de cuenta: `admin`, `documentacion`, `doctor`, `operador` y `funcionario`.
- Solo `documentacion` puede crear indicaciones; siempre nacen en `Revisar`.
- Solo una cuenta `doctor` puede cambiar el estado de una indicación. Solo `Vigente` se muestra en el índice público.
- Las categorías se consultan desde `categoria`; el administrador puede agregar nuevas desde `Presentacion/indicaciones.php`.
- Las cuentas se crean desde `Presentacion/administrar_usuarios.php`, con nombre de usuario y contraseña. Las contraseñas nuevas se guardan con `password_hash`; las cuentas antiguas en texto plano se convierten a hash al iniciar sesión correctamente.
- Los traslados incluyen nombre del paciente y marca de zona roja; la lista se presenta en tarjetas adaptables.
- Si ya tenés la base de datos creada, hacé una copia de seguridad y ejecutá una sola vez `Datos/migracion_mejoras.sql` en phpMyAdmin. Para una instalación nueva, importá `Datos/bdd.sql`.
- Crear un usuario `doctor` y otro `documentacion` desde el panel de usuarios. Las cuentas de personal operativo (chofer/enfermero) son registros separados de las credenciales del sistema.

## Pruebas pendientes
La sintaxis PHP se validó con `php -l`. La conexión real a MariaDB, las rutas desde el navegador y la ejecución de la migración deben probarse en tu instalación de XAMPP. Antes de ejecutar `migracion_mejoras.sql`, hacé una exportación de la base de datos.
