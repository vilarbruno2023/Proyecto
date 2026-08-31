<?php

$host = "127.0.0.1";
$port = "3306;
$baseDatos = "proyecto";
$usuarioDB = "root";
$contraseñaDB = "";
 
try {
    $conexion = new PDO(
        "mysql:host=$host;port=$port;dbname=$baseDatos;charset=utf8mb4",
        $usuarioDB,
        $contraseñaDB
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}