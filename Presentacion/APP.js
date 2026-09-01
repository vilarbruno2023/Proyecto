document.addEventListener('DOMContentLoaded', function() {
    const error = new URLSearchParams(window.location.search);

    if (error.get('error') === '1') {
        alert("Usuario o contraseña incorrectos.");
    }

    const formLogin = document.querySelector('form');
    if (formLogin) {
        formLogin.addEventListener('submit', function() {
            const inputUsuario = document.querySelector('input[name="usuario"]');
            if (inputUsuario && inputUsuario.value) {
                localStorage.setItem('usuario', inputUsuario.value);
            }
        });
    }
});