# Modelo de datos — SIGSM

La definición ejecutable está en `Privado/Datos/schema.sql`. Los datos ficticios están en `Privado/Datos/data.sql`.

## Modelo relacional

| Tabla | Clave primaria | Claves foráneas | Función |
|---|---|---|---|
| `funcionario` | `idUsuario` | — | Cuentas y roles |
| `categoria` | `idCategoria` | — | Categorías de indicaciones |
| `documento` | `idDocumento` | `idUsuario` → `funcionario`; `idCategoria` → `categoria` | Indicación y nombre de archivo |
| `encuesta_respuesta` | `idEncuesta` | — | Encuesta anónima con fecha |
| `personal_traslado` | `idPersonal` | — | Chofer, enfermero y otros recursos humanos |
| `vehiculo` | `idVehiculo` | — | Vehículos disponibles |
| `traslado` | `idTraslado` | `idSolicitante` → `funcionario`; `idChofer` y `idEnfermero` → `personal_traslado`; `idVehiculo` → `vehiculo` | Gestión e historial de traslado |

## Diagrama entidad-relación (texto)

```text
funcionario (1) ───────< (N) documento (N) >────── (1) categoria
     │
     └─────────< (N) traslado (N) >────── (1) vehiculo
                      │
                      ├────> (0..1) personal_traslado  [chofer]
                      └────> (0..1) personal_traslado  [enfermero]

encuesta_respuesta  [anónima, no tiene FK a usuarios]
```

El modelo evita repetir nombres de categorías, choferes y vehículos en cada documento o traslado: se usan identificadores. Los datos de cada persona o vehículo están en su tabla. La encuesta no tiene relación con `funcionario` para conservar el anonimato.

## Estados

- Documento: `Revisar`, `Vigente`, `Rechazada` (almacenados en `documento.estado`).
- Traslado: `Pendiente`, `En curso`, `Finalizado`, `Cancelado` (ENUM en `traslado.estado`).

Los nombres de archivo se almacenan como texto (`documento.archivo`); los PDF se guardan en `Privado/Documentos/`, fuera del directorio público durante la instalación. No se guardan como BLOB.

Este documento describe las tablas, sus claves y las relaciones definidas en el esquema actual.
