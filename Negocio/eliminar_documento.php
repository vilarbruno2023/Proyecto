<?php
session_start();
require_once __DIR__ . "/../Datos/conexion.php";

$id = $_POST['idDocumento'] ?? null;

if ($id) {
    $stmt = $conexion->prepare("DELETE FROM documento WHERE idDocumento = :id");
    $stmt->execute(['id' => $id]);
}

header("Location: ../Presentacion/SIGSM.html");
exit;