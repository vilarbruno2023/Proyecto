<?php
session_start();
if (empty($_SESSION['idUsuario']) || strtolower((string) ($_SESSION['rol'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado');
}
require_once __DIR__ . '/../Datos/conexion.php';
$accion = (string) ($_POST['accion'] ?? '');
try {
    if ($accion === 'crear') {
        $u = trim((string) ($_POST['nombreUsuario'] ?? ''));
        $pw = (string) ($_POST['contrasenia'] ?? '');
        $rol = (string) ($_POST['rol'] ?? '');
        if (!preg_match('/^[a-zA-Z0-9._-]{3,50}$/', $u) || strlen($pw) < 10 || !in_array($rol, ['funcionario', 'documentacion', 'doctor', 'operador', 'admin'], true))
            throw new Exception('Datos inválidos');
        $q = $conexion->prepare('INSERT INTO funcionario(nombreUsuario,contrasenia,rol) VALUES(?,?,?)');
        $q->execute([$u, password_hash($pw, PASSWORD_DEFAULT), $rol]);
    } elseif ($accion === 'cambiar_clave') {
        $id = filter_var($_POST['idUsuario'] ?? null, FILTER_VALIDATE_INT);
        $pw = (string) ($_POST['nuevaContrasenia'] ?? '');
        if (!$id || strlen($pw) < 10)
            throw new Exception('Contraseña inválida');
        $q = $conexion->prepare('UPDATE funcionario SET contrasenia=? WHERE idUsuario=?');
        $q->execute([password_hash($pw, PASSWORD_DEFAULT), $id]);
    } else
        throw new Exception('Acción inválida');
    header('Location: ../Presentacion/usuarios.html?ok=1');
} catch (Throwable $e) {
    error_log('Usuarios SIGSM: ' . $e->getMessage());
    header('Location: ../Presentacion/usuarios.html?error=1');
}
exit;
