<?php
session_start();
require_once __DIR__ . "/../Datos/conexion.php";

if (empty($_SESSION['idUsuario'])) {
    header("Location: ../Presentacion/inicio.html?error=1");
    exit;
}

$tituloIndicacion = trim((string)($_POST['titulo'] ?? ''));
$idCategoriaSeleccionada = $_POST['idCategoria'] ?? '';
$resumenIndicacion = trim((string)($_POST['resumen'] ?? ''));
$estadoIndicacion = trim((string)($_POST['estado'] ?? 'Vigente'));

if ($tituloIndicacion === '' || $idCategoriaSeleccionada === '' || empty($_FILES['archivo']['name'])) {
    header("Location: ../Presentacion/SIGSM.html?error=1");
    exit;
}

$archivoSubido = $_FILES['archivo'];
if ($archivoSubido['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($archivoSubido['tmp_name'])) {
    header("Location: ../Presentacion/SIGSM.html?error=2");
    exit;
}

$extension = strtolower(pathinfo($archivoSubido['name'], PATHINFO_EXTENSION));

if ($extension !== 'pdf') {
    header("Location: ../Presentacion/SIGSM.html?error=3");
    exit;
}

$directorioDocumentos = __DIR__ . '/../Documentos/';
if (!is_dir($directorioDocumentos)) {
    mkdir($directorioDocumentos, 0777, true);
}

$nombreArchivoGuardado = 'doc_' . uniqid('', true) . '.' . $extension;
$rutaDestinoArchivo = $directorioDocumentos . $nombreArchivoGuardado;

if (!move_uploaded_file($archivoSubido['tmp_name'], $rutaDestinoArchivo)) {
    header("Location: ../Presentacion/SIGSM.html?error=4");
    exit;
}

$stmt = $conexion->prepare("INSERT INTO documento (idUsuario, idCategoria, titulo, resumen, estado, archivo) VALUES (:idUsuario, :idCategoria, :titulo, :resumen, :estado, :archivo)");

$stmt->execute([
    'idUsuario' => (int) $_SESSION['idUsuario'],
    'idCategoria' => (int) $idCategoriaSeleccionada,
    'titulo' => $tituloIndicacion,
    'resumen' => $resumenIndicacion,
    'estado' => $estadoIndicacion,
    'archivo' => $nombreArchivoGuardado
]);

header("Location: ../Presentacion/SIGSM.html?ok=1");
exit;