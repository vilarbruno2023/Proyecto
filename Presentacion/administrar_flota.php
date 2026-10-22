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

<body class="pagina-admin">
    <header>
        <div class="hizq">
            <h3>S.I.G.S.M.</h3>
        </div>
        <div class="hder"><a class="boton-enlace" href="SIGSM.php">Panel</a><a class="boton-enlace" href="administrar_usuarios.php">Usuarios y credenciales</a><a class="boton-enlace" href="traslados.php">Traslados</a></div>
    </header>
    <main class="pagina-contenido">
        <section class="panel">
            <h1>Registrar usuario del sistema</h1>
            <p>Esta cuenta permite iniciar sesión en SIGSM. No requiere cédula ni nombre completo; utilizá un nombre de usuario único.</p>
            <form class="formulario-grid" method="post" action="../Negocio/guardar_usuario.php">
                <input type="hidden" name="accion" value="crear"><input type="hidden" name="origen" value="flota">
                <label>Nombre de usuario<input name="nombreUsuario" minlength="3" maxlength="50" pattern="[a-zA-Z0-9._-]+" autocomplete="username" required></label>
                <label>Contraseña inicial<input type="password" name="contrasenia" minlength="10" autocomplete="new-password" required><small>Mínimo 10 caracteres.</small></label>
                <label>Rol<select name="rol" required><option value="funcionario">Funcionario general</option><option value="documentacion">Documentación (crear indicaciones)</option><option value="doctor">Doctor/a (aprobar indicaciones)</option><option value="operador">Operador de traslados</option><option value="admin">Administrador</option></select></label>
                <button type="submit">Crear cuenta</button>
            </form>
            <p><a href="administrar_usuarios.php">Ver usuarios y cambiar contraseñas</a></p>
        </section>
        <section class="panel">
            <div class="fsup">
                <h2>Registrar integrante del personal operativo</h2>
                <p>Este listado es independiente de las credenciales y sirve para asignar choferes, enfermeros y doctores a los traslados. Los choferes pueden manejar toda la flota compatible. El enfermero es responsable del paciente durante el
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
        <section class="panel">
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