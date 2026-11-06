<?php
require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . '/Datos/conexion.php';
// El servidor valida las opciones aunque el formulario limite lo que se puede elegir.
$segmentos = [
    'trasplantado' => ['trasplante', 'seguimiento'],
    'estandar' => ['salud_reproductiva', 'cardiologia', 'imagenologia', 'nefrologia', 'prevencion', 'otro'],
    'fnr' => ['fnr_trasplante', 'fnr_cardiologia', 'fnr_nefrologia', 'fnr_otro']
];
$tipoEncuesta = (string) ($_POST['tipoEncuesta'] ?? '');
$segmento = (string) ($_POST['segmento'] ?? '');
$satisfaccion = filter_input(INPUT_POST, 'satisfaccion', FILTER_VALIDATE_INT);
if (!isset($segmentos[$tipoEncuesta]) || !in_array($segmento, $segmentos[$tipoEncuesta], true) || $satisfaccion === false || $satisfaccion === null || $satisfaccion < 1 || $satisfaccion > 5) {
    header('Location: ../Presentacion/formulario.html?error=1');
    exit;
}
try {
    $consulta = $conexion->prepare('INSERT INTO encuesta_respuesta (tipoEncuesta, segmento, satisfaccion) VALUES (:tipoEncuesta, :segmento, :satisfaccion)');
    $consulta->execute([':tipoEncuesta' => $tipoEncuesta, ':segmento' => $segmento, ':satisfaccion' => $satisfaccion]);
    header('Location: ../Presentacion/formulario.html?ok=1');
    exit;
} catch (PDOException $error) {
    error_log('Error al guardar encuesta: ' . $error->getMessage());
    header('Location: ../Presentacion/formulario.html?error=1');
    exit;
}
