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

<body>
    <header>
        <div class="hizq">
            <h3>S.I.G.S.M. | Traslados</h3>
        </div>
        <div class="hder"><a class="boton-enlace" href="SIGSM.php">Panel</a></div>
    </header>
    <main class="centro">
        <section class="contf">
            <div class="fsup">
                <h2>Crear solicitud de traslado</h2>
            </div>
            <form class="formulario-encuesta" action="../Negocio/guardar_traslado.php" method="post">
                <div class="campo-encuesta"><label>Qué se traslada</label><select name="tipoTraslado" id="tipoTraslado"
                        required>
                        <option value="paciente">Paciente</option>
                        <option value="elemento">Elemento</option>
                    </select></div>
                <div class="campo-encuesta"><label for="cedulaPaciente">Cédula del paciente (manual)</label><input
                        id="cedulaPaciente" name="cedulaPaciente" maxlength="20"
                        placeholder="Ingresar cédula (obligatoria para pacientes)"></div>
                <div class="campo-encuesta"><label for="pacienteReferencia">Referencia opcional del
                        paciente</label><input id="pacienteReferencia" name="pacienteReferencia" maxlength="120"
                        placeholder="No incluir información clínica innecesaria"></div>
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
        <section class="contf" style="margin-top:22px">
            <div class="fsup">
                <h2>Traslados registrados</h2>
                <p>El solicitante registra la solicitud. El personal autorizado actualiza asignaciones y seguimiento.
                </p>
            </div>
            <div class="tabla-desplazable">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cédula / elemento</th>
                            <th>Origen → destino</th>
                            <th>Ruta</th>
                            <th>Asignación</th>
                            <th>Estado y horarios</th>
                            <th>Actualizar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($traslados as $t): ?>
                            <tr>
                                <td><?= h($t['idTraslado']) ?></td>
                                <td><?= h($t['tipoTraslado'] === 'elemento' ? ($t['descripcionElemento'] ?: 'Elemento') : $t['cedulaPaciente']) ?>
                                </td>
                                <td><?= h($t['origen']) ?> → <?= h($t['destino']) ?></td>
                                <td><?= h($t['ruta']) ?></td>
                                <td><?= h($t['codigoVehiculo'] ?: 'Sin vehículo') ?>; chofer:
                                    <?= h($t['chofer'] ?: 'Sin asignar') ?>; enfermero:
                                    <?= h($t['enfermero'] ?: 'Sin asignar') ?></td>
                                <td><?= h($t['estado']) ?><br>Salida: <?= h($t['horaSalida'] ?: '—') ?><br>Estimada:
                                    <?= h($t['horaEstimadaLlegada'] ?: '—') ?><br>Llegada: <?= h($t['horaLlegada'] ?: '—') ?>
                                </td>
                                <td><?php if (in_array(strtolower((string) ($_SESSION['rol'] ?? '')), ['admin', 'operador'], true)): ?>
                                        <form action="../Negocio/actualizar_traslado.php" method="post"
                                            class="formulario-encuesta"><input type="hidden" name="idTraslado"
                                                value="<?= h($t['idTraslado']) ?>"><select name="idChofer">
                                                <option value="">Chofer sin asignar</option>
                                                <?php foreach ($personal as $p)
                                                    if ($p['funcion'] === 'chofer'): ?>
                                                        <option value="<?= h($p['idPersonal']) ?>"
                                                            <?= $t['idChofer'] == $p['idPersonal'] ? 'selected' : '' ?>><?= h($p['nombre']) ?>
                                                        </option><?php endif; ?>
                                            </select><select name="idEnfermero">
                                                <option value="">Enfermero sin asignar</option>
                                                <?php foreach ($personal as $p)
                                                    if ($p['funcion'] === 'enfermero'): ?>
                                                        <option value="<?= h($p['idPersonal']) ?>"
                                                            <?= $t['idEnfermero'] == $p['idPersonal'] ? 'selected' : '' ?>><?= h($p['nombre']) ?>
                                                        </option><?php endif; ?>
                                            </select><select name="idVehiculo">
                                                <option value="">Vehículo sin asignar</option>
                                                <?php foreach ($vehiculos as $v): ?>
                                                    <option value="<?= h($v['idVehiculo']) ?>"
                                                        <?= $t['idVehiculo'] == $v['idVehiculo'] ? 'selected' : '' ?>><?= h($v['codigo']) ?> —
                                                        <?= h($v['tipo']) ?> (cap. <?= h($v['capacidad']) ?>)</option>
                                                <?php endforeach; ?>
                                            </select><select name="estado">
                                                <option value="solicitado" <?= $t['estado'] === 'solicitado' ? 'selected' : '' ?>>
                                                    Solicitado</option>
                                                <option value="programado" <?= $t['estado'] === 'programado' ? 'selected' : '' ?>>
                                                    Programado</option>
                                                <option value="en_curso" <?= $t['estado'] === 'en_curso' ? 'selected' : '' ?>>En curso
                                                </option>
                                                <option value="completado" <?= $t['estado'] === 'completado' ? 'selected' : '' ?>>
                                                    Completado</option>
                                                <option value="cancelado" <?= $t['estado'] === 'cancelado' ? 'selected' : '' ?>>
                                                    Cancelado</option>
                                            </select><label>Hora salida</label><input type="datetime-local" name="horaSalida"
                                                value="<?= h($t['horaSalida'] ? date('Y-m-d\TH:i', strtotime($t['horaSalida'])) : '') ?>"><label>Llegada
                                                estimada</label><input type="datetime-local" name="horaEstimadaLlegada"
                                                value="<?= h($t['horaEstimadaLlegada'] ? date('Y-m-d\TH:i', strtotime($t['horaEstimadaLlegada'])) : '') ?>"><label>Hora
                                                llegada efectiva</label><input type="datetime-local" name="horaLlegada"
                                                value="<?= h($t['horaLlegada'] ? date('Y-m-d\TH:i', strtotime($t['horaLlegada'])) : '') ?>"><button
                                                type="submit">Guardar cambios</button></form><?php else: ?>Solo personal
                                        operativo/admin puede actualizar.<?php endif; ?>
                                </td>
                            </tr><?php endforeach; ?><?php if (!$traslados): ?>
                            <tr>
                                <td colspan="7">No hay solicitudes registradas.</td>
                            </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script>
        const tipoTraslado = document.getElementById('tipoTraslado');
        const cedulaPaciente = document.getElementById('cedulaPaciente');
        const descripcionElemento = document.getElementById('descripcionElemento');
        function ajustarCamposTraslado() { cedulaPaciente.required = tipoTraslado.value === 'paciente'; descripcionElemento.required = tipoTraslado.value === 'elemento'; }
        tipoTraslado.addEventListener('change', ajustarCamposTraslado); ajustarCamposTraslado();
        const parametros = new URLSearchParams(location.search); if (parametros.has('ok') || parametros.has('error')) alert(parametros.has('ok') ? 'Operación registrada correctamente.' : 'No se pudo guardar. Revisá los datos y los permisos.');
    </script>
</body>

</html>