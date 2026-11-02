<?php
session_start();
if (empty($_SESSION['idUsuario']) || strtolower((string) ($_SESSION['rol'] ?? '')) !== 'documentacion') {
    http_response_code(403);
    exit('Solo Documentación puede crear indicaciones.');
}
require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . DIRECTORY_SEPARATOR . 'Datos' . DIRECTORY_SEPARATOR . 'conexion.php';
$t = trim((string) ($_POST['titulo'] ?? ''));
$r = trim((string) ($_POST['resumen'] ?? ''));
$cat = filter_var($_POST['idCategoria'] ?? null, FILTER_VALIDATE_INT);
if (!$t || strlen($t) > 100 || !$cat || empty($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit('Datos incompletos.');
}
$f = $_FILES['archivo'];
if ($f['size'] > 12 * 1024 * 1024) {
    http_response_code(400);
    exit('El PDF supera 12 MB.');
}
$fi = new finfo(FILEINFO_MIME_TYPE);
if ($fi->file($f['tmp_name']) !== 'application/pdf') {
    http_response_code(400);
    exit('El archivo debe ser PDF.');
}
$q = $conexion->prepare('SELECT COUNT(*) FROM categoria WHERE idCategoria=?');
$q->execute([$cat]);
if (!$q->fetchColumn()) {
    http_response_code(400);
    exit('Categoría inexistente.');
}
$nombre = 'doc_' . bin2hex(random_bytes(12)) . '.pdf';
$dest = __DIR__ . '/../Documentos/' . $nombre;
if (!move_uploaded_file($f['tmp_name'], $dest)) {
    http_response_code(500);
    exit('No se pudo guardar el archivo.');
}
try {
    $q = $conexion->prepare("INSERT INTO documento(idUsuario,idCategoria,titulo,resumen,archivo,estado) VALUES(?,?,?,?,?,'Revisar')");
    $q->execute([(int) $_SESSION['idUsuario'], $cat, $t, $r, $nombre]);
} catch (Throwable $e) {
    @unlink($dest);
    error_log($e->getMessage());
    http_response_code(500);
    exit('No se pudo registrar la indicación.');
}
header('Location: ../Presentacion/pagina.php?pagina=indicaciones&ok=1');
exit;
