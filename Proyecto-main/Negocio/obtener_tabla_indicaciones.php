<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['idUsuario']) || !in_array(strtolower((string) ($_SESSION['rol'] ?? '')), ['admin', 'doctor', 'documentacion'], true)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Acceso denegado']);
    exit;
}
require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . DIRECTORY_SEPARATOR . 'Datos' . DIRECTORY_SEPARATOR . 'conexion.php';
$q = $conexion->query('SELECT d.idDocumento,d.titulo,d.estado,d.resumen,d.archivo,c.nombreCategoria FROM documento d JOIN categoria c ON c.idCategoria=d.idCategoria ORDER BY d.idDocumento DESC');
echo json_encode($q->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
