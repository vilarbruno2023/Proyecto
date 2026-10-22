<?php
session_start();
if (empty($_SESSION['idUsuario']) || strtolower((string)($_SESSION['rol'] ?? '')) !== 'admin') { http_response_code(403); exit('Acceso denegado'); }
require_once __DIR__ . '/../Datos/conexion.php';
$nombre = trim((string)($_POST['nombreCategoria'] ?? ''));
if ($nombre === '' || strlen($nombre) > 50) { header('Location: ../Presentacion/indicaciones.php?cat_error=1'); exit; }
try {
 $check=$conexion->prepare('SELECT COUNT(*) FROM categoria WHERE LOWER(nombreCategoria)=LOWER(?)'); $check->execute([$nombre]);
 if ($check->fetchColumn()) { header('Location: ../Presentacion/indicaciones.php?cat_error=2'); exit; }
 $q=$conexion->prepare('INSERT INTO categoria(nombreCategoria) VALUES(?)'); $q->execute([$nombre]);
 header('Location: ../Presentacion/indicaciones.php?cat_ok=1'); exit;
} catch (Throwable $e) {
 error_log('Categoría: '.$e->getMessage());
 header('Location: ../Presentacion/indicaciones.php?cat_error=2'); exit;
}
