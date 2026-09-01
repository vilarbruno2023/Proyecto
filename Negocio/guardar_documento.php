<?php
session_start();
require_once __DIR__ . "/../Datos/conexion.php";

if (empty($_SESSION['idUsuario'])) {
    header("Location: ../Presentacion/inicio.html?error=1");
    exit;
}

$titulo = trim((string)($_POST['titulo'] ?? ''));
$idCategoria = $_POST['idCategoria'] ?? '';
$resumen = trim((string)($_POST['resumen'] ?? ''));
$estado = trim((string)($_POST['estado'] ?? 'Vigente'));

if ($titulo === '' || $idCategoria === '' || empty($_FILES['archivo']['name'])) {
    header("Location: ../Presentacion/SIGSM.html?error=1");
    exit;
}

$archivo = $_FILES['archivo'];
if ($archivo['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($archivo['tmp_name'])) {
    header("Location: ../Presentacion/SIGSM.html?error=2");
    exit;
}

$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if ($extension !== 'pdf') {
    header("Location: ../Presentacion/SIGSM.html?error=3");
    exit;
}

$carpetaDestino = __DIR__ . '/../Documentos/';
if (!is_dir($carpetaDestino)) {
    mkdir($carpetaDestino, 0777, true);
}

$nombreArchivo = 'doc_' . uniqid('', true) . '.' . $extension;
$rutaDestino = $carpetaDestino . $nombreArchivo;

if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
    header("Location: ../Presentacion/SIGSM.html?error=4");
    exit;
}

$stmt = $conexion->prepare("INSERT INTO documento (idUsuario, idCategoria, titulo, resumen, estado, archivo, codigoQR) VALUES (:idUsuario, :idCategoria, :titulo, :resumen, :estado, :archivo, :codigoQR)");

$stmt->execute([
    'idUsuario' => (int) $_SESSION['idUsuario'],
    'idCategoria' => (int) $idCategoria,
    'titulo' => $titulo,
    'resumen' => $resumen,
    'estado' => $estado,
    'archivo' => $nombreArchivo,
    'codigoQR' => 'QR_' . uniqid()
]);

header("Location: ../Presentacion/SIGSM.html?ok=1");
exit;