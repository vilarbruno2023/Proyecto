<?php
session_start();
if (empty($_SESSION['idUsuario']) || strtolower((string)($_SESSION['rol'] ?? '')) !== 'admin') {
    http_response_code(403); exit('Acceso denegado');
}
require_once __DIR__ . '/../Datos/conexion.php';
$accion = (string)($_POST['accion'] ?? 'crear');
$destino = (($_POST['origen'] ?? '') === 'flota') ? 'administrar_flota.php' : 'administrar_usuarios.php';
try {
    if ($accion === 'crear') {
        $usuario = trim((string)($_POST['nombreUsuario'] ?? ''));
        $clave = (string)($_POST['contrasenia'] ?? '');
        $rol = strtolower(trim((string)($_POST['rol'] ?? 'funcionario')));
        $roles = ['admin','documentacion','doctor','operador','funcionario'];
        if (!preg_match('/^[a-zA-Z0-9._-]{3,50}$/', $usuario)) throw new Exception('El usuario debe tener entre 3 y 50 caracteres (letras, números, punto, guion o guion bajo).');
        if (strlen($clave) < 10) throw new Exception('La contraseña debe tener al menos 10 caracteres.');
        if (!in_array($rol, $roles, true)) throw new Exception('Rol inválido.');
        $check = $conexion->prepare('SELECT COUNT(*) FROM funcionario WHERE LOWER(nombreUsuario)=LOWER(?)');
        $check->execute([$usuario]);
        if ($check->fetchColumn()) throw new Exception('Ese nombre de usuario ya existe.');
        $q = $conexion->prepare('INSERT INTO funcionario(nombreUsuario, contrasenia, rol) VALUES(?,?,?)');
        $q->execute([$usuario, password_hash($clave, PASSWORD_DEFAULT), $rol]);
    } elseif ($accion === 'cambiar_clave') {
        $id = filter_input(INPUT_POST, 'idUsuario', FILTER_VALIDATE_INT);
        $clave = (string)($_POST['nuevaContrasenia'] ?? '');
        if (!$id || strlen($clave) < 10) throw new Exception('La contraseña debe tener al menos 10 caracteres.');
        if ($id === (int)$_SESSION['idUsuario']) {
            // El administrador puede cambiar su propia contraseña desde este panel.
        }
        $q = $conexion->prepare('UPDATE funcionario SET contrasenia=? WHERE idUsuario=?');
        $q->execute([password_hash($clave, PASSWORD_DEFAULT), $id]);
    } else {
        throw new Exception('Acción inválida.');
    }
    header('Location: ../Presentacion/' . ($accion === 'crear' ? $destino : 'administrar_usuarios.php') . '?ok=1'); exit;
} catch (Throwable $e) {
    error_log('Gestión de usuarios: '.$e->getMessage());
    $mensaje = $e instanceof PDOException && (string)$e->getCode() === '23000' ? 'Ese nombre de usuario ya existe.' : $e->getMessage();
    header('Location: ../Presentacion/' . ($accion === 'crear' ? $destino : 'administrar_usuarios.php') . '?error='.rawurlencode($mensaje)); exit;
}
