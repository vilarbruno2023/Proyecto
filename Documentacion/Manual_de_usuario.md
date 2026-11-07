# Manual de usuario de SIGSM

Las pantallas corresponden a la organización original del proyecto. Reemplazar los marcadores de captura por imágenes **reales** obtenidas en XAMPP.

## Paciente

1. Abrir `Presentacion/index.html` desde el servidor.
2. Buscar la indicación por palabra clave o categoría.
3. Elegir **Ver indicación** para consultar el título, la categoría y el resumen; desde esa página, pulsar **Abrir PDF** o escanear el código QR para abrirla desde otro dispositivo.
4. Para responder una encuesta, pulsar **Encuestas**, elegir tipo, servicio y puntuación, y enviar.

_Captura pendiente: listado público, búsqueda y lectura del PDF._

## Funcionario de documentación

1. Abrir **Uso interno** e iniciar sesión.
2. Ir a **Indicaciones**; cargar título, categoría y un PDF de hasta 12 MB.
3. Confirmar que aparece en estado **Revisar**.
4. Para corregir una indicación, usar **Editar** si se tiene permiso. Se puede reemplazar el PDF; cualquier edición la devuelve a **Revisar** y requiere nueva aprobación médica.

_Captura pendiente: formulario de carga y documento en revisión._

## Doctor

1. Entrar con una cuenta de rol `doctor`.
2. Abrir **Indicaciones** y elegir **Vigente** para publicar, o **Rechazada** para retirar.
3. Pulsar **Guardar** y comprobar en el inicio público que solo se muestran las vigentes.

_Captura pendiente: revisión médica y resultado público._

## Operador de traslados

1. Iniciar sesión; abrir **Traslados**.
2. Completar paciente/elemento, origen, destino, ruta y demás datos que correspondan.
3. Registrar. El traslado se muestra **Pendiente**.
4. Desde **Seguimiento**, pasar a **En curso** y posteriormente a **Finalizado**, o cancelar si corresponde.
5. Buscar en la lista usando nombre del paciente o código de vehículo.

_Captura pendiente: registro, tarjeta de traslado y cambio de estado._

## Administrador

1. Entrar con la cuenta `admin`.
2. Desde **Usuarios**, registrar cuentas y cambiar contraseñas.
3. Desde **Personal y vehículos**, registrar recursos.
4. Desde **Encuestas**, revisar los resultados agrupados.
5. Desde **Indicaciones**, administrar categorías y eliminar registros.

_Captura pendiente: panel de administrador, usuarios y resultados de encuestas._
