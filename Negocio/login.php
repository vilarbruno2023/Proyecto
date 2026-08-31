<?php
session_start();
require_once "conexion.php";

$usuario = $_POST['usuario'] ?? '';
$clave = $_POST['clave'] ?? '';

    
$sql = "SELECT * FROM funcionario WHERE nombreUsuario = :usuario";

$stmt = $conexion->prepare($sql);
$stmt->execute(['usuario' => $usuario]);
    
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($funcionario && password_verify($clave, $funcionario['contrasenia'])) {
    $_SESSION['idUsuario'] = $funcionario['idUsuario'];
    $_SESSION['nombre'] = $funcionario['nombreUsuario'];
    $_SESSION['rol'] = $funcionario['rol'];
    
    if ($funcionario['rol'] === 'Admin') {
        header("Location: ../htmls/SIGSM.html");
    } else {
        header("Location: ../htmls/index.html");
    }
    exit;
} 
header("Location: ../htmls/inicio.html?error=1");
exit;
