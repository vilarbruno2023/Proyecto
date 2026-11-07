# Casos de prueba a ejecutar en XAMPP

Marcar `[x]` solo después de comprobar el resultado en el equipo del proyecto; no se han ejecutado pruebas de integración con MariaDB en este entorno.

- [ ] Importar `schema.sql` y luego `data.sql` sin errores; confirmar tablas y claves foráneas.
- [ ] Ingresar con `admin` / `Cambiar12345!` y entrar al panel. Rechazar contraseña incorrecta.
- [ ] Comprobar que un visitante sin sesión no puede entrar a `pagina.php?pagina=usuarios` ni usar las APIs privadas.
- [ ] Comprobar que `http://localhost/Proyecto/Privado/Presentacion/usuarios.html` responde 403 (regla `.htaccess`).
- [ ] Comprobar que un enlace directo a `Documentos/nombrearchivo.pdf` responde 403 y que `ver_documento.php?id=N` sirve los vigentes.
- [ ] Ingresar como documentación: subir un PDF válido de hasta 12 MB y comprobar que aparece en **Revisar**.
- [ ] Probar un archivo de otro formato, uno renombrado a `.pdf` y otro mayor a 12 MB; verificar que PHP los rechaza.
- [ ] Ingresar como doctor: aprobar indicación y comprobar que se ve en el índice público; rechazar otra y comprobar que no se ve.
- [ ] Buscar indicaciones por texto y filtrar por categoría; revisar el enlace **Ver archivo**.
- [ ] Enviar encuesta pública sin iniciar sesión; verificar en base de datos `encuesta_respuesta` y resultados del administrador.
- [ ] Crear una cuenta desde el panel y comprobar que `contrasenia` es un hash.
- [ ] Registrar un chofer, enfermero y ambulancia; asociarlos a un traslado.
- [ ] Registrar traslado de paciente con origen, destino, ruta y zona roja. Verificar que inicia en **Pendiente**.
- [ ] Pasar un traslado a **En curso**, después a **Finalizado**; verificar hora de salida y llegada.
- [ ] Cancelar un traslado pendiente; comprobar que no deja volver a cambiar estado.
- [ ] Buscar historial por nombre del paciente y por vehículo.
- [ ] Probar el índice en ancho de celular y en monitor de escritorio sin pérdida de contenido.
- [ ] Obtener capturas reales del recorrido y completar la hoja testigo.

## Validaciones automatizadas efectuadas fuera de XAMPP

- PHP: revisión con `php -l`; servidor de desarrollo PHP respondió 200 en página pública, 401 en APIs sin sesión y redirigió la página privada al login.
- JavaScript: revisión con `node --check`.
- Integridad: archivos CSS comparados mediante SHA-256 contra `Proyecto(2).zip`.

**No equivale a una prueba de funcionamiento de MariaDB + XAMPP.**
