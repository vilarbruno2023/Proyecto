<?php
session_start();
require_once __DIR__ . "/../Datos/conexion.php";
require_once __DIR__ . "/../Datos/Clases/Documento.php";

if (empty($_SESSION['idUsuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../Presentacion/inicio.html?error=1");
    exit;
}

$id = $_POST['idDocumento'] ?? null;

if ($id) {
    Documento::eliminar($conexion, (int)$id);
}

header("Location: ../Presentacion/SIGSM.html");
exit;