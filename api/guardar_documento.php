<?php
session_start();
require_once "conexion.php";

$titulo = $_POST['titulo'] ?? '';
$idCategoria = $_POST['idCategoria'] ?? '';
$resumen = $_POST['resumen'] ?? '';
$estado = $_POST['estado'] ?? 'Vigente';

if ($titulo !== '' && $idCategoria !== '') {
    $idUsuario = $_SESSION['idUsuario'] ?? 1;

    $stmt = $conexion->prepare("INSERT INTO documento (idUsuario, idCategoria, titulo, resumen, estado, archivo, codigoQR) VALUES (:idUsuario, :idCategoria, :titulo, :resumen, :estado, 'documento.pdf', :codigoQR)");
    
    $stmt->execute([
        'idUsuario' => $idUsuario,
        'idCategoria' => $idCategoria,
        'titulo' => $titulo,
        'resumen' => $resumen,
        'estado' => $estado,
        'codigoQR' => 'QR_' . uniqid()
    ]);
}

header("Location: ../htmls/SIGSM.html");
exit;