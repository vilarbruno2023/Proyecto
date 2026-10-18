document.addEventListener('DOMContentLoaded', async function cargarIndicacionesPublicas() {
    const cuerpoTablaIndicaciones = document.getElementById('cuerpoIndicacionesPublicas');
    const campoBuscarIndicacion = document.getElementById('buscarIndicacion');
    const selectorArea = document.getElementById('filtroArea');
    const mensajeSinResultados = document.getElementById('mensajeSinResultados');

    try {
        const respuestaIndicaciones = await fetch('../Negocio/obtener_indicaciones_vigentes.php');
        if (!respuestaIndicaciones.ok) {
            throw new Error('No se pudieron cargar las indicaciones.');
        }

        cuerpoTablaIndicaciones.innerHTML = await respuestaIndicaciones.text();
        filtrarIndicacionesPublicas();
    } catch (errorCarga) {
        console.error('Error al cargar las indicaciones públicas:', errorCarga);
        cuerpoTablaIndicaciones.innerHTML = '<tr><td colspan="4">No fue posible cargar las indicaciones. Intentá nuevamente más tarde.</td></tr>';
    }

    function filtrarIndicacionesPublicas() {
        const textoBusqueda = campoBuscarIndicacion.value.trim().toLocaleLowerCase('es');
        const areaSeleccionada = selectorArea.value;
        const filasIndicaciones = cuerpoTablaIndicaciones.querySelectorAll('tr.fila-indicacion');
        let cantidadResultados = 0;

        filasIndicaciones.forEach(function aplicarFiltro(filaIndicacion) {
            const textoFila = filaIndicacion.textContent.toLocaleLowerCase('es');
            const areaIndicacion = filaIndicacion.dataset.area;
            const coincideTexto = textoFila.includes(textoBusqueda);
            const coincideArea = areaSeleccionada === '' || areaIndicacion === areaSeleccionada;
            const mostrarIndicacion = coincideTexto && coincideArea;

            filaIndicacion.hidden = !mostrarIndicacion;
            if (mostrarIndicacion) cantidadResultados++;
        });

        mensajeSinResultados.hidden = cantidadResultados > 0 || filasIndicaciones.length === 0;
    }

    campoBuscarIndicacion.addEventListener('input', filtrarIndicacionesPublicas);
    selectorArea.addEventListener('change', filtrarIndicacionesPublicas);
});
