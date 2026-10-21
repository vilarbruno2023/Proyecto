<?php
session_start();
if (!isset($_SESSION['idUsuario']) || strtolower((string) ($_SESSION['rol'] ?? '')) !== 'admin') {
    header('Location: inicio.html');
    exit;
}
require_once __DIR__ . '/../Datos/conexion.php';
function h($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
$personal = $conexion->query('SELECT * FROM personal_traslado ORDER BY funcion,nombre')->fetchAll(PDO::FETCH_ASSOC);
$vehiculos = $conexion->query('SELECT * FROM vehiculo ORDER BY tipo,codigo')->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="estilos.css">
    <title>Administrar personal y vehículos</title>
</head>

<body>
    <header>
        <div class="hizq">
            <h3>S.I.G.S.M.</h3>
        </div>
        <div class="hder"><a class="boton-enlace" href="SIGSM.php">Panel</a><a class="boton-enlace"
                href="traslados.php">Traslados</a></div>
    </header>
    <main class="centro">
        <section class="contf">
            <div class="fsup">
                <h2>Registrar personal</h2>
                <p>Choferes pueden manejar toda la flota compatible. El enfermero es responsable del paciente durante el
                    traslado.</p>
            </div>
            <form class="formulario-encuesta" method="post" action="../Negocio/guardar_personal_vehiculo.php"><input
                    type="hidden" name="accion" value="personal"><label>Nombre completo</label><input name="nombre"
                    required maxlength="100"><label>Cédula o documento (opcional)</label><input name="documento"
                    maxlength="20"><label>Función</label><select name="funcion" required>
                    <option value="doctor">Doctor/a</option>
                    <option value="chofer">Chofer</option>
                    <option value="enfermero">Enfermero/a</option>
                    <option value="administrativo">Administrativo/a</option>
                </select><button>Registrar personal</button></form>
            <div class="tabla-desplazable">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Documento</th>
                            <th>Función</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($personal as $p): ?>
                            <tr>
                                <td><?= h($p['nombre']) ?></td>
                                <td><?= h($p['documento']) ?></td>
                                <td><?= h($p['funcion']) ?></td>
                                <td><?= $p['activo'] ? 'Activo' : 'Inactivo' ?></td>
                                <td>
                                    <form method="post" action="../Negocio/guardar_personal_vehiculo.php"><input
                                            type="hidden" name="accion" value="estado_personal"><input type="hidden"
                                            name="id" value="<?= h($p['idPersonal']) ?>"><input type="hidden" name="activo"
                                            value="<?= $p['activo'] ? 0 : 1 ?>"><button><?= $p['activo'] ? 'Desactivar' : 'Activar' ?></button>
                                    </form>
                                </td>
                            </tr><?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="contf" style="margin-top:22px">
            <div class="fsup">
                <h2>Registrar vehículo</h2>
            </div>
            <form class="formulario-encuesta" method="post" action="../Negocio/guardar_personal_vehiculo.php"><input
                    type="hidden" name="accion" value="vehiculo"><label>Código interno</label><input name="codigo"
                    required maxlength="30" placeholder="AMB-01"><label>Tipo de vehículo</label><select name="tipo">
                    <option value="ambulancia">Ambulancia</option>
                    <option value="auto">Auto</option>
                    <option value="utilitario">Utilitario</option>
                    <option value="otro">Otro</option>
                </select><label>Descripción</label><input name="descripcion" maxlength="150"><label>Capacidad de
                    pasajeros</label><input type="number" name="capacidad" min="1" max="50" value="1"
                    required><label><input type="checkbox" name="requiere_asistencia" value="1"> Requiere asistencia de
                    enfermería para pacientes</label><button>Registrar vehículo</button></form>
            <div class="tabla-desplazable">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($vehiculos as $v): ?>
                            <tr>
                                <td><?= h($v['codigo']) ?></td>
                                <td><?= h($v['tipo']) ?></td>
                                <td><?= h($v['descripcion']) ?></td>
                                <td><?= h($v['capacidad']) ?></td>
                                <td><?= $v['activo'] ? 'Activo' : 'Inactivo' ?></td>
                                <td>
                                    <form method="post" action="../Negocio/guardar_personal_vehiculo.php"><input
                                            type="hidden" name="accion" value="estado_vehiculo"><input type="hidden"
                                            name="id" value="<?= h($v['idVehiculo']) ?>"><input type="hidden" name="activo"
                                            value="<?= $v['activo'] ? 0 : 1 ?>"><button><?= $v['activo'] ? 'Desactivar' : 'Activar' ?></button>
                                    </form>
                                </td>
                            </tr><?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>

</html>