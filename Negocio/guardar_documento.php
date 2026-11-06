<?php
session_start();

if (
    empty($_SESSION['idUsuario']) ||
    !in_array(strtolower((string) ($_SESSION['rol'] ?? '')), ['admin', 'documentacion'], true)
) {
    http_response_code(403);
    exit('No tenés permiso para crear indicaciones.');
}

require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . '/Datos/conexion.php';

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
    exit('Revisá el título, la categoría y el archivo PDF.');
}

$archivoSubido = $_FILES['archivo'];
if ($archivoSubido['size'] > 12 * 1024 * 1024) {
    http_response_code(400);
    exit('El PDF supera 12 MB.');
}

$detectorMime = new finfo(FILEINFO_MIME_TYPE);
if ($detectorMime->file($archivoSubido['tmp_name']) !== 'application/pdf') {
    http_response_code(400);
    exit('El archivo debe ser PDF.');
}

$consultaCategoria = $conexion->prepare('SELECT COUNT(*) FROM categoria WHERE idCategoria = ?');
$consultaCategoria->execute([$idCategoria]);
if (!(int) $consultaCategoria->fetchColumn()) {
    http_response_code(400);
    exit('La categoría seleccionada no existe.');
}

$nombreArchivo = 'doc_' . bin2hex(random_bytes(12)) . '.pdf';
$rutaArchivo = __DIR__ . '/../Documentos/' . $nombreArchivo;
if (!move_uploaded_file($archivoSubido['tmp_name'], $rutaArchivo)) {
    http_response_code(500);
    exit('No se pudo guardar el archivo.');
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
    exit('No se pudo registrar la indicación.');
}

header('Location: ../Presentacion/pagina.php?pagina=indicaciones&ok=1');
exit;
