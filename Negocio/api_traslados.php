<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['idUsuario'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Iniciá sesión.']);
    exit;
}
require_once __DIR__ . '/../Datos/conexion.php';
require_once __DIR__ . '/funciones_traslados.php';
$accion = (string) ($_GET['accion'] ?? $_POST['accion'] ?? 'listar');
try {
    if ($accion === 'listar') {
        echo json_encode(['ok' => true, 'traslados' => listarTraslados($conexion), 'recursos' => listarRecursosTraslado($conexion)], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new RuntimeException('Método no permitido.');
    }
    if ($accion === 'crear') {
        $id = crearTraslado($conexion, $_POST, (int) $_SESSION['idUsuario']);
        echo json_encode(['ok' => true, 'mensaje' => 'Traslado registrado.', 'id' => $id], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($accion === 'estado') {
        actualizarEstadoTraslado($conexion, $_POST);
        echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    http_response_code(400);
    throw new InvalidArgumentException('Acción desconocida.');
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('API traslados: ' . $e->getMessage());
    if (http_response_code() < 400)
        http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e instanceof RuntimeException ? $e->getMessage() : 'No se pudo completar la operación.'], JSON_UNESCAPED_UNICODE);
}
