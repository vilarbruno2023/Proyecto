<?php
session_start();
if (empty($_SESSION['idUsuario']) || strtolower((string)($_SESSION['rol'] ?? '')) !== 'documentacion') {
    http_response_code(403); exit('Solo el personal de Documentación puede crear indicaciones.');
}
require_once __DIR__ . '/../Datos/conexion.php';
$titulo = trim((string)($_POST['titulo'] ?? ''));
$idCategoria = filter_input(INPUT_POST, 'idCategoria', FILTER_VALIDATE_INT);
$resumen = trim((string)($_POST['resumen'] ?? ''));
if ($titulo === '' || strlen($titulo) > 100 || !$idCategoria || empty($_FILES['archivo']['name'])) {
    header('Location: ../Presentacion/indicaciones.php?error=1'); exit;
}
$q = $conexion->prepare('SELECT COUNT(*) FROM categoria WHERE idCategoria=?');
$q->execute([$idCategoria]);
if (!$q->fetchColumn()) { header('Location: ../Presentacion/indicaciones.php?error=1'); exit; }
$f = $_FILES['archivo'];
if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name']) || ($f['size'] ?? 0) > 10 * 1024 * 1024) {
    header('Location: ../Presentacion/indicaciones.php?error=1'); exit;
}
$ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
if ($ext !== 'pdf' || $mime !== 'application/pdf') { header('Location: ../Presentacion/indicaciones.php?error=1'); exit; }
$dir = __DIR__ . '/../Documentos/';
if (!is_dir($dir) && !mkdir($dir, 0755, true)) { header('Location: ../Presentacion/indicaciones.php?error=1'); exit; }
$nombre = 'doc_' . bin2hex(random_bytes(16)) . '.pdf';
if (!move_uploaded_file($f['tmp_name'], $dir . $nombre)) { header('Location: ../Presentacion/indicaciones.php?error=1'); exit; }
try {
    $stmt = $conexion->prepare("INSERT INTO documento(idUsuario,idCategoria,titulo,resumen,estado,archivo) VALUES(?,?,?,?, 'Revisar',?)");
    $stmt->execute([(int)$_SESSION['idUsuario'], $idCategoria, $titulo, $resumen ?: null, $nombre]);
} catch (Throwable $e) {
    @unlink($dir . $nombre); error_log('Guardar indicación: '.$e->getMessage());
    header('Location: ../Presentacion/indicaciones.php?error=1'); exit;
}
header('Location: ../Presentacion/indicaciones.php?ok=1'); exit;
