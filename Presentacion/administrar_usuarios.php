<?php
session_start();
if (empty($_SESSION['idUsuario']) || strtolower((string) ($_SESSION['rol'] ?? '')) !== 'admin') {
    header('Location: inicio.html');
    exit;
}
require_once __DIR__ . '/../Datos/conexion.php';
function h($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
$usuarios = $conexion->query('SELECT idUsuario,nombreUsuario,rol FROM funcionario ORDER BY nombreUsuario')->fetchAll(PDO::FETCH_ASSOC);
?><!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="estilos.css">
    <title>Usuarios y credenciales | SIGSM</title>
</head>

<body class="pagina-admin">
    <header>
        <div class="hizq">
            <h3>S.I.G.S.M. | Usuarios</h3>
        </div>
        <div class="hder"><a class="boton-enlace" href="SIGSM.php">Volver al panel</a></div>
    </header>
    <main class="pagina-contenido">
        <section class="panel">
            <h1>Crear usuario del sistema</h1>
            <p>Usá un nombre de usuario para iniciar sesión. No se solicita cédula.</p>
            <?php if (isset($_GET['ok'])): ?>
                <p class="aviso aviso-ok">Operación completada.</p><?php elseif (isset($_GET['error'])): ?>
                <p class="aviso aviso-error"><?= h($_GET['error']) ?></p><?php endif; ?>
            <form class="formulario-grid" method="post" action="../Negocio/guardar_usuario.php">
                <input type="hidden" name="accion" value="crear"><label>Nombre de usuario<input name="nombreUsuario"
                        minlength="3" maxlength="50" pattern="[a-zA-Z0-9._-]+" autocomplete="username" required></label>
                <label>Contraseña inicial<input type="password" name="contrasenia" minlength="10"
                        autocomplete="new-password" required><small>Mínimo 10 caracteres.</small></label>
                <label>Rol del sistema<select name="rol" required>
                        <option value="funcionario">Funcionario general</option>
                        <option value="documentacion">Documentación (crear indicaciones)</option>
                        <option value="doctor">Doctor/a (aprobar indicaciones)</option>
                        <option value="operador">Operador de traslados</option>
                        <option value="admin">Administrador</option>
                    </select></label><button type="submit">Crear usuario</button>
            </form>
        </section>
        <section class="panel">
            <h2>Usuarios registrados</h2>
            <div class="tabla-desplazable">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Cambiar contraseña</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?= h($u['nombreUsuario']) ?></td>
                                <td><?= h($u['rol']) ?></td>
                                <td>
                                    <form class="formulario-inline" method="post" action="../Negocio/guardar_usuario.php">
                                        <input type="hidden" name="accion" value="cambiar_clave"><input type="hidden"
                                            name="idUsuario" value="<?= h($u['idUsuario']) ?>"><input type="password"
                                            name="nuevaContrasenia" minlength="10" placeholder="Nueva contraseña"
                                            autocomplete="new-password" required><button type="submit">Actualizar</button>
                                    </form>
                                </td>
                            </tr><?php endforeach; ?>
                        <?php if (!$usuarios): ?>
                            <tr>
                                <td colspan="3">No hay usuarios.</td>
                            </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>

</html>