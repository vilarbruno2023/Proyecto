# SIGSM — encuestas y módulo de traslados

## Instalación
1. Copiar `Proyecto` a la carpeta `htdocs` de XAMPP.
2. Ajustar `Datos/conexion.php` para MariaDB.
3. Para una base nueva, importar `Datos/bdd.sql` en phpMyAdmin. Esta versión incluye encuestas, personal, vehículos y traslados.
4. Crear o conservar un usuario en `funcionario` con rol `admin` para administrar el sistema.

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
