document.addEventListener('DOMContentLoaded', function configurarCierreSesion() {
    const botonCerrarSesion = document.getElementById('btnLogoutAside');
    if (!botonCerrarSesion) return;

    botonCerrarSesion.addEventListener('click', async function cerrarSesion() {
        try {
            const respuestaCierre = await fetch('../Negocio/logout.php', {
                credentials: 'same-origin',
                cache: 'no-store'
            });
            const resultadoCierre = await respuestaCierre.json();
            if (resultadoCierre.logout) {
                window.location.replace('inicio.html');
            }
        } catch (errorCierre) {
            console.error('No se pudo cerrar la sesión:', errorCierre);
        }
    });
});
