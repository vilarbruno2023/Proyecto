<?php
require_once __DIR__ . "/../Datos/conexion.php";

$satisfaccion = $_POST['satisfaccion'] ?? '';
$comentarios = trim($_POST['comentarios'] ?? '');

if ($satisfaccion === '') {
    header("Location: ../Presentacion/formulario.html?error=1");
    exit;
}

try {
    $stmt = $conexion->prepare("
        INSERT INTO encuesta_respuesta (satisfaccion, comentarios)
        VALUES (:satisfaccion, :comentarios)
    ");

    $stmt->execute([
        ':satisfaccion' => $satisfaccion,
        ':comentarios' => $comentarios
    ]);

    header("Location: ../Presentacion/formulario.html?ok=1");
    exit;
} catch (PDOException $e) {
    die("Error al guardar la encuesta: " . $e->getMessage());
}
