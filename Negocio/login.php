<?php
session_start();
require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . DIRECTORY_SEPARATOR . 'Datos' . DIRECTORY_SEPARATOR . 'conexion.php';

$usuario = trim((string) ($_POST['usuario'] ?? ''));
$clave = (string) ($_POST['clave'] ?? '');
$stmt = $conexion->prepare('SELECT idUsuario, nombreUsuario, contrasenia, rol FROM funcionario WHERE nombreUsuario = :usuario LIMIT 1');
$stmt->execute(['usuario' => $usuario]);
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

$valida = false;
if ($funcionario) {
    $guardada = (string) $funcionario['contrasenia'];
    if (password_verify($clave, $guardada)) {
        $valida = true;
    } elseif (hash_equals($guardada, $clave)) {
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        $actualizar = $conexion->prepare('UPDATE funcionario SET contrasenia = ? WHERE idUsuario = ?');
        $actualizar->execute([$hash, $funcionario['idUsuario']]);
        $valida = true;
    }
}
if ($valida) {
    session_regenerate_id(true);
    $_SESSION['idUsuario'] = (int) $funcionario['idUsuario'];
    $_SESSION['nombre'] = $funcionario['nombreUsuario'];
    $_SESSION['rol'] = strtolower((string) $funcionario['rol']);
    $rol = $_SESSION['rol'];
    if ($rol === 'admin')
        header('Location: ../Presentacion/pagina.php?pagina=panel');
    elseif (in_array($rol, ['doctor', 'documentacion'], true))
        header('Location: ../Presentacion/pagina.php?pagina=indicaciones');
    elseif (in_array($rol, ['operador', 'funcionario'], true))
        header('Location: ../Presentacion/pagina.php?pagina=traslados');
    else
        header('Location: ../Presentacion/index.html');
    exit;
}
header('Location: ../Presentacion/inicio.html?error=1');
exit;
