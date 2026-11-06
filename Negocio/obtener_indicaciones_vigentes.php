<?php
require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . '/Datos/conexion.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$categorias = $conexion
    ->query('SELECT idCategoria, nombreCategoria FROM categoria ORDER BY nombreCategoria')
    ->fetchAll(PDO::FETCH_ASSOC);
// La página pública solo muestra indicaciones aprobadas.
$indicaciones = $conexion
    ->query(
        "SELECT d.idDocumento, d.titulo, d.resumen, d.archivo, c.nombreCategoria " .
        "FROM documento d JOIN categoria c ON c.idCategoria = d.idCategoria " .
        "WHERE d.estado = 'Vigente' ORDER BY d.idDocumento DESC"
    )
    ->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(
    ['categorias' => $categorias, 'indicaciones' => $indicaciones],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
