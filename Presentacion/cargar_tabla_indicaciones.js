document.addEventListener('DOMContentLoaded', async function cargarTablaIndicaciones() {
    const cuerpoTablaIndicaciones = document.querySelector('#tablaDocumentos tbody');
    if (!cuerpoTablaIndicaciones) return;

    try {
        const respuestaIndicaciones = await fetch('../Negocio/obtener_tabla_indicaciones.php');
        if (!respuestaIndicaciones.ok) {
            throw new Error('No se pudo cargar la tabla de administración.');
        }
        cuerpoTablaIndicaciones.innerHTML = await respuestaIndicaciones.text();
    } catch (errorCarga) {
        console.error('Error al cargar la tabla administrativa:', errorCarga);
        cuerpoTablaIndicaciones.innerHTML = '<tr><td colspan="5">No fue posible cargar las indicaciones.</td></tr>';
    }
});
