'use strict';

const direccionApiTraslados = '../Negocio/api_traslados.php';
const formularioCrear = document.getElementById('formCrear');
const formularioFiltros = document.getElementById('formFiltrosTraslados');
const listaTraslados = document.getElementById('listaTraslados');
let recursosTraslado = { personal: [], vehiculos: [] };

function mostrarMensaje(texto, esError = false) {
    const mensaje = document.getElementById('mensaje');
    mensaje.className = esError ? 'mensaje error' : 'mensaje ok';
    mensaje.textContent = texto;
}

async function llamarApi(datosFormulario) {
    let direccion = direccionApiTraslados + '?accion=listar';
    const opciones = {};

    if (datosFormulario) {
        direccion = direccionApiTraslados;
        opciones.method = 'POST';
        opciones.body = datosFormulario;
    }

    const respuesta = await fetch(direccion, opciones);
    const resultado = await respuesta.json();
    if (!respuesta.ok || !resultado.ok) {
        throw new Error(resultado.error || 'No se pudo completar la operación.');
    }
    return resultado;
}

function agregarOpcion(selector, valor, texto) {
    const opcion = document.createElement('option');
    opcion.value = valor;
    opcion.textContent = texto;
    selector.append(opcion);
}

function mostrarRecursos(formulario) {
    const esPaciente = formulario.elements.tipoTraslado.value === 'paciente';
    const selectorChofer = formulario.elements.idChofer;
    const selectorEnfermero = formulario.elements.idEnfermero;
    const selectorVehiculo = formulario.elements.idVehiculo;
    const choferSeleccionado = selectorChofer.value;
    const enfermeroSeleccionado = selectorEnfermero.value;
    const vehiculoSeleccionado = selectorVehiculo.value;

    selectorChofer.textContent = '';
    selectorEnfermero.textContent = '';
    selectorVehiculo.textContent = '';
    agregarOpcion(selectorChofer, '', 'Sin asignar');
    agregarOpcion(selectorEnfermero, '', 'Sin asignar');
    agregarOpcion(selectorVehiculo, '', 'Sin asignar');

    recursosTraslado.personal.forEach(persona => {
        if (persona.funcion === 'chofer') {
            agregarOpcion(selectorChofer, persona.idPersonal, persona.nombre);
        }
        if (persona.funcion === 'enfermero') {
            agregarOpcion(selectorEnfermero, persona.idPersonal, persona.nombre);
        }
    });

    recursosTraslado.vehiculos.forEach(vehiculo => {
        if (!esPaciente || vehiculo.tipo !== 'utilitario') {
            agregarOpcion(
                selectorVehiculo,
                vehiculo.idVehiculo,
                vehiculo.codigo + ' (' + vehiculo.tipo + ')'
            );
        }
    });

    selectorChofer.value = choferSeleccionado;
    selectorEnfermero.value = enfermeroSeleccionado;
    selectorVehiculo.value = vehiculoSeleccionado;
}

function mostrarCamposCrear() {
    const esPaciente = formularioCrear.elements.tipoTraslado.value === 'paciente';
    ['campoNombre', 'campoCedula', 'campoReferencia', 'campoZona'].forEach(identificador => {
        document.getElementById(identificador).hidden = !esPaciente;
    });
    document.getElementById('campoElemento').hidden = esPaciente;
    document.getElementById('campoEnfermero').hidden = !esPaciente;
    formularioCrear.elements.nombrePaciente.required = esPaciente;
    formularioCrear.elements.descripcionElemento.required = !esPaciente;

    if (!esPaciente) {
        formularioCrear.elements.idEnfermero.value = '';
    }
    mostrarRecursos(formularioCrear);
}

function mostrarCamposEdicion(formulario) {
    const esPaciente = formulario.elements.tipoTraslado.value === 'paciente';
    formulario.querySelectorAll('[data-campo-paciente]').forEach(campo => {
        campo.hidden = !esPaciente;
    });
    formulario.querySelector('[data-campo-elemento]').hidden = esPaciente;
    formulario.elements.nombrePaciente.required = esPaciente;
    formulario.elements.descripcionElemento.required = !esPaciente;

    if (!esPaciente) {
        formulario.elements.idEnfermero.value = '';
    }
    mostrarRecursos(formulario);
}

function agregarDatosTraslado(tarjeta, traslado) {
    tarjeta.querySelector('[data-numero]').textContent = 'Traslado #' + traslado.idTraslado;
    tarjeta.querySelector('[data-titulo]').textContent = traslado.tipoTraslado === 'elemento'
        ? traslado.descripcionElemento || 'Elemento'
        : traslado.nombrePaciente || 'Paciente';
    tarjeta.querySelector('[data-estado]').textContent = traslado.estado;
    tarjeta.querySelector('[data-zona-roja]').hidden = Number(traslado.zonaRoja) !== 1;
    tarjeta.querySelector('[data-prioridad]').textContent = 'Prioridad: ' + traslado.prioridad;
    tarjeta.querySelector('[data-origen]').textContent = traslado.origen;
    tarjeta.querySelector('[data-destino]').textContent = traslado.destino;
    tarjeta.querySelector('[data-cedula]').textContent = traslado.cedulaPaciente || 'No indicada';
    tarjeta.querySelector('[data-ruta]').textContent = traslado.ruta;
    tarjeta.querySelector('[data-vehiculo]').textContent = traslado.codigoVehiculo || 'Sin asignar';
    tarjeta.querySelector('[data-chofer]').textContent = traslado.chofer || 'Sin asignar';
    tarjeta.querySelector('[data-enfermero]').textContent = traslado.enfermero || 'Sin asignar';
    tarjeta.querySelector('[data-solicitud]').textContent = traslado.horaSolicitud;
    tarjeta.querySelector('[data-salida]').textContent = traslado.horaSalida || '—';
    tarjeta.querySelector('[data-llegada-estimada]').textContent = traslado.horaEstimadaLlegada || '—';
    tarjeta.querySelector('[data-llegada]').textContent = traslado.horaLlegada || '—';
    tarjeta.querySelector('[data-observaciones]').textContent = traslado.observaciones || '—';
}

function completarFormularioEdicion(tarjeta, traslado) {
    const detallesEdicion = tarjeta.querySelector('[data-editar]');
    detallesEdicion.hidden = !traslado.estado === 'Pendiente';
    const formulario = detallesEdicion.querySelector('form');

    formulario.elements.tipoTraslado.value = traslado.tipoTraslado;
    ['nombrePaciente', 'cedulaPaciente', 'pacienteReferencia', 'descripcionElemento',
        'origen', 'destino', 'ruta', 'observaciones'].forEach(nombreCampo => {
        formulario.elements[nombreCampo].value = traslado[nombreCampo] || '';
    });
    formulario.elements.zonaRoja.checked = Number(traslado.zonaRoja) === 1;
    formulario.elements.prioridad.value = traslado.prioridad;
    formulario.elements.horaEstimadaLlegada.value = traslado.horaEstimadaLlegada
        ? traslado.horaEstimadaLlegada.replace(' ', 'T').slice(0, 16)
        : '';

    mostrarRecursos(formulario);
    formulario.elements.idChofer.value = traslado.idChofer || '';
    formulario.elements.idEnfermero.value = traslado.idEnfermero || '';
    formulario.elements.idVehiculo.value = traslado.idVehiculo || '';
    mostrarCamposEdicion(formulario);

    formulario.elements.tipoTraslado.addEventListener('change', function () {
        mostrarCamposEdicion(formulario);
    });
}

function mostrarEstadosPermitidos(tarjeta, estadoActual) {
    const formularioEstado = tarjeta.querySelector('.estado-form');
    const selectorEstado = formularioEstado.elements.estado;
    // El servidor también valida estas transiciones al recibir el formulario.
    const estadosPermitidos = {
        'Pendiente': ['En curso', 'Cancelado'],
        'En curso': ['Finalizado', 'Cancelado']
    };
    const opciones = estadosPermitidos[estadoActual] || [];

    formularioEstado.hidden = opciones.length === 0;
    for (const opcion of selectorEstado.options) {
        opcion.hidden = !opciones.includes(opcion.value);
    }
    if (opciones.length > 0) {
        selectorEstado.value = opciones[0];
    }
}

function crearTarjetaTraslado(traslado) {
    const tarjeta = document.getElementById('plantillaTraslado')
        .content.firstElementChild.cloneNode(true);
    tarjeta.classList.toggle('zona-roja', Number(traslado.zonaRoja) === 1);
    tarjeta.querySelector('form.editar-traslado-form').dataset.id = traslado.idTraslado;
    tarjeta.querySelector('.estado-form').dataset.id = traslado.idTraslado;

    agregarDatosTraslado(tarjeta, traslado);
    completarFormularioEdicion(tarjeta, traslado);
    mostrarEstadosPermitidos(tarjeta, traslado.estado);
    return tarjeta;
}

async function cargarTraslados() {
    const datos = await llamarApi(new FormData(formularioFiltros));
    recursosTraslado = datos.recursos;
    mostrarRecursos(formularioCrear);
    listaTraslados.textContent = '';

    if (datos.traslados.length === 0) {
        listaTraslados.textContent = 'Todavía no hay traslados registrados.';
        return;
    }

    datos.traslados.forEach(traslado => {
        listaTraslados.append(crearTarjetaTraslado(traslado));
    });
}

async function registrarTraslado(evento) {
    evento.preventDefault();
    const datosFormulario = new FormData(formularioCrear);
    datosFormulario.append('accion', 'crear');

    try {
        const resultado = await llamarApi(datosFormulario);
        mostrarMensaje('Traslado #' + resultado.id + ' registrado correctamente.');
        formularioCrear.reset();
        mostrarCamposCrear();
        await cargarTraslados();
    } catch (error) {
        mostrarMensaje(error.message, true);
    }
}

async function guardarCambiosTraslado(evento) {
    const formulario = evento.target.closest('form.editar-traslado-form, form.estado-form');
    if (!formulario) return;

    evento.preventDefault();
    const esEdicion = formulario.classList.contains('editar-traslado-form');
    const datosFormulario = new FormData(formulario);
    datosFormulario.append('accion', esEdicion ? 'editar' : 'estado');
    datosFormulario.append('idTraslado', formulario.dataset.id);

    try {
        const resultado = await llamarApi(datosFormulario);
        mostrarMensaje(resultado.mensaje);
        await cargarTraslados();
    } catch (error) {
        mostrarMensaje(error.message, true);
    }
}

formularioCrear.elements.tipoTraslado.addEventListener('change', mostrarCamposCrear);
formularioCrear.addEventListener('submit', registrarTraslado);
listaTraslados.addEventListener('submit', guardarCambiosTraslado);
formularioFiltros.addEventListener('submit', function (evento) {
    evento.preventDefault();
    cargarTraslados().catch(error => mostrarMensaje(error.message, true));
});
document.getElementById('limpiarFiltrosTraslados').addEventListener('click', function () {
    formularioFiltros.reset();
    cargarTraslados().catch(error => mostrarMensaje(error.message, true));
});
mostrarCamposCrear();
cargarTraslados().catch(error => mostrarMensaje(error.message, true));
