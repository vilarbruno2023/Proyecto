document.addEventListener('DOMContentLoaded', function() {
    fetch('../api/obtener_tabla_documentos.php')
        .then(res => res.text())
        .then(html => {
            const tbody = document.querySelector('#tablaDocumentos tbody');
            if(tbody) {
                tbody.innerHTML = html;
            }
        });
});