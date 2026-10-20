<?php
require_once __DIR__ . "/../Datos/conexion.php";
$segmentos = [
 'trasplantado' => ['trasplante','seguimiento'],
 'estandar' => ['salud_reproductiva','cardiologia','imagenologia','nefrologia','prevencion','otro'],
 'fnr' => ['fnr_trasplante','fnr_cardiologia','fnr_nefrologia','fnr_otro']
];
$tipo = (string)($_POST['tipoEncuesta'] ?? '');
$segmento = (string)($_POST['segmento'] ?? '');
$satisfaccion = filter_input(INPUT_POST, 'satisfaccion', FILTER_VALIDATE_INT);
if (!isset($segmentos[$tipo]) || !in_array($segmento, $segmentos[$tipo], true) || $satisfaccion === false || $satisfaccion === null || $satisfaccion < 1 || $satisfaccion > 5) {
 header('Location: ../Presentacion/formulario.html?error=1'); exit;
}
try {
 $stmt = $conexion->prepare('INSERT INTO encuesta_respuesta (fechaEncuesta, tipoEncuesta, segmento, satisfaccion) VALUES (CURRENT_TIMESTAMP(6), :tipo, :segmento, :satisfaccion)');
 $stmt->execute([':tipo'=>$tipo, ':segmento'=>$segmento, ':satisfaccion'=>$satisfaccion]);
 header('Location: ../Presentacion/formulario.html?ok=1'); exit;
} catch (PDOException $e) { error_log('Error al guardar encuesta: '.$e->getMessage()); header('Location: ../Presentacion/formulario.html?error=1'); exit; }
