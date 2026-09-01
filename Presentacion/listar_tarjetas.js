document.addEventListener('DOMContentLoaded', function() {
    fetch('../Negocio/obtener_documentos_pacientes.php')
        .then(res => res.text())
        .then(html => {
            const mainCentro = document.querySelector('main .centro');
            if(mainCentro) {
                mainCentro.innerHTML = html;
            }
        });
});