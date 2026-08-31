document.addEventListener('DOMContentLoaded', function() {
    const btnHeader = document.getElementById('btnLogout');
    const btnAside = document.getElementById('btnLogoutAside');

    function cerrarSesion() {
        fetch('../api/logout.php')
            .then(response => response.json())
            .then(data => {
                if(data.logout) {
                    window.location.href = data.redirect;
                }
            });
    }

    if (btnHeader) btnHeader.addEventListener('click', cerrarSesion);
    if (btnAside) btnAside.addEventListener('click', cerrarSesion);
});