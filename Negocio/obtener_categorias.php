<?php
require_once __DIR__ . '/../Datos/conexion.php';
header('Content-Type: application/json; charset=utf-8');
$categorias = $conexion->query('SELECT idCategoria, nombreCategoria FROM categoria ORDER BY nombreCategoria')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($categorias, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
