<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(['autenticado' => false]);
    exit;
}

echo json_encode([
    'autenticado' => true,
    'idUsuario' => $_SESSION['idUsuario'],
    'nombre' => $_SESSION['nombre'],
    'rol' => $_SESSION['rol']
]);