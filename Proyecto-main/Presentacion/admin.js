const API = '../Negocio/api_administracion.php';
const $ = s => document.querySelector(s);
const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
async function api(action, data) {
    const opt = data ? { method: 'POST', body: new FormData(Object.assign(document.createElement('form'), {})) } : {};
    if (data) { const fd = new FormData(); Object.entries(data).forEach(([k, v]) => fd.append(k, v)); opt.method = 'POST'; opt.body = fd; }
    const r = await fetch(`${API}?accion=${encodeURIComponent(action)}`, opt); const j = await r.json(); if (!r.ok || !j.ok) throw Error(j.error || 'No se pudo completar.'); return j;
}
function aviso(msg, error = false) { const el = $('#aviso'); if (el) { el.textContent = msg; el.className = error ? 'aviso aviso-error' : 'aviso aviso-ok'; } }
function page(title, body) { document.title = title + ' | SIGSM'; $('#contenido').innerHTML = body; }
async function loadIndicaciones() {
    try {
        const d = await api('indicaciones'); let body = '';
        if (d.puedeCrear) body += `<section class="panel"><h2>Nueva indicación</h2><p>Se guarda como Revisar. Solo un doctor puede aprobarla.</p><form id="nuevaIndicacion" class="formulario-grid"><label>Título<input name="titulo" maxlength="100" required></label><label>Categoría<select name="idCategoria" required><option value="">Seleccioná</option>${d.categorias.map(c => `<option value="${c.idCategoria}">${esc(c.nombreCategoria)}</option>`).join('')}</select></label><label class="ancho-completo">Resumen<textarea name="resumen" maxlength="4000"></textarea></label><label class="ancho-completo">PDF<input type="file" name="archivo" accept="application/pdf,.pdf" required></label><button>Enviar a revisión</button></form></section>`;
        if (d.puedeCategorias) body += `<section class="panel"><h2>Categorías</h2><form id="categoria" class="formulario-inline"><input name="nombreCategoria" maxlength="50" placeholder="Nueva categoría" required><button>Agregar</button></form></section>`;
        const columnasAdicionales = Number(d.puedeAprobar) + Number(d.puedeEliminar);
        const filasIndicaciones = d.indicaciones.map(indicacion => `
            <tr>
                <td>${esc(indicacion.titulo)}</td>
                <td>${esc(indicacion.nombreCategoria)}</td>
                <td>${esc(indicacion.estado)}</td>
                <td>${esc(indicacion.resumen)}</td>
                <td><a target="_blank" rel="noopener" href="../Documentos/${encodeURIComponent(indicacion.archivo.split(/[\\\\/]/).pop())}">Ver PDF</a></td>
                ${d.puedeAprobar ? `<td><form class="estado-doc formulario-inline" data-id="${indicacion.idDocumento}"><select name="estado">${['Revisar', 'Vigente', 'Rechazada'].map(estado => `<option ${estado === indicacion.estado ? 'selected' : ''}>${estado}</option>`).join('')}</select><button>Guardar</button></form></td>` : ''}
                ${d.puedeEliminar ? `<td><form class="eliminar-doc formulario-inline" data-id="${indicacion.idDocumento}"><button type="submit">Eliminar</button></form></td>` : ''}
            </tr>
        `).join('');
        const encabezadoEliminar = d.puedeEliminar ? '<th>Acciones</th>' : '';
        const columnasTotales = 5 + columnasAdicionales;
        body += `<section class="panel"><h2>Indicaciones registradas</h2><div class="tabla-desplazable"><table><thead><tr><th>Título</th><th>Categoría</th><th>Estado</th><th>Resumen</th><th>PDF</th>${d.puedeAprobar ? '<th>Revisión</th>' : ''}${encabezadoEliminar}</tr></thead><tbody>${filasIndicaciones || `<tr><td colspan="${columnasTotales}">No hay indicaciones.</td></tr>`}</tbody></table></div></section>`;
        page('Indicaciones', body);
        $('#nuevaIndicacion')?.addEventListener('submit', async e => { e.preventDefault(); try { const r = await fetch('../Negocio/guardar_documento.php', { method: 'POST', body: new FormData(e.target) }); if (!r.ok) throw Error('No se pudo guardar el PDF.'); aviso('Indicación enviada a revisión.'); loadIndicaciones(); } catch (x) { aviso(x.message, true); } });
        $('#categoria')?.addEventListener('submit', async e => { e.preventDefault(); try { await api('crear_categoria', Object.fromEntries(new FormData(e.target))); aviso('Categoría agregada.'); loadIndicaciones(); } catch (x) { aviso(x.message, true); } });
        document.querySelectorAll('.estado-doc').forEach(f => f.addEventListener('submit', async e => { e.preventDefault(); try { await api('aprobar_indicacion', { idDocumento: f.dataset.id, estado: new FormData(f).get('estado') }); aviso('Estado actualizado.'); loadIndicaciones(); } catch (x) { aviso(x.message, true); } }));
        document.querySelectorAll('.eliminar-doc').forEach(formulario => formulario.addEventListener('submit', async e => {
            e.preventDefault();
            if (!confirm('¿Seguro que querés eliminar esta indicación y su PDF?')) return;
            try {
                await api('eliminar_indicacion', { idDocumento: formulario.dataset.id });
                aviso('Indicación eliminada.');
                loadIndicaciones();
            } catch (error) {
                aviso(error.message, true);
            }
        }));
    } catch (e) { aviso(e.message, true); }
}
async function loadUsuarios() {
    try {
        const d = await api('usuarios'); page('Usuarios', `<section class="panel"><h1>Crear usuario</h1><form id="crearUsuario" class="formulario-grid"><label>Nombre de usuario<input name="nombreUsuario" minlength="3" maxlength="50" pattern="[a-zA-Z0-9._-]+" required></label><label>Contraseña (mínimo 10)<input type="password" name="contrasenia" minlength="10" required></label><label>Rol<select name="rol">${['funcionario', 'documentacion', 'doctor', 'operador', 'admin'].map(x => `<option value="${x}">${x}</option>`).join('')}</select></label><button>Crear usuario</button></form></section><section class="panel"><h2>Usuarios registrados</h2><div class="tabla-desplazable"><table><thead><tr><th>Usuario</th><th>Rol</th><th>Cambiar contraseña</th></tr></thead><tbody>${d.usuarios.map(u => `<tr><td>${esc(u.nombreUsuario)}</td><td>${esc(u.rol)}</td><td><form class="clave formulario-inline" data-id="${u.idUsuario}"><input type="password" name="nuevaContrasenia" minlength="10" placeholder="Nueva contraseña" required><button>Actualizar</button></form></td></tr>`).join('')}</tbody></table></div></section>`);
        $('#crearUsuario').onsubmit = async e => { e.preventDefault(); try { await api('crear_usuario', Object.fromEntries(new FormData(e.target))); aviso('Usuario creado.'); loadUsuarios(); } catch (x) { aviso(x.message, true); } };
        document.querySelectorAll('.clave').forEach(f => f.onsubmit = async e => { e.preventDefault(); try { await api('cambiar_clave', { idUsuario: f.dataset.id, nuevaContrasenia: new FormData(f).get('nuevaContrasenia') }); aviso('Contraseña actualizada.'); } catch (x) { aviso(x.message, true); } });
    } catch (e) { aviso(e.message, true); }
}
async function loadEncuestas() {
    try {
        const d = await api('encuestas'); const names = { trasplantado: 'Usuarios trasplantados', estandar: 'Satisfacción estándar', fnr: 'Programas FNR' }; const seg = { trasplante: 'Atención de trasplante', seguimiento: 'Seguimiento del trasplante', salud_reproductiva: 'Salud Reproductiva', cardiologia: 'Cardiología', imagenologia: 'Imagenología', nefrologia: 'Nefrología', prevencion: 'Prevención', otro: 'Otro servicio', fnr_trasplante: 'Programa de trasplantes', fnr_cardiologia: 'Programa de cardiología', fnr_nefrologia: 'Programa de nefrología', fnr_otro: 'Otro programa FNR' };
        page('Encuestas', `<section class="panel"><h1>Respuestas de encuestas</h1><p>Identificador: fecha y hora exacta de envío. Respuestas anónimas.</p></section>${Object.entries(names).map(([k, v]) => { const rows = d.respuestas.filter(x => x.tipoEncuesta === k); return `<section class="panel"><h2>${v} (${rows.length})</h2><div class="tabla-desplazable"><table><thead><tr><th>Fecha y hora</th><th>Servicio/programa</th><th>Satisfacción</th></tr></thead><tbody>${rows.map(x => `<tr><td>${esc(x.fechaEncuesta)}</td><td>${esc(seg[x.segmento] || x.segmento)}</td><td>${esc(x.satisfaccion)}/5</td></tr>`).join('') || '<tr><td colspan="3">Todavía no hay respuestas.</td></tr>'}</tbody></table></div></section>` }).join('')}`);
    } catch (e) { aviso(e.message, true); }
}
async function loadFlota() {
    try {
        const d = await api('flota'); page('Personal y vehículos', `<section class="panel"><h1>Personal operativo</h1><p>Los registros operativos no son cuentas de acceso al sistema. Los choferes no necesitan iniciar sesión.</p><form id="personal" class="formulario-grid"><label>Nombre completo<input name="nombre" maxlength="100" required></label><label>Documento (opcional)<input name="documento" maxlength="20"></label><label>Función<select name="funcion"><option value="doctor">Doctor/a</option><option value="chofer">Chofer</option><option value="enfermero">Enfermero/a</option><option value="administrativo">Administrativo/a</option></select></label><button>Registrar personal</button></form><div class="tabla-desplazable"><table><thead><tr><th>Nombre</th><th>Documento</th><th>Función</th><th>Estado</th><th>Acción</th></tr></thead><tbody>${d.personal.map(x => `<tr><td>${esc(x.nombre)}</td><td>${esc(x.documento)}</td><td>${esc(x.funcion)}</td><td>${x.activo ? 'Activo' : 'Inactivo'}</td><td><button class="pers" data-id="${x.idPersonal}" data-act="${x.activo ? 0 : 1}">${x.activo ? 'Desactivar' : 'Activar'}</button></td></tr>`).join('')}</tbody></table></div></section><section class="panel"><h2>Vehículos</h2><form id="vehiculo" class="formulario-grid"><label>Código<input name="codigo" maxlength="30" required></label><label>Tipo<select name="tipo"><option>ambulancia</option><option>auto</option><option>utilitario</option><option>otro</option></select></label><label>Descripción<input name="descripcion" maxlength="150"></label><label>Capacidad<input type="number" name="capacidad" min="1" max="50" value="1"></label><label><input type="checkbox" name="requiere_asistencia" value="1"> Requiere enfermería</label><button>Registrar vehículo</button></form><div class="tabla-desplazable"><table><thead><tr><th>Código</th><th>Tipo</th><th>Descripción</th><th>Capacidad</th><th>Estado</th><th>Acción</th></tr></thead><tbody>${d.vehiculos.map(x => `<tr><td>${esc(x.codigo)}</td><td>${esc(x.tipo)}</td><td>${esc(x.descripcion)}</td><td>${x.capacidad}</td><td>${x.activo ? 'Activo' : 'Inactivo'}</td><td><button class="veh" data-id="${x.idVehiculo}" data-act="${x.activo ? 0 : 1}">${x.activo ? 'Desactivar' : 'Activar'}</button></td></tr>`).join('')}</tbody></table></div></section>`);
        $('#personal').onsubmit = async e => { e.preventDefault(); try { await api('crear_personal', Object.fromEntries(new FormData(e.target))); aviso('Personal registrado.'); loadFlota(); } catch (x) { aviso(x.message, true); } };
        $('#vehiculo').onsubmit = async e => { e.preventDefault(); try { await api('crear_vehiculo', Object.fromEntries(new FormData(e.target))); aviso('Vehículo registrado.'); loadFlota(); } catch (x) { aviso(x.message, true); } };
        document.querySelectorAll('.pers').forEach(b => b.onclick = async () => { try { await api('estado_personal', { id: b.dataset.id, activo: b.dataset.act }); loadFlota(); } catch (x) { aviso(x.message, true); } });
        document.querySelectorAll('.veh').forEach(b => b.onclick = async () => { try { await api('estado_vehiculo', { id: b.dataset.id, activo: b.dataset.act }); loadFlota(); } catch (x) { aviso(x.message, true); } });
    } catch (e) { aviso(e.message, true); }
}
document.addEventListener('DOMContentLoaded', () => { const route = document.body.dataset.page; ({ indicaciones: loadIndicaciones, usuarios: loadUsuarios, encuestas: loadEncuestas, flota: loadFlota })[route]?.(); });
