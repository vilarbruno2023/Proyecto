document.addEventListener('DOMContentLoaded', function() {
    fetch('../Negocio/verificar_sesion.php')
        .then(response => response.json())
        .then(data => {
            if (!data.autenticado) {
                window.location.href = 'inicio.html';
                return;
            }

            const rol = String(data.rol || '').toLowerCase();

            if (window.location.pathname.endsWith('SIGSM.html') && rol !== 'admin') {
                window.location.href = 'index.html';
                return;
            }

            if (window.location.pathname.endsWith('index.html') && rol === 'admin') {
                window.location.href = 'SIGSM.html';
                return;
            }

            if (window.location.pathname.endsWith('inicio.html') && rol === 'admin') {
                window.location.href = 'SIGSM.html';
                return;
            }

            if (window.location.pathname.endsWith('inicio.html') && rol === 'usuario') {
                window.location.href = 'index.html';
                return;
            }
        }).catch(error => {
            console.error('Error al verificar la sesión:', error);
            window.location.href = 'inicio.html';
        });
});
