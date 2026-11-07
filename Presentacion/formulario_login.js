document.addEventListener('DOMContentLoaded', function configurarFormularioLogin() {
    const formularioLogin = document.querySelector('.login-form');
    formularioLogin.addEventListener('submit', function (evento) {
        const usuario = formularioLogin.elements.usuario.value.trim();
        const clave = formularioLogin.elements.clave.value;

        if (usuario === '' || clave.trim() === '') {
            evento.preventDefault();
            alert('Ingresá tu usuario y contraseña.');
        }
    });

    const parametrosUrl = new URLSearchParams(window.location.search);
    const huboErrorDeAcceso = parametrosUrl.get('error') === '1';

    if (huboErrorDeAcceso) {
        alert('Usuario o contraseña incorrectos.');
    }
});
