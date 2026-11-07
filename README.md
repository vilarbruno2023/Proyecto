# SIGSM — Sistema de Gestión de Servicios Médicos

Aplicación web de PHP, HTML, CSS y JavaScript para consultar indicaciones, responder encuestas anónimas y gestionar traslados. La aplicación usa MariaDB para guardar la información.

## Requisitos

- Servidor web con PHP y soporte para sesiones.
- Extensiones PHP `pdo_mysql`, `fileinfo` y `mbstring`.
- Acceso de red desde el servidor PHP al servidor MariaDB.
- La carpeta `Documentos/` dentro de la carpeta privada debe existir y permitir lectura y escritura a PHP.
- Para subir archivos de hasta 12 MB, configurar PHP con `upload_max_filesize` de al menos `12M` y `post_max_size` mayor que ese límite.

MariaDB se ejecuta en un servidor Linux separado. Este proyecto no incluye Dockerfile ni configuración de Docker Compose para la base de datos.

## Instalación

1. Colocá los archivos de la aplicación en el servidor web y la carpeta `Privado/` en una ubicación fuera del directorio público.
2. En `Negocio/configuracion.php`, cambiá `DIRECTORIO_PRIVADO` para que apunte a la ubicación real de esa carpeta privada. Los PDF se guardan en su subcarpeta `Documentos/`.
3. Asegurate de que `Privado/Documentos/` exista y que el usuario de PHP tenga permisos de lectura y escritura.
4. En el servidor MariaDB, importá `Privado/Datos/schema.sql` para crear la base `proyecto` y sus tablas.
5. Importá `Privado/Datos/data.sql` una sola vez para agregar las cuentas, categorías y datos ficticios. Para usar las indicaciones de prueba, copiá los tres PDF (`indicacion_prueba_vigente.pdf`, `indicacion_prueba_revision.pdf` e `indicacion_prueba_rechazada.pdf`) a `Privado/Documentos/`.
6. En `Privado/Datos/conexion.php`, configurá la dirección del servidor MariaDB, el puerto, el nombre de la base, el usuario y la contraseña. El valor actual `127.0.0.1` solo funciona si MariaDB está accesible desde el mismo servidor donde corre PHP.
7. Abrí `Presentacion/index.html` desde el servidor web. Para iniciar sesión, abrí `Presentacion/inicio.html` o seleccioná **Uso interno**.

`schema.sql` crea las tablas y no es una migración para una base existente. No lo importes sobre una base con datos que quieras conservar. `data.sql` contiene identificadores y nombres únicos para ejemplos; no lo importes más de una vez.

## Cuentas y datos de prueba

Todas estas cuentas tienen la contraseña temporal `Cambiar12345!`:

| Usuario | Rol |
|---|---|
| `admin` | Administrador |
| `documentacion_demo` | Documentación |
| `doctor_demo` | Doctor |
| `operador_demo` | Operador |
| `funcionario_demo` | Funcionario |

`data.sql` también incluye categorías, seis respuestas de encuesta, personal, vehículos y dos traslados ficticios. Las contraseñas se guardan en la base como hash. Estos datos son para pruebas; no uses las cuentas de demostración en una instalación pública.

## Funcionalidades

### Indicaciones

- Documentación y administración pueden crear indicaciones con un archivo PDF. Se guardan con estado **Revisar**.
- Doctor puede aprobarlas como **Vigente** o rechazarlas.
- Administración puede crear categorías, editar indicaciones y eliminar indicaciones.
- El listado público muestra solo indicaciones vigentes. Los documentos se sirven mediante `Negocio/ver_documento.php`.
- Cada indicación pública tiene un código QR generado localmente con `Presentacion/qrcode.min.js`; abre la URL actual de su página de detalle. Para probarlo en la red local, abrí el sitio desde `192.168.1.77` y conectá los celulares a la misma red. Esa IP no funciona desde internet.

### Encuestas

El formulario público permite enviar respuestas anónimas para los tipos `trasplantado`, `estandar` y `fnr`, con una puntuación de 1 a 5. Administración puede consultar cantidad de respuestas, promedio y distribución de puntajes por tipo.

### Traslados

Los usuarios con sesión y rol permitido pueden registrar y consultar traslados de pacientes o elementos, editar los que están **Pendiente** y asignar personal y vehículos activos.

Los estados son **Pendiente**, **En curso**, **Finalizado** y **Cancelado**. Desde **Pendiente** se puede pasar a **En curso** o **Cancelado**; desde **En curso**, a **Finalizado** o **Cancelado**. Los estados finales no tienen más transiciones. El historial permite filtrar por nombre de paciente y código de vehículo.

### Usuarios, personal y vehículos

Administración puede crear usuarios y cambiar contraseñas, además de registrar y activar o desactivar personal y vehículos.

## Acceso por rol

- `admin`: panel, usuarios, encuestas, indicaciones, personal, vehículos y traslados.
- `documentacion`: indicaciones y traslados; puede crear indicaciones.
- `doctor`: indicaciones y traslados; puede revisar indicaciones.
- `operador` y `funcionario`: traslados.

El acceso a las páginas y operaciones se valida en PHP mediante sesión y rol. Las páginas privadas se sirven por `Presentacion/pagina.php`.

## Organización del proyecto

```text
Proyecto/
├── Negocio/                  Endpoints PHP y configuración
├── Presentacion/             Páginas públicas, JavaScript y CSS
│   ├── pagina.php            Enrutador de páginas privadas
│   └── qrcode.min.js         Generador local de códigos QR
├── Privado/
│   ├── Datos/
│   │   ├── schema.sql        Estructura de la base
│   │   ├── data.sql          Datos ficticios para pruebas
│   │   └── Clases/           Clases Documento y Traslado
│   ├── Documentos/           PDF almacenados fuera del sitio público
│   └── Presentacion/         Páginas privadas en HTML
└── Documentacion/             Manual, modelo de datos, diagramas y pruebas
```

`Negocio/configuracion.php` contiene la ruta de la carpeta privada. `DIRECTORIO_DOCUMENTOS` apunta a su subcarpeta `Documentos/`. En el servidor, la carpeta `Privado/` debe estar fuera del `DocumentRoot`; `Negocio/ver_documento.php` entrega el PDF solo después de comprobar el estado de la indicación y los permisos.

### Licencia del generador QR

`Presentacion/qrcode.min.js` utiliza qrcodejs, de davidshimjs, bajo licencia MIT:

Copyright (c) 2012 davidshimjs

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

## Documentación adicional

- `Documentacion/Manual_de_usuario.md`: uso de la aplicación.
- `Documentacion/Modelo_de_datos.md`: tablas y relaciones.
- `Documentacion/Diagramas_del_sistema.md`: clases PHP y secuencia para registrar un traslado.
- `Documentacion/Casos_de_prueba.md`: comprobaciones manuales.
- `Documentacion/Hoja_testigo.md`: evidencias de la entrega.

Las pruebas con la base deben ejecutarse usando el servidor MariaDB configurado para el proyecto. El archivo `Documentacion/Casos_de_prueba.md` indica cuáles comprobaciones siguen pendientes.
