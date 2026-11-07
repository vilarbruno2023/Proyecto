<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (
    empty($_SESSION['idUsuario']) ||
    !in_array(strtolower((string) ($_SESSION['rol'] ?? '')), ['admin', 'documentacion'], true)
) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No tenés permiso para crear indicaciones.'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . '/Datos/conexion.php';

if (!is_dir(DIRECTORIO_DOCUMENTOS) || !is_writable(DIRECTORIO_DOCUMENTOS)) {
    error_log('No existe o no se puede escribir en el directorio de documentos: ' . DIRECTORIO_DOCUMENTOS);
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No está disponible el almacenamiento privado de documentos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$titulo = trim((string) ($_POST['titulo'] ?? ''));
$resumen = trim((string) ($_POST['resumen'] ?? ''));
$idCategoria = filter_var($_POST['idCategoria'] ?? null, FILTER_VALIDATE_INT);

if (
    $titulo === '' ||
    mb_strlen($titulo, 'UTF-8') > 100 ||
    mb_strlen($resumen, 'UTF-8') > 4000 ||
    !$idCategoria ||
    empty($_FILES['archivo']) ||
    $_FILES['archivo']['error'] !== UPLOAD_ERR_OK
) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Revisá el título, la categoría y el archivo.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$archivoSubido = $_FILES['archivo'];
if ($archivoSubido['size'] > 12 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'El archivo supera 12 MB.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Solo se admiten archivos PDF reales (máximo 12 MB).
$extension = strtolower(pathinfo($archivoSubido['name'], PATHINFO_EXTENSION));
$detectorMime = new finfo(FILEINFO_MIME_TYPE);
if ($extension !== 'pdf' || $detectorMime->file($archivoSubido['tmp_name']) !== 'application/pdf') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Solo se permiten archivos PDF.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$consultaCategoria = $conexion->prepare('SELECT COUNT(*) FROM categoria WHERE idCategoria = ?');
$consultaCategoria->execute([$idCategoria]);
if (!(int) $consultaCategoria->fetchColumn()) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'La categoría seleccionada no existe.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$nombreArchivo = 'doc_' . bin2hex(random_bytes(12)) . '.pdf';
$rutaArchivo = DIRECTORIO_DOCUMENTOS . '/' . $nombreArchivo;
if (!move_uploaded_file($archivoSubido['tmp_name'], $rutaArchivo)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el archivo.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $consultaDocumento = $conexion->prepare(
        "INSERT INTO documento (idUsuario, idCategoria, titulo, resumen, archivo, estado) " .
        "VALUES (?, ?, ?, ?, ?, 'Revisar')"
    );
    $consultaDocumento->execute([
        (int) $_SESSION['idUsuario'],
        $idCategoria,
        $titulo,
        $resumen ?: null,
        $nombreArchivo
    ]);
} catch (Throwable $error) {
    error_log('Error al registrar indicación: ' . $error->getMessage());
    // Si falló el registro, se elimina el PDF que ya se había subido.
    if (is_file($rutaArchivo) && !unlink($rutaArchivo)) {
        error_log('No se pudo eliminar el PDF no registrado: ' . $rutaArchivo);
    }
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo registrar la indicación.'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['ok' => true, 'mensaje' => 'Indicación enviada a revisión.'], JSON_UNESCAPED_UNICODE);
