document.addEventListener('DOMContentLoaded', function() {
    const btnAside = document.getElementById('btnLogoutAside');

    function cerrarSesion() {
        fetch('../Negocio/logout.php')
            .then(response => response.json())
            .then(data => {
                if(data.logout) {
                    window.location.href = data.redirect;
                }
            });
    }

    if (btnAside) btnAside.addEventListener('click', cerrarSesion);
});