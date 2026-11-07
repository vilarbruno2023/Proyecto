<?php
require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . '/Datos/conexion.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (isset($_GET['id'])) {
    $idDocumento = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$idDocumento || $idDocumento < 1) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Indicación inválida.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $consulta = $conexion->prepare(
        "SELECT d.idDocumento, d.titulo, d.resumen, c.nombreCategoria " .
        "FROM documento d JOIN categoria c ON c.idCategoria = d.idCategoria " .
        "WHERE d.idDocumento = ? AND d.estado = 'Vigente'"
    );
    $consulta->execute([$idDocumento]);
    $indicacion = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$indicacion) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'No se encontró la indicación.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(
        ['ok' => true, 'indicacion' => $indicacion],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

$categorias = $conexion
    ->query('SELECT idCategoria, nombreCategoria FROM categoria ORDER BY nombreCategoria')
    ->fetchAll(PDO::FETCH_ASSOC);
// La página pública solo muestra indicaciones aprobadas.
$indicaciones = $conexion
    ->query(
        "SELECT d.idDocumento, d.titulo, d.resumen, c.nombreCategoria " .
        "FROM documento d JOIN categoria c ON c.idCategoria = d.idCategoria " .
        "WHERE d.estado = 'Vigente' ORDER BY d.idDocumento DESC"
    )
    ->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(
    ['categorias' => $categorias, 'indicaciones' => $indicaciones],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
