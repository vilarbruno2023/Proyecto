<?php
session_start();
require_once "conexion.php";

$id = $_POST['idDocumento'] ?? null;

if ($id) {
    $stmt = $conexion->prepare("DELETE FROM documento WHERE idDocumento = :id");
    $stmt->execute(['id' => $id]);
}

header("Location: ../htmls/SIGSM.html");
exit;