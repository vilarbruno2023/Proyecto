const direccionApi = '../Negocio/api_administracion.php';
const contenido = document.getElementById('contenido') || document.body;
const aviso = document.getElementById('aviso');

function mostrarAviso(mensaje, hayError = false) {
    aviso.textContent = mensaje;
    aviso.className = hayError ? 'aviso aviso-error' : 'aviso aviso-ok';
}

async function llamarApi(accion, datos = null) {
    const opciones = {};
    if (datos) {
        opciones.method = 'POST';
        opciones.body = datos;
    }

    const respuesta = await fetch(direccionApi + '?accion=' + accion, opciones);
    const resultado = await respuesta.json();
    if (!respuesta.ok || !resultado.ok) {
        throw new Error(resultado.error || 'No se pudo completar la operación.');
    }
    return resultado;
}

function nuevoFormularioDatos(formulario) {
    return new FormData(formulario);
}

function filaVacia(plantilla, tabla, texto, cantidadColumnas = 3) {
    if (tabla.children.length !== 0) return;
    const fila = plantilla.content.firstElementChild.cloneNode(true);
    const celda = fila.querySelector('td');
    celda.colSpan = cantidadColumnas;
    celda.textContent = texto;
    tabla.append(fila);
}

async function cargarUsuarios() {
    const datos = await llamarApi('usuarios');
    const tabla = document.getElementById('usuarios');
    tabla.textContent = '';

    datos.usuarios.forEach(usuario => {
        const fila = document.getElementById('filaUsuario').content.firstElementChild.cloneNode(true);
        fila.querySelector('[data-usuario]').textContent = usuario.nombreUsuario;
        fila.querySelector('[data-rol]').textContent = usuario.rol;
        fila.querySelector('form').dataset.id = usuario.idUsuario;
        tabla.append(fila);
    });

    filaVacia(document.getElementById('sinUsuarios'), tabla, 'Todavía no hay usuarios.');
}

async function cargarFlota() {
    const datos = await llamarApi('flota');
    const tablaPersonal = document.getElementById('listaPersonal');
    const tablaVehiculos = document.getElementById('listaVehiculos');
    tablaPersonal.textContent = '';
    tablaVehiculos.textContent = '';

    datos.personal.forEach(persona => {
        const fila = document.getElementById('filaPersonal').content.firstElementChild.cloneNode(true);
        // Convertimos el estado para reconocer tanto 0 como "0".
        const estaActivo = Number(persona.activo) === 1;
        fila.querySelector('[data-nombre]').textContent = persona.nombre;
        fila.querySelector('[data-documento]').textContent = persona.documento || '';
        fila.querySelector('[data-funcion]').textContent = persona.funcion;
        fila.querySelector('[data-estado]').textContent = estaActivo ? 'Activo' : 'Inactivo';

        const boton = fila.querySelector('button');
        boton.dataset.id = persona.idPersonal;
        boton.dataset.activo = estaActivo ? '0' : '1';
        boton.textContent = estaActivo ? 'Desactivar' : 'Activar';
        tablaPersonal.append(fila);
    });

    datos.vehiculos.forEach(vehiculo => {
        const fila = document.getElementById('filaVehiculo').content.firstElementChild.cloneNode(true);
        // Convertimos el estado para reconocer tanto 0 como "0".
        const estaActivo = Number(vehiculo.activo) === 1;
        fila.querySelector('[data-codigo]').textContent = vehiculo.codigo;
        fila.querySelector('[data-tipo]').textContent = vehiculo.tipo;
        fila.querySelector('[data-descripcion]').textContent = vehiculo.descripcion || '';
        fila.querySelector('[data-capacidad]').textContent = vehiculo.capacidad;
        fila.querySelector('[data-estado]').textContent = estaActivo ? 'Activo' : 'Inactivo';

        const boton = fila.querySelector('button');
        boton.dataset.id = vehiculo.idVehiculo;
        boton.dataset.activo = estaActivo ? '0' : '1';
        boton.textContent = estaActivo ? 'Desactivar' : 'Activar';
        tablaVehiculos.append(fila);
    });
}

function agregarCategorias(selector, categorias, categoriaSeleccionada = '') {
    selector.textContent = '';
    categorias.forEach(categoria => {
        const opcion = document.createElement('option');
        opcion.value = categoria.idCategoria;
        opcion.textContent = categoria.nombreCategoria;
        opcion.selected = Number(categoria.idCategoria) === Number(categoriaSeleccionada);
        selector.append(opcion);
    });
}

function filtrarIndicaciones() {
    const tabla = document.getElementById('listaIndicaciones');
    const textoBuscado = document.getElementById('filtroTextoIndicaciones').value.toLowerCase();
    const categoriaSeleccionada = document.getElementById('filtroCategoriaIndicaciones').value;
    const estadoSeleccionado = document.getElementById('filtroEstadoIndicaciones').value;
    const filas = tabla.querySelectorAll('[data-indicacion]');
    let cantidadVisible = 0;

    // Los tres filtros se aplican juntos sobre las indicaciones cargadas.
    filas.forEach(fila => {
        const coincideTexto = fila.dataset.texto.includes(textoBuscado);
        const coincideCategoria = !categoriaSeleccionada ||
            fila.dataset.categoria === categoriaSeleccionada;
        const coincideEstado = !estadoSeleccionado || fila.dataset.estado === estadoSeleccionado;
        fila.hidden = !coincideTexto || !coincideCategoria || !coincideEstado;

        if (!fila.hidden) cantidadVisible++;
    });

    const filaMensaje = tabla.querySelector('[data-mensaje-indicaciones]');
    if (!filaMensaje) return;
    filaMensaje.hidden = cantidadVisible > 0;
    filaMensaje.querySelector('td').textContent = filas.length === 0
        ? 'Todavía no hay indicaciones.'
        : 'No se encontraron indicaciones con esos filtros.';
}

async function cargarIndicaciones() {
    const datos = await llamarApi('indicaciones');
    document.getElementById('seccionNuevaIndicacion').hidden = !datos.puedeCrear;
    document.getElementById('seccionCategorias').hidden = !datos.puedeCategorias;
    document.getElementById('encabezadoRevision').hidden = !datos.puedeAprobar;

    const filtroCategoria = document.getElementById('filtroCategoriaIndicaciones');
    filtroCategoria.textContent = '';
    const opcionTodasCategorias = document.createElement('option');
    opcionTodasCategorias.value = '';
    opcionTodasCategorias.textContent = 'Todas las categorías';
    filtroCategoria.append(opcionTodasCategorias);
    datos.categorias.forEach(categoria => {
        const opcion = document.createElement('option');
        opcion.value = categoria.idCategoria;
        opcion.textContent = categoria.nombreCategoria;
        filtroCategoria.append(opcion);
    });

    if (datos.puedeCrear) {
        agregarCategorias(
            document.querySelector('#nuevaIndicacion [name="idCategoria"]'),
            datos.categorias
        );
    }

    const mostrarAcciones = datos.puedeEliminar ||
        datos.indicaciones.some(indicacion => indicacion.puedeEditar);
    document.getElementById('encabezadoAcciones').hidden = !mostrarAcciones;

    const tabla = document.getElementById('listaIndicaciones');
    tabla.textContent = '';

    datos.indicaciones.forEach(indicacion => {
        const fila = document.getElementById('filaIndicacion').content.firstElementChild.cloneNode(true);
        fila.dataset.indicacion = 'true';
        fila.dataset.texto = [
            indicacion.titulo,
            indicacion.nombreCategoria,
            indicacion.estado,
            indicacion.resumen || ''
        ].join(' ').toLowerCase();
        fila.dataset.categoria = indicacion.idCategoria;
        fila.dataset.estado = indicacion.estado;
        fila.querySelector('[data-titulo]').textContent = indicacion.titulo;
        fila.querySelector('[data-categoria]').textContent = indicacion.nombreCategoria;
        fila.querySelector('[data-estado]').textContent = indicacion.estado;
        fila.querySelector('[data-resumen]').textContent = indicacion.resumen || '';

        const enlace = fila.querySelector('[data-pdf]');
        enlace.href = '../Negocio/ver_documento.php?id=' + indicacion.idDocumento;

        const revision = fila.querySelector('[data-revision]');
        revision.hidden = !datos.puedeAprobar;
        revision.querySelector('form').dataset.id = indicacion.idDocumento;
        revision.querySelector('select').value = indicacion.estado;

        const acciones = fila.querySelector('[data-acciones]');
        acciones.hidden = !mostrarAcciones;
        acciones.querySelector('[data-editar]').hidden = !indicacion.puedeEditar;
        acciones.querySelector('.eliminarIndicacion').hidden = !datos.puedeEliminar;

        const formularioEdicion = acciones.querySelector('.editarIndicacion');
        formularioEdicion.dataset.id = indicacion.idDocumento;
        formularioEdicion.elements.titulo.value = indicacion.titulo;
        formularioEdicion.elements.resumen.value = indicacion.resumen || '';
        agregarCategorias(
            formularioEdicion.elements.idCategoria,
            datos.categorias,
            indicacion.idCategoria
        );
        acciones.querySelector('.eliminarIndicacion').dataset.id = indicacion.idDocumento;
        tabla.append(fila);
    });

    const filaMensaje = document.getElementById('sinIndicaciones').content.firstElementChild.cloneNode(true);
    filaMensaje.dataset.mensajeIndicaciones = 'true';
    filaMensaje.querySelector('td').colSpan = 5 +
        Number(datos.puedeAprobar) + Number(mostrarAcciones);
    tabla.append(filaMensaje);
    filtrarIndicaciones();
}

async function enviarFormulario(formulario, accion) {
    await llamarApi(accion, nuevoFormularioDatos(formulario));
    formulario.reset();
    mostrarAviso('Cambios guardados.');

    if (accion === 'crear_usuario') await cargarUsuarios();
    if (accion === 'crear_personal' || accion === 'crear_vehiculo') await cargarFlota();
    if (accion === 'crear_categoria' || accion === 'editar_indicacion' ||
        accion === 'aprobar_indicacion' || accion === 'eliminar_indicacion') {
        await cargarIndicaciones();
    }
}

contenido.addEventListener('submit', async evento => {
    evento.preventDefault();
    const formulario = evento.target;

    try {
        if (formulario.id === 'nuevaIndicacion') {
            const respuesta = await fetch('../Negocio/guardar_documento.php', {
                method: 'POST',
                body: nuevoFormularioDatos(formulario)
            });
            const resultado = await respuesta.json();
            if (!respuesta.ok || !resultado.ok) throw new Error(resultado.error || 'No se pudo guardar la indicación.');
            formulario.reset();
            mostrarAviso('Indicación enviada a revisión.');
            await cargarIndicaciones();
        } else if (formulario.id === 'crearUsuario') {
            await enviarFormulario(formulario, 'crear_usuario');
        } else if (formulario.classList.contains('cambiarClave')) {
            const datos = nuevoFormularioDatos(formulario);
            datos.append('idUsuario', formulario.dataset.id);
            await llamarApi('cambiar_clave', datos);
            formulario.reset();
            mostrarAviso('Contraseña actualizada.');
        } else if (formulario.id === 'personal') {
            await enviarFormulario(formulario, 'crear_personal');
        } else if (formulario.id === 'vehiculo') {
            await enviarFormulario(formulario, 'crear_vehiculo');
        } else if (formulario.id === 'categoria') {
            await enviarFormulario(formulario, 'crear_categoria');
        } else if (formulario.classList.contains('revisarIndicacion')) {
            const datos = nuevoFormularioDatos(formulario);
            datos.append('idDocumento', formulario.dataset.id);
            await llamarApi('aprobar_indicacion', datos);
            mostrarAviso('Estado actualizado.');
            await cargarIndicaciones();
        } else if (formulario.classList.contains('editarIndicacion')) {
            const datos = nuevoFormularioDatos(formulario);
            datos.append('idDocumento', formulario.dataset.id);
            await llamarApi('editar_indicacion', datos);
            mostrarAviso('Indicación actualizada.');
            await cargarIndicaciones();
        } else if (formulario.classList.contains('eliminarIndicacion')) {
            if (!confirm('¿Seguro que querés eliminar esta indicación y su PDF?')) return;
            const datos = new FormData();
            datos.append('idDocumento', formulario.dataset.id);
            await llamarApi('eliminar_indicacion', datos);
            mostrarAviso('Indicación eliminada.');
            await cargarIndicaciones();
        }
    } catch (error) {
        mostrarAviso(error.message, true);
    }
});

contenido.addEventListener('click', async evento => {
    const boton = evento.target.closest('.cambiarEstadoPersonal, .cambiarEstadoVehiculo');
    if (!boton) return;

    const accion = boton.classList.contains('cambiarEstadoPersonal')
        ? 'estado_personal'
        : 'estado_vehiculo';
    const datos = new FormData();
    datos.append('id', boton.dataset.id);
    datos.append('activo', boton.dataset.activo);

    try {
        await llamarApi(accion, datos);
        mostrarAviso('Estado actualizado.');
        await cargarFlota();
    } catch (error) {
        mostrarAviso(error.message, true);
    }
});

if (document.getElementById('usuarios')) {
    cargarUsuarios().catch(error => mostrarAviso(error.message, true));
} else if (document.getElementById('listaPersonal')) {
    cargarFlota().catch(error => mostrarAviso(error.message, true));
} else if (document.getElementById('listaIndicaciones')) {
    document.getElementById('filtroTextoIndicaciones')
        .addEventListener('input', filtrarIndicaciones);
    document.getElementById('filtroCategoriaIndicaciones')
        .addEventListener('change', filtrarIndicaciones);
    document.getElementById('filtroEstadoIndicaciones')
        .addEventListener('change', filtrarIndicaciones);
    cargarIndicaciones().catch(error => mostrarAviso(error.message, true));
}
