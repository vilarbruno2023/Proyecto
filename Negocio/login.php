<?php
session_start();
require_once __DIR__ . "/../Datos/conexion.php";

$usuario = trim((string)($_POST['usuario'] ?? ''));
$clave = (string)($_POST['clave'] ?? '');

$sql = "SELECT * FROM funcionario WHERE nombreUsuario = :usuario LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->execute(['usuario' => $usuario]);
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($funcionario && $funcionario['contrasenia'] === $clave) {
    $_SESSION['idUsuario'] = $funcionario['idUsuario'];
    $_SESSION['nombre'] = $funcionario['nombreUsuario'];
    $_SESSION['rol'] = strtolower((string)$funcionario['rol']);

    if ($_SESSION['rol'] === 'admin') {
        header("Location: ../Presentacion/SIGSM.html");
    } else {
        header("Location: ../Presentacion/index.html");
    }
    exit;
}

header("Location: ../Presentacion/inicio.html?error=1");
exit;
