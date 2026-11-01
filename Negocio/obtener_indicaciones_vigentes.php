<?php
require_once __DIR__ . '/../Datos/conexion.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$q = $conexion->query("SELECT d.idDocumento,d.titulo,d.resumen,d.archivo,c.nombreCategoria FROM documento d JOIN categoria c ON c.idCategoria=d.idCategoria WHERE d.estado='Vigente' ORDER BY d.idDocumento DESC");
echo json_encode($q->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
