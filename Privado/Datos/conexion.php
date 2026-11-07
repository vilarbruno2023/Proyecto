<?php

$servidor = "127.0.0.1";
$puerto = "3306";
$baseDatos = "proyecto";
$usuarioBaseDatos = "root";
$contrasenaBaseDatos = "";
 
try {
    $conexion = new PDO(
        "mysql:host=$servidor;port=$puerto;dbname=$baseDatos;charset=utf8mb4",
        $usuarioBaseDatos,
        $contrasenaBaseDatos
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    error_log('Error al conectar con la base de datos: ' . $error->getMessage());
    http_response_code(500);
    die('No se pudo conectar a la base de datos.');
}