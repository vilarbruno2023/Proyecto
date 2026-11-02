'use strict';
const API_TRASLADOS = '../Negocio/api_traslados.php';
const $ = (id) => document.getElementById(id);
const mensaje = (texto, tipo = 'ok') => { const el = $('mensaje'); el.className = 'mensaje ' + tipo; el.textContent = texto; };
const campo = (tag, texto, clase = '') => { const el = document.createElement(tag); if (clase) el.className = clase; el.textContent = texto ?? '—'; return el; };
function mostrarCampos() {
    const paciente = $('tipoTraslado').value === 'paciente';
    ['campoNombre', 'campoCedula', 'campoReferencia', 'campoZona'].forEach(id => $(id).hidden = !paciente);
    $('campoElemento').hidden = paciente;
    $('formCrear').elements.nombrePaciente.required = paciente;
    $('formCrear').elements.descripcionElemento.required = !paciente;
}
function datos(label, value) { const d = document.createElement('div'); d.append(campo('strong', label), campo('span', value)); return d; }
function crearTarjeta(t) {
    const card = document.createElement('article'); card.className = 'traslado-card' + (Number(t.zonaRoja) === 1 ? ' zona-roja' : '');
    const head = document.createElement('div'); head.className = 'traslado-head';
    const title = document.createElement('div'); title.append(campo('small', 'Traslado #' + t.idTraslado), campo('h3', t.tipoTraslado === 'elemento' ? (t.descripcionElemento || 'Elemento') : (t.nombrePaciente || 'Paciente')));
    const tags = document.createElement('div'); tags.append(campo('span', String(t.estado).replaceAll('_', ' '), 'tag'));
    if (Number(t.zonaRoja) === 1) tags.append(campo('span', 'ZONA ROJA', 'tag roja'));
    tags.append(campo('span', 'Prioridad: ' + t.prioridad, 'tag')); head.append(title, tags); card.append(head);
    const grid = document.createElement('div'); grid.className = 'datos-card';
    [['Origen', t.origen], ['Destino', t.destino], ['Cédula', t.cedulaPaciente || 'No indicada'], ['Ruta', t.ruta], ['Vehículo', t.codigoVehiculo || 'Sin asignar'], ['Chofer asignado', t.chofer || 'Sin asignar'], ['Enfermero asignado', t.enfermero || 'Sin asignar'], ['Solicitado', t.horaSolicitud], ['Salida', t.horaSalida || '—'], ['Llegada estimada', t.horaEstimadaLlegada || '—'], ['Llegada efectiva', t.horaLlegada || '—'], ['Observaciones', t.observaciones || '—']].forEach(([k, v]) => grid.append(datos(k, v)));
    card.append(grid);
    if (!['completado', 'cancelado'].includes(t.estado)) {
        const form = document.createElement('form'); form.className = 'estado-form'; form.dataset.id = t.idTraslado;
        const select = document.createElement('select'); select.name = 'estado';
        const opciones = t.estado === 'solicitado' || t.estado === 'programado' ? [['en_curso', 'En curso'], ['cancelado', 'Cancelado']] : t.estado === 'en_curso' ? [['demorado', 'Demorado'], ['completado', 'Completado'], ['cancelado', 'Cancelado']] : [['en_curso', 'Reanudar (en curso)'], ['completado', 'Completado'], ['cancelado', 'Cancelado']];
        opciones.forEach(([v, l]) => { const o = document.createElement('option'); o.value = v; o.textContent = l; select.append(o) });
        const btn = document.createElement('button'); btn.type = 'submit'; btn.textContent = 'Actualizar estado'; form.append(campo('strong', 'Seguimiento'), select, btn); card.append(form);
    }
    return card;
}
async function cargar() {
    const r = await fetch(API_TRASLADOS + '?accion=listar', { credentials: 'same-origin' }); const data = await r.json();
    if (!r.ok || !data.ok) throw new Error(data.error || 'No se pudieron cargar los traslados.');
    const lista = $('listaTraslados'); lista.replaceChildren();
    if (!data.traslados.length) { lista.textContent = 'Todavía no hay traslados registrados.'; return; }
    data.traslados.forEach(t => lista.append(crearTarjeta(t)));
}
$('tipoTraslado').addEventListener('change', mostrarCampos); mostrarCampos();
$('formCrear').addEventListener('submit', async e => { e.preventDefault(); const f = e.currentTarget; const fd = new FormData(f); fd.append('accion', 'crear'); try { const r = await fetch(API_TRASLADOS, { method: 'POST', body: fd, credentials: 'same-origin' }); const d = await r.json(); if (!r.ok || !d.ok) throw new Error(d.error || 'No se pudo registrar.'); mensaje('Traslado #' + d.id + ' registrado correctamente.'); f.reset(); mostrarCampos(); await cargar(); } catch (err) { mensaje(err.message, 'error'); } });
$('listaTraslados').addEventListener('submit', async e => { const f = e.target.closest('form.estado-form'); if (!f) return; e.preventDefault(); const fd = new FormData(f); fd.append('accion', 'estado'); fd.append('idTraslado', f.dataset.id); try { const r = await fetch(API_TRASLADOS, { method: 'POST', body: fd, credentials: 'same-origin' }); const d = await r.json(); if (!r.ok || !d.ok) throw new Error(d.error || 'No se pudo actualizar.'); mensaje(d.mensaje); await cargar(); } catch (err) { mensaje(err.message, 'error'); } });
cargar().catch(err => { $('listaTraslados').textContent = err.message; mensaje(err.message, 'error'); });
