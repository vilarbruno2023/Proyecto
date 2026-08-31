document.addEventListener('DOMContentLoaded', function() {
    const btnNewIndicacion = document.getElementById('btnNewIndicacion');
    const formularioDiv = document.getElementById('formularioDiv');

    if (btnNewIndicacion && formularioDiv) {
        btnNewIndicacion.addEventListener('click', function() {
            if (formularioDiv.style.display === 'none') {
                formularioDiv.style.display = 'block';
                btnNewIndicacion.textContent = 'Ocultar formulario';
            } else {
                formularioDiv.style.display = 'none';
                btnNewIndicacion.textContent = 'Nueva indicación';
            }
        });
    }
});
