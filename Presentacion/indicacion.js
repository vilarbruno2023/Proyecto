async function cargarIndicacion() {
    const parametrosUrl = new URLSearchParams(location.search);
    const idIndicacion = parametrosUrl.get('id');
    const mensaje = document.getElementById('mensajeIndicacion');

    if (!idIndicacion) {
        mensaje.textContent = 'No se indicó qué indicación mostrar.';
        return;
    }

    try {
        const respuesta = await fetch(
            '../Negocio/obtener_indicaciones_vigentes.php?id=' + idIndicacion,
            { cache: 'no-store' }
        );
        const datos = await respuesta.json();

        if (!respuesta.ok || !datos.ok) {
            throw new Error(datos.error || 'No se pudo cargar la indicación.');
        }

        document.getElementById('tituloIndicacion').textContent = datos.indicacion.titulo;
        document.getElementById('categoriaIndicacion').textContent = datos.indicacion.nombreCategoria;
        document.getElementById('resumenIndicacion').textContent =
            datos.indicacion.resumen || 'Sin resumen.';
        document.getElementById('archivoIndicacion').href =
            '../Negocio/ver_documento.php?id=' + datos.indicacion.idDocumento;
        const codigoQr = document.getElementById('codigoQr');
        codigoQr.textContent = '';
        new QRCode(codigoQr, {
            text: window.location.href,
            width: 200,
            height: 200,
            correctLevel: QRCode.CorrectLevel.M
        });
        document.getElementById('detalleIndicacion').hidden = false;
        mensaje.hidden = true;
    } catch (error) {
        mensaje.textContent = error.message;
    }
}

cargarIndicacion();
