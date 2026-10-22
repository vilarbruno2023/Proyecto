<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header('Location: inicio.html');
    exit;
}
require_once __DIR__ . '/../Datos/conexion.php';
function h($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
$personal = $conexion->query("SELECT idPersonal,nombre,funcion FROM personal_traslado WHERE activo=1 ORDER BY funcion,nombre")->fetchAll(PDO::FETCH_ASSOC);
$vehiculos = $conexion->query("SELECT idVehiculo,codigo,tipo,descripcion,capacidad,requiere_asistencia FROM vehiculo WHERE activo=1 ORDER BY tipo,codigo")->fetchAll(PDO::FETCH_ASSOC);
$traslados = $conexion->query("SELECT t.*, c.nombre AS chofer, e.nombre AS enfermero, v.codigo AS codigoVehiculo FROM traslado t LEFT JOIN personal_traslado c ON c.idPersonal=t.idChofer LEFT JOIN personal_traslado e ON e.idPersonal=t.idEnfermero LEFT JOIN vehiculo v ON v.idVehiculo=t.idVehiculo ORDER BY t.horaSolicitud DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="estilos.css">
    <title>Ambulancias</title>
</head>

<body class="pagina-admin">
    <header>
        <div class="hizq">
            <h3>S.I.G.S.M. | Traslados</h3>
        </div>
        <div class="hder"><a class="boton-enlace" href="SIGSM.php">Panel</a></div>
    </header>
    <main class="pagina-contenido">
        <section class="panel">
            <div class="fsup">
                <h2>Crear solicitud de traslado</h2>
            </div>
            <form class="formulario-encuesta" action="../Negocio/guardar_traslado.php" method="post">
                <div class="campo-encuesta"><label>Qué se traslada</label><select name="tipoTraslado" id="tipoTraslado"
                        required>
                        <option value="paciente">Paciente</option>
                        <option value="elemento">Elemento</option>
                    </select></div>
                <div class="campo-encuesta campo-paciente"><label for="nombrePaciente">Nombre del paciente</label><input
                        id="nombrePaciente" name="nombrePaciente" maxlength="120" placeholder="Nombre del paciente"></div>
                <div class="campo-encuesta campo-paciente"><label for="cedulaPaciente">Cédula del paciente (opcional)</label><input
                        id="cedulaPaciente" name="cedulaPaciente" maxlength="20" placeholder="Cédula, si corresponde"></div>
                <div class="campo-encuesta campo-paciente"><label for="pacienteReferencia">Referencia opcional</label><input
                        id="pacienteReferencia" name="pacienteReferencia" maxlength="120" placeholder="Referencia operativa"></div>
                <label class="zona-roja campo-paciente"><input type="checkbox" name="zonaRoja" value="1"> <strong>Traslado en zona roja</strong> — requiere atención prioritaria</label>
                <div class="campo-encuesta"><label for="descripcionElemento">Elemento a trasladar (si
                        corresponde)</label><input id="descripcionElemento" name="descripcionElemento" maxlength="150">
                </div>
                <div class="campo-encuesta"><label>Prioridad</label><select name="prioridad">
                        <option value="normal">Normal</option>
                        <option value="urgente">Urgente</option>
                        <option value="critica">Crítica</option>
                    </select></div>
                <div class="campo-encuesta"><label>Origen</label><input name="origen" maxlength="255" required
                        placeholder="Dirección o centro de origen"></div>
                <div class="campo-encuesta"><label>Destino</label><input name="destino" maxlength="255" required
                        placeholder="Dirección o centro de destino"></div>
                <div class="campo-encuesta"><label>Ruta manual propuesta</label><textarea name="ruta" maxlength="500"
                        required placeholder="Calles, puntos de referencia o recorrido definido"></textarea></div>
                <div class="campo-encuesta"><label>Observaciones operativas</label><textarea name="observaciones"
                        maxlength="2000"></textarea></div><button type="submit">Registrar solicitud</button>
            </form>
        </section>
        <section class="panel panel-traslados">
            <div class="fsup">
                <h2>Traslados registrados</h2>
                <p>Solicitudes en tarjetas para facilitar su lectura. El personal operativo/admin puede asignar recursos y actualizar el seguimiento.</p>
            </div>
            <div class="tarjetas-traslados">
            <?php foreach ($traslados as $t): ?>
                <article class="tarjeta-traslado <?= !empty($t['zonaRoja']) ? 'tarjeta-zona-roja' : '' ?>">
                    <div class="tarjeta-traslado-cabecera">
                        <div><span class="id-traslado">Traslado #<?=h($t['idTraslado'])?></span><h3><?=h($t['tipoTraslado']==='elemento' ? ($t['descripcionElemento'] ?: 'Elemento') : ($t['nombrePaciente'] ?: 'Paciente sin nombre'))?></h3></div>
                        <div class="etiquetas-traslado"><span class="estado-traslado estado-<?=h($t['estado'])?>"><?=h(str_replace('_',' ',$t['estado']))?></span>
                        <?php if(!empty($t['zonaRoja'])): ?><span class="etiqueta-zona-roja">ZONA ROJA</span><?php endif; ?>
                        <span class="prioridad-traslado">Prioridad: <?=h($t['prioridad'])?></span></div>
                    </div>
                    <div class="datos-traslado">
                        <div><strong>Origen</strong><span><?=h($t['origen'])?></span></div><div><strong>Destino</strong><span><?=h($t['destino'])?></span></div>
                        <div><strong>Cédula</strong><span><?=h($t['cedulaPaciente'] ?: 'No indicada')?></span></div>
                        <div><strong>Ruta</strong><span><?=h($t['ruta'])?></span></div>
                        <div><strong>Vehículo</strong><span><?=h($t['codigoVehiculo'] ?: 'Sin asignar')?></span></div>
                        <div><strong>Chofer</strong><span><?=h($t['chofer'] ?: 'Sin asignar')?></span></div>
                        <div><strong>Enfermero</strong><span><?=h($t['enfermero'] ?: 'Sin asignar')?></span></div>
                        <div><strong>Solicitado</strong><span><?=h($t['horaSolicitud'])?></span></div>
                        <div><strong>Salida</strong><span><?=h($t['horaSalida'] ?: '—')?></span></div>
                        <div><strong>Llegada estimada</strong><span><?=h($t['horaEstimadaLlegada'] ?: '—')?></span></div>
                        <div><strong>Llegada efectiva</strong><span><?=h($t['horaLlegada'] ?: '—')?></span></div>
                        <?php if(!empty($t['observaciones'])): ?><div class="dato-observaciones"><strong>Observaciones</strong><span><?=h($t['observaciones'])?></span></div><?php endif; ?>
                    </div>
                    <?php if (in_array(strtolower((string)($_SESSION['rol'] ?? '')), ['admin','operador'], true)): ?>
                    <details class="editar-traslado"><summary>Asignar recursos / actualizar estado</summary>
                    <form action="../Negocio/actualizar_traslado.php" method="post" class="formulario-grid">
                        <input type="hidden" name="idTraslado" value="<?=h($t['idTraslado'])?>">
                        <label>Chofer<select name="idChofer"><option value="">Sin asignar</option><?php foreach($personal as $p) if($p['funcion']==='chofer'): ?><option value="<?=h($p['idPersonal'])?>" <?=$t['idChofer']==$p['idPersonal']?'selected':''?>><?=h($p['nombre'])?></option><?php endif; ?></select></label>
                        <label>Enfermero/a<select name="idEnfermero"><option value="">Sin asignar</option><?php foreach($personal as $p) if($p['funcion']==='enfermero'): ?><option value="<?=h($p['idPersonal'])?>" <?=$t['idEnfermero']==$p['idPersonal']?'selected':''?>><?=h($p['nombre'])?></option><?php endif; ?></select></label>
                        <label>Vehículo<select name="idVehiculo"><option value="">Sin asignar</option><?php foreach($vehiculos as $v): ?><option value="<?=h($v['idVehiculo'])?>" <?=$t['idVehiculo']==$v['idVehiculo']?'selected':''?>><?=h($v['codigo'])?> — <?=h($v['tipo'])?> (cap. <?=h($v['capacidad'])?>)</option><?php endforeach; ?></select></label>
                        <label>Estado<select name="estado"><?php foreach(['solicitado'=>'Solicitado','programado'=>'Programado','en_curso'=>'En curso','completado'=>'Completado','cancelado'=>'Cancelado'] as $valor=>$etiqueta): ?><option value="<?=h($valor)?>" <?=$t['estado']===$valor?'selected':''?>><?=h($etiqueta)?></option><?php endforeach; ?></select></label>
                        <label>Hora de salida<input type="datetime-local" name="horaSalida" value="<?=h($t['horaSalida'] ? date('Y-m-d\\TH:i',strtotime($t['horaSalida'])) : '')?>"></label>
                        <label>Llegada estimada<input type="datetime-local" name="horaEstimadaLlegada" value="<?=h($t['horaEstimadaLlegada'] ? date('Y-m-d\\TH:i',strtotime($t['horaEstimadaLlegada'])) : '')?>"></label>
                        <label>Llegada efectiva<input type="datetime-local" name="horaLlegada" value="<?=h($t['horaLlegada'] ? date('Y-m-d\\TH:i',strtotime($t['horaLlegada'])) : '')?>"></label>
                        <button type="submit">Guardar cambios</button>
                    </form></details>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
            <?php if(!$traslados): ?><p class="mensaje-vacio">Todavía no hay traslados registrados.</p><?php endif; ?>
            </div>
        </section>
    </main>
    <script>
        const tipoTraslado = document.getElementById('tipoTraslado');
        const nombrePaciente = document.getElementById('nombrePaciente');
        const descripcionElemento = document.getElementById('descripcionElemento');
        function ajustarCamposTraslado() {
            nombrePaciente.required = tipoTraslado.value === 'paciente';
            document.querySelectorAll('.campo-paciente').forEach(el => el.hidden = tipoTraslado.value !== 'paciente');
            descripcionElemento.required = tipoTraslado.value === 'elemento';
        }
        tipoTraslado.addEventListener('change', ajustarCamposTraslado); ajustarCamposTraslado();
        const parametros = new URLSearchParams(location.search);
        if (parametros.has('ok') || parametros.has('error')) alert(parametros.has('ok') ? 'Operación registrada correctamente.' : 'No se pudo guardar. Revisá los datos y los permisos.');
    </script>
</body>

</html>