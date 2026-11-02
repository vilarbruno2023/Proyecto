document.addEventListener('DOMContentLoaded', function configurarFormularioLogin() {
    const parametrosUrl = new URLSearchParams(window.location.search);
    const huboErrorDeAcceso = parametrosUrl.get('error') === '1';

    if (huboErrorDeAcceso) {
        alert('Usuario o contraseña incorrectos.');
    }
});
