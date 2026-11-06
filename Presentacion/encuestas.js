const direccionApiEncuestas = '../Negocio/api_administracion.php?accion=encuestas';

function mostrarResultadosEncuesta(resultados, tipo, idResumen, idPuntajes) {
    const encuesta = resultados[tipo];
    const puntajes = encuesta.puntajes;
    const resumen = document.getElementById(idResumen);
    const textoPuntajes = document.getElementById(idPuntajes);

    if (encuesta.cantidad === 0) {
        resumen.textContent = 'Respuestas: 0 | Promedio: sin datos';
    } else {
        resumen.textContent = 'Respuestas: ' + encuesta.cantidad + ' | Promedio: ' +
            encuesta.promedio + '/5';
    }

    textoPuntajes.textContent = 'Puntajes: 1=' + puntajes[0] + ', 2=' + puntajes[1] +
        ', 3=' + puntajes[2] + ', 4=' + puntajes[3] + ', 5=' + puntajes[4];
}

async function cargarEncuestas() {
    const respuesta = await fetch(direccionApiEncuestas, { cache: 'no-store' });
    const datos = await respuesta.json();

    if (!respuesta.ok || !datos.ok) {
        throw new Error(datos.error || 'No se pudieron cargar las encuestas.');
    }

    mostrarResultadosEncuesta(
        datos.resultados, 'trasplantado', 'resumenTrasplantado', 'puntajesTrasplantado'
    );
    mostrarResultadosEncuesta(
        datos.resultados, 'estandar', 'resumenEstandar', 'puntajesEstandar'
    );
    mostrarResultadosEncuesta(
        datos.resultados, 'fnr', 'resumenFNR', 'puntajesFNR'
    );
}

cargarEncuestas().catch(error => {
    document.getElementById('aviso').textContent = error.message;
    document.getElementById('aviso').className = 'aviso aviso-error';
});
