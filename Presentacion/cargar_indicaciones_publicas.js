const tabla = document.getElementById('cuerpoIndicacionesPublicas');
const busqueda = document.getElementById('buscarIndicacion');
const filtroCategoria = document.getElementById('filtroArea');
const mensajeSinResultados = document.getElementById('mensajeSinResultados');

function filtrarIndicaciones() {
    const texto = busqueda.value.trim().toLocaleLowerCase('es');
    let cantidadResultados = 0;

    tabla.querySelectorAll('.fila-indicacion').forEach(fila => {
        const coincideTexto = fila.textContent.toLocaleLowerCase('es').includes(texto);
        const coincideCategoria = !filtroCategoria.value ||
            fila.dataset.categoria === filtroCategoria.value;
        fila.hidden = !coincideTexto || !coincideCategoria;
        if (!fila.hidden) cantidadResultados++;
    });

    mensajeSinResultados.hidden = cantidadResultados > 0;
}

async function cargarIndicaciones() {
    let datos;

    try {
        const respuesta = await fetch('../Negocio/obtener_indicaciones_vigentes.php');
        if (!respuesta.ok) throw new Error('No se pudieron cargar las indicaciones.');
        datos = await respuesta.json();
    } catch (error) {
        console.error('Error al cargar las indicaciones públicas:', error);
        document.getElementById('mensajeCargaIndicaciones').textContent =
            'No fue posible cargar las indicaciones. Intentá nuevamente más tarde.';
        return;
    }

    tabla.textContent = '';
    datos.categorias.forEach(categoria => {
        const opcion = document.createElement('option');
        opcion.value = categoria.nombreCategoria;
        opcion.textContent = categoria.nombreCategoria;
        filtroCategoria.append(opcion);
    });

    datos.indicaciones.forEach(indicacion => {
        const fila = document.getElementById('filaIndicacionPublica')
            .content.firstElementChild.cloneNode(true);
        fila.dataset.categoria = indicacion.nombreCategoria;
        fila.querySelector('[data-titulo]').textContent = indicacion.titulo || '';
        fila.querySelector('[data-categoria]').textContent = indicacion.nombreCategoria || '';
        fila.querySelector('[data-resumen]').textContent = indicacion.resumen || '';
        fila.querySelector('[data-indicacion]').href = 'indicacion.html?id=' + indicacion.idDocumento;
        tabla.append(fila);
    });

    filtrarIndicaciones();
}

busqueda.addEventListener('input', filtrarIndicaciones);
filtroCategoria.addEventListener('change', filtrarIndicaciones);
cargarIndicaciones();
