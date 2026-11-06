const tipoEncuesta = document.getElementById('tipoEncuesta');
const segmento = document.getElementById('segmento');
const mensaje = document.getElementById('mensajeEncuesta');
// Estos valores deben coincidir con los que acepta guardar_encuesta.php.
const opcionesPorEncuesta = {
    trasplantado: [
        ['trasplante', 'Atención de trasplante'],
        ['seguimiento', 'Seguimiento del trasplante']
    ],
    estandar: [
        ['salud_reproductiva', 'Salud Reproductiva'],
        ['cardiologia', 'Cardiología'],
        ['imagenologia', 'Imagenología'],
        ['nefrologia', 'Nefrología'],
        ['prevencion', 'Prevención'],
        ['otro', 'Otro servicio']
    ],
    fnr: [
        ['fnr_trasplante', 'Programa de trasplantes'],
        ['fnr_cardiologia', 'Programa de cardiología'],
        ['fnr_nefrologia', 'Programa de nefrología'],
        ['fnr_otro', 'Otro programa del FNR']
    ]
};
tipoEncuesta.addEventListener('change', function () {
    segmento.textContent = '';
    const opcionInicial = document.createElement('option');
    opcionInicial.value = '';
    opcionInicial.textContent = 'Seleccioná';
    segmento.append(opcionInicial);

    for (const [valor, texto] of opcionesPorEncuesta[tipoEncuesta.value] || []) {
        const opcion = document.createElement('option');
        opcion.value = valor;
        opcion.textContent = texto;
        segmento.append(opcion);
    }
});

const parametrosUrl = new URLSearchParams(location.search);
if (parametrosUrl.get('ok') === '1' || parametrosUrl.get('error') === '1') {
    mensaje.hidden = false;
    mensaje.textContent = parametrosUrl.get('ok') === '1'
        ? 'Gracias. Tu respuesta anónima fue registrada.'
        : 'No se pudo guardar la respuesta. Revisá los datos e intentá de nuevo.';
}
