<?php
session_start();
if (!isset($_SESSION['idUsuario']) || strtolower((string) ($_SESSION['rol'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado');
}
require_once __DIR__ . '/../Datos/conexion.php';
$a = (string) ($_POST['accion'] ?? '');
try {
    if ($a === 'personal') {
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $doc = trim((string) ($_POST['documento'] ?? ''));
        $funcion = (string) ($_POST['funcion'] ?? '');
        if ($nombre === '' || !in_array($funcion, ['doctor', 'chofer', 'enfermero', 'administrativo'], true))
            throw new Exception('Datos inválidos');
        $q = $conexion->prepare('INSERT INTO personal_traslado(nombre,documento,funcion) VALUES(?,?,?)');
        $q->execute([$nombre, $doc ?: null, $funcion]);
    } elseif ($a === 'vehiculo') {
        $codigo = trim((string) ($_POST['codigo'] ?? ''));
        $tipo = (string) ($_POST['tipo'] ?? '');
        $desc = trim((string) ($_POST['descripcion'] ?? ''));
        $cap = filter_input(INPUT_POST, 'capacidad', FILTER_VALIDATE_INT);
        if ($codigo === '' || !in_array($tipo, ['ambulancia', 'auto', 'utilitario', 'otro'], true) || !$cap || $cap < 1 || $cap > 50)
            throw new Exception('Datos inválidos');
        $q = $conexion->prepare('INSERT INTO vehiculo(codigo,tipo,descripcion,capacidad,requiere_asistencia) VALUES(?,?,?,?,?)');
        $q->execute([$codigo, $tipo, $desc ?: null, $cap, isset($_POST['requiere_asistencia']) ? 1 : 0]);
    } elseif ($a === 'estado_personal') {
        $q = $conexion->prepare('UPDATE personal_traslado SET activo=? WHERE idPersonal=?');
        $q->execute([(int) ($_POST['activo'] ?? 0), (int) ($_POST['id'] ?? 0)]);
    } elseif ($a === 'estado_vehiculo') {
        $q = $conexion->prepare('UPDATE vehiculo SET activo=? WHERE idVehiculo=?');
        $q->execute([(int) ($_POST['activo'] ?? 0), (int) ($_POST['id'] ?? 0)]);
    } else
        throw new Exception('Acción inválida');
    header('Location: ../Presentacion/administrar_flota.php?ok=1');
    exit;
} catch (Throwable $e) {
    error_log('Personal/vehiculo: ' . $e->getMessage());
    header('Location: ../Presentacion/administrar_flota.php?error=1');
    exit;
}
