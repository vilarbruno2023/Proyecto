<?php
session_start();
if (empty($_SESSION['idUsuario']) || strtolower((string) ($_SESSION['rol'] ?? '')) !== 'doctor') {
    http_response_code(403);
    exit('Solo un doctor puede revisar indicaciones.');
}
require_once __DIR__ . '/../Datos/conexion.php';
$id = filter_var($_POST['idDocumento'] ?? null, FILTER_VALIDATE_INT);
$estado = (string) ($_POST['estado'] ?? '');
if (!$id || !in_array($estado, ['Revisar', 'Vigente', 'Rechazada'], true)) {
    http_response_code(400);
    exit('Datos inválidos.');
}
$q = $conexion->prepare('UPDATE documento SET estado=? WHERE idDocumento=?');
$q->execute([$estado, $id]);
header('Location: ../Presentacion/indicaciones.html?estado_ok=1');
exit;
