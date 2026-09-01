const params = new URLSearchParams(window.location.search);
const codigoQR = params.get('codigoQR');

if (!codigoQR) {
    document.getElementById('titulo').textContent = 'Documento no disponible';
    document.getElementById('resumen').textContent = 'No se indicó el documento.';
    document.getElementById('pdfLink').style.display = 'none';
} else {
    fetch('../Negocio/documento_qr.php?codigoQR=' + encodeURIComponent(codigoQR))
        .then(response => response.json())
        .then(data => {
            if (!data.ok) {
                document.getElementById('titulo').textContent = 'Documento no disponible';
                document.getElementById('resumen').textContent = data.mensaje || 'No se pudo cargar el documento.';
                document.getElementById('pdfLink').style.display = 'none';
                return;
            }

            document.getElementById('titulo').textContent = data.titulo;
            document.getElementById('estado').textContent = 'Estado: ' + data.estado;
            document.getElementById('categoria').textContent = 'Categoría: ' + data.categoria;
            document.getElementById('codigo').textContent = 'QR: ' + data.codigoQR;
            document.getElementById('resumen').textContent = data.resumen || 'Sin resumen disponible.';

            const pdfLink = document.getElementById('pdfLink');
            pdfLink.href = data.pdfUrl || '#';
            pdfLink.addEventListener('click', function (e) {
                if (!data.pdfUrl || data.pdfUrl === '#') {
                    e.preventDefault();
                    alert('No hay PDF asociado a este documento.');
                }
            });
        })
        .catch(() => {
            document.getElementById('titulo').textContent = 'Documento no disponible';
            document.getElementById('resumen').textContent = 'No se pudo cargar el documento.';
            document.getElementById('pdfLink').style.display = 'none';
        });
}
