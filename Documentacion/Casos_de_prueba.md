# Casos de prueba del sistema

Marcar `[x]` solo después de comprobar el resultado en el entorno donde se ejecutará el proyecto, con PHP y MariaDB disponibles.

- [ ] Importar `schema.sql` y luego `data.sql` sin errores; confirmar tablas y claves foráneas.
- [ ] Confirmar que los datos de prueba crean indicaciones **Vigente**, **Revisar** y **Rechazada** con sus PDFs; en el sitio público solo debe aparecer la vigente.
- [ ] Ingresar con `admin` / `Cambiar12345!` y entrar al panel. Rechazar contraseña incorrecta.
- [ ] Sin sesión, comprobar que `Presentacion/pagina.php?pagina=usuarios` redirige al inicio de sesión y que las APIs privadas responden 401.
- [ ] Comprobar que la carpeta `Privado/` está fuera del directorio público, que los PDF en `Privado/Documentos/` no se pueden abrir por URL directa y que `Negocio/ver_documento.php?id=N` sirve los vigentes y restringe los no vigentes según el rol.
- [ ] Ingresar como documentación: subir un PDF válido de hasta 12 MB y comprobar que aparece en **Revisar**.
- [ ] Probar un archivo de otro formato, uno renombrado a `.pdf` y otro mayor a 12 MB; verificar que PHP los rechaza.
- [ ] Editar una indicación **Vigente** sin reemplazar el PDF y comprobar que vuelve a **Revisar** y deja de aparecer en el sitio público.
- [ ] Reemplazar el PDF de una indicación y comprobar que el archivo nuevo queda asociado y el estado vuelve a **Revisar**. Rechazar archivos que no sean PDF o superen 12 MB.
- [ ] Probar las validaciones de JavaScript dejando solo espacios en campos obligatorios de encuesta, login, traslado y administración; comprobar que muestran un mensaje y no envían los datos.
- [ ] Probar límites de usuario, contraseña, longitudes de traslado y capacidad de vehículo; comprobar los mensajes de validación antes de enviar.
- [ ] Ingresar como doctor: aprobar indicación y comprobar que se ve en el índice público; rechazar otra y comprobar que no se ve.
- [ ] Desde el índice público, abrir **Ver indicación** y comprobar título, categoría, resumen y botón **Abrir PDF**. Una indicación no vigente no debe mostrarse.
- [ ] Abrir una indicación desde la IP local `192.168.1.77`, escanear el QR con un celular conectado a la misma red y comprobar que abre la misma indicación. Confirmar que desde fuera de esa red la IP local no es accesible.
- [ ] Buscar indicaciones por texto y filtrar por categoría; revisar el enlace **Ver indicación**.
- [ ] Enviar encuesta pública sin iniciar sesión; verificar en base de datos `encuesta_respuesta` y resultados del administrador.
- [ ] Crear una cuenta desde el panel y comprobar que `contrasenia` es un hash.
- [ ] Crear una categoría y luego intentar crear otra con el mismo nombre; comprobar que la segunda se rechaza.
- [ ] Registrar un chofer, enfermero y ambulancia; asociarlos a un traslado.
- [ ] Registrar traslado de paciente con origen, destino, ruta y zona roja. Verificar que inicia en **Pendiente**.
- [ ] Pasar un traslado a **En curso**, después a **Finalizado**; verificar hora de salida y llegada.
- [ ] Cancelar un traslado pendiente; comprobar que no deja volver a cambiar estado.
- [ ] Buscar historial por nombre del paciente y por vehículo.
- [ ] Probar el índice en ancho de celular y en monitor de escritorio sin pérdida de contenido.
- [ ] Obtener capturas reales del recorrido y completar la hoja testigo.

## Validaciones efectuadas durante el desarrollo

- PHP: revisión de sintaxis con `php -l`; también se comprobaron páginas y respuestas de API en el servidor PHP local.
- JavaScript: validaciones de formularios comprobadas en el navegador; Node.js no es un requisito del proyecto.

Estas comprobaciones no sustituyen las pruebas de integración con la base MariaDB del entorno de ejecución.