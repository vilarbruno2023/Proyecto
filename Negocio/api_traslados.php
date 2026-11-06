<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['idUsuario'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Iniciá sesión.']);
    exit;
}
$rol = strtolower((string) ($_SESSION['rol'] ?? ''));
// El permiso se verifica en el servidor, no solo en la pantalla.
if (!in_array($rol, ['admin', 'documentacion', 'doctor', 'operador', 'funcionario'], true)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No tenés permiso para acceder a los traslados.']);
    exit;
}
require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . '/Datos/conexion.php';
require_once __DIR__ . '/funciones_traslados.php';
$accion = (string) ($_GET['accion'] ?? $_POST['accion'] ?? 'listar');
try {
    if ($accion === 'listar') {
        $nombrePaciente = trim((string) ($_POST['paciente'] ?? ''));
        $codigoVehiculo = trim((string) ($_POST['vehiculo'] ?? ''));

        echo json_encode([
            'ok' => true,
            'traslados' => listarTraslados($conexion, $nombrePaciente, $codigoVehiculo),
            'recursos' => listarRecursosTraslado($conexion)
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new RuntimeException('Método no permitido.');
    }
    if ($accion === 'crear') {
        $idTraslado = crearTraslado($conexion, $_POST, (int) $_SESSION['idUsuario']);
        echo json_encode(['ok' => true, 'mensaje' => 'Traslado registrado.', 'id' => $idTraslado], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($accion === 'editar') {
        editarTraslado($conexion, $_POST);
        echo json_encode(['ok' => true, 'mensaje' => 'Traslado actualizado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($accion === 'estado') {
        actualizarEstadoTraslado($conexion, $_POST);
        echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    http_response_code(400);
    throw new InvalidArgumentException('Acción desconocida.');
} catch (InvalidArgumentException $error) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $error->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $error) {
    error_log('API traslados: ' . $error->getMessage());
    if (http_response_code() < 400)
        http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $error instanceof RuntimeException ? $error->getMessage() : 'No se pudo completar la operación.'], JSON_UNESCAPED_UNICODE);
}
