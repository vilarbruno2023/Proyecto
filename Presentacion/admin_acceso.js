fetch('../Negocio/verificar_sesion.php', { cache: 'no-store' })
    .then(respuesta => respuesta.json())
    .then(datosSesion => {
        if (!datosSesion.autenticado) {
            location.href = 'inicio.html';
            return;
        }
        // pagina.php vuelve a validar el rol antes de entregar cada vista.
        const paginaActual = new URLSearchParams(location.search).get('pagina');
        const rolUsuario = (datosSesion.rol || '').toLowerCase();
        if (['usuarios', 'encuestas', 'flota', 'panel'].includes(paginaActual)) {
            if (rolUsuario !== 'admin')
                location.href = 'inicio.html';
        } else if (paginaActual === 'indicaciones' && !['admin', 'doctor', 'documentacion'].includes(rolUsuario)) {
            location.href = 'inicio.html';
        }
    })
    .catch(() => {
        location.href = 'inicio.html';
    });