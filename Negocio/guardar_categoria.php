<?php
session_start();
if (empty($_SESSION['idUsuario']) || strtolower((string) ($_SESSION['rol'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado');
}
require_once __DIR__ . '/../Datos/conexion.php';
$n = trim((string) ($_POST['nombreCategoria'] ?? ''));
if (!$n || mb_strlen($n) > 50) {
    http_response_code(400);
    exit('Nombre inválido');
}
try {
    $q = $conexion->prepare('INSERT INTO categoria(nombreCategoria) VALUES(?)');
    $q->execute([$n]);
    header('Location: ../Presentacion/indicaciones.html?cat_ok=1');
} catch (Throwable $e) {
    error_log($e->getMessage());
    header('Location: ../Presentacion/indicaciones.html?cat_error=1');
}
exit;
