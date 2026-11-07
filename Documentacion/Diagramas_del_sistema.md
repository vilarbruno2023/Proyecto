# Diagramas del sistema

Estos diagramas usan cajas y flechas para representar las clases PHP y el recorrido de una operación implementada.

## Diagrama de clases

```text
+----------------------------+       usa       +-------------------------+
| Negocio/api_traslados.php  |---------------->| Traslado                |
|----------------------------|                 |-------------------------|
| Recibe acciones HTTP       |                 | listarTraslados()       |
| Comprueba sesión y rol     |                 | listarRecursosTraslado()|
| Devuelve respuestas JSON   |                 | crearTraslado()         |
+----------------------------+                 | editarTraslado()        |
                                               | actualizarEstadoTraslado()|
                                               | valida datos y recursos |
                                               +------------+------------+
                                                            |
                                                            | consulta y modifica
                                                            v
                                               +-------------------------+
                                               | Base de datos (PDO)     |
                                               | tablas de traslados,    |
                                               | personal y vehículos    |
                                               +-------------------------+

+------------------------------+     usa      +-------------------------+
| Negocio/api_administracion.php|------------>| Documento               |
|------------------------------|              |-------------------------|
| Recibe acciones HTTP         |              | eliminar()              |
| Comprueba sesión y permisos  |              | elimina registro y PDF  |
| Devuelve respuestas JSON     |              +------------+------------+
+------------------------------+                           |
                                                           | usa
                                                           v
                                               +-------------------------+
                                               | Base de datos (PDO) y   |
                                               | Privado/Documentos/    |
                                               +-------------------------+
```

`Traslado` y `Documento` son las clases PHP del proyecto ubicadas en `Privado/Datos/Clases/`. Las páginas y los archivos JavaScript envían solicitudes a los controladores de `Negocio/`; estos usan las clases para realizar operaciones.

## Diagrama de secuencia: registrar un traslado

```text
Funcionario       Página y JavaScript      api_traslados.php       Traslado       Base de datos
     |                     |                      |                    |                |
     | completa formulario |                      |                    |                |
     |-------------------->|                      |                    |                |
     |                     | valida datos         |                    |                |
     |                     | envía POST "crear"   |                    |                |
     |                     |--------------------->|                    |                |
     |                     |                      | comprueba sesión,  |                |
     |                     |                      | rol y acción       |                |
     |                     |                      |------------------->|                |
     |                     |                      |                    | valida datos   |
     |                     |                      |                    | e inserta      |
     |                     |                      |                    |--------------->|
     |                     |                      |                    |<---------------|
     |                     |                      | devuelve resultado |                |
     |                     |                      |<-------------------|                |
     |                     | recibe respuesta JSON|                    |                |
     |                     |<---------------------|                    |                |
     | ve confirmación y   |                      |                    |                |
     | la lista actualizada|                      |                    |                |
     |<--------------------|                      |                    |                |
```

Si la validación falla, `Traslado` devuelve un error y la API responde con un mensaje de error; la página lo muestra en lugar de confirmar el registro.
