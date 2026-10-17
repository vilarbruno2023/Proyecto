document.addEventListener('DOMContentLoaded', async () => {
    const listaIndicaciones = document.getElementById('listaIndicaciones');
    const campoBusqueda = document.getElementById('buscarIndicacion');
    const selectorArea = document.getElementById('filtroArea');
    const mensajeListado = document.getElementById('mensajeIndicaciones');

    try {
        const respuesta = await fetch('../Negocio/obtener_documentos_pacientes.php');
        if (!respuesta.ok) {
            throw new Error('No se pudieron cargar las indicaciones.');
        }

        listaIndicaciones.innerHTML = await respuesta.text();
        const tarjetasIndicacion = [...listaIndicaciones.querySelectorAll('.tarjeta-indicacion')];

        function filtrarIndicaciones() {
            const textoBusqueda = campoBusqueda.value.trim().toLocaleLowerCase('es');
            const areaSeleccionada = selectorArea.value;
            let cantidadVisible = 0;

            tarjetasIndicacion.forEach((tarjeta) => {
                const textoTarjeta = tarjeta.textContent.toLocaleLowerCase('es');
                const areaTarjeta = tarjeta.dataset.area;
                const coincideTexto = textoTarjeta.includes(textoBusqueda);
                const coincideArea = areaSeleccionada === '' || areaTarjeta === areaSeleccionada;
                const mostrarTarjeta = coincideTexto && coincideArea;

                tarjeta.hidden = !mostrarTarjeta;
                if (mostrarTarjeta) cantidadVisible++;
            });

            mensajeListado.textContent = cantidadVisible === 0
                ? 'No se encontraron indicaciones con esos filtros.'
                : `${cantidadVisible} ${cantidadVisible === 1 ? 'indicación disponible' : 'indicaciones disponibles'}.`;
        }

        campoBusqueda.addEventListener('input', filtrarIndicaciones);
        selectorArea.addEventListener('change', filtrarIndicaciones);
        filtrarIndicaciones();
    } catch (error) {
        mensajeListado.textContent = 'No fue posible cargar las indicaciones. Intentá nuevamente más tarde.';
        console.error(error);
    }
});
