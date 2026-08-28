<?php
declare(strict_types=1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido. Usa GET.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_GET['action'] ?? '') !== 'getall') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Acción no válida. Usa action=getall.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    require_once __DIR__ . '/conexion.php';
    $conexion = obtenerConexion();
    $consulta = $conexion->query(
        'SELECT id, nombre, especialista, indicacion, url_ficticia
         FROM documentos_medicos
         WHERE activo = 1
         ORDER BY creado_en DESC, id DESC'
    );

    echo json_encode([
        'success' => true,
        'data' => $consulta->fetchAll(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'No fue posible consultar los documentos.',
    ], JSON_UNESCAPED_UNICODE);
}

