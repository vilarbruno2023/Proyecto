document.addEventListener('DOMContentLoaded', async function verificarAccesoAdministrativo() {
    try {
        const respuestaSesion = await fetch('../Negocio/verificar_sesion.php', {
            credentials: 'same-origin',
            cache: 'no-store'
        });
        const datosSesion = await respuestaSesion.json();

        if (!datosSesion.autenticado) {
            window.location.replace('inicio.html');
            return;
        }

        const rolUsuario = String(datosSesion.rol || '').toLowerCase();
        if (rolUsuario !== 'admin') {
            window.location.replace('index.html');
        }
    } catch (errorSesion) {
        console.error('No se pudo verificar la sesión administrativa:', errorSesion);
        window.location.replace('inicio.html');
    }
});
