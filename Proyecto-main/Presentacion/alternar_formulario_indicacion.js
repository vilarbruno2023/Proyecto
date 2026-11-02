document.addEventListener('DOMContentLoaded', function configurarFormularioIndicacion() {
    const botonNuevaIndicacion = document.getElementById('btnNewIndicacion');
    const contenedorFormulario = document.getElementById('formularioDiv');

    if (!botonNuevaIndicacion || !contenedorFormulario) return;

    botonNuevaIndicacion.addEventListener('click', function alternarVisibilidadFormulario() {
        const formularioOculto = contenedorFormulario.style.display === 'none';
        contenedorFormulario.style.display = formularioOculto ? 'block' : 'none';
        botonNuevaIndicacion.textContent = formularioOculto ? 'Ocultar formulario' : 'Nueva indicación';
    });
});
