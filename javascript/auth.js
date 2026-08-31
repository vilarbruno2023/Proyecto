document.addEventListener('DOMContentLoaded', function() {
    fetch('../api/verificar_sesion.php')
        .then(response => response.json())
        .then(data => {
            if(!data.autenticado) {
                window.location.href = '../htmls/inicio.html';
                return
            }

            const rol = data.rol.toLowerCase();
            if(window.location.pathname.endsWith('SIGSM.html') && rol !== 'admin') {
                window.location.href = '../htmls/index.html';
                return
            }
        }).catch(error => {
            console.error('Error al verificar la sesión:', error);
            window.location.href = '../htmls/inicio.html';
        });
});
