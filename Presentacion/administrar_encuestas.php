<?php
session_start();
if (!isset($_SESSION['idUsuario']) || strtolower((string)($_SESSION['rol'] ?? '')) !== 'admin') { header('Location: inicio.html'); exit; }
require_once __DIR__ . '/../Datos/conexion.php';
$tipos = ['trasplantado'=>'Usuarios trasplantados','estandar'=>'Satisfacción estándar','fnr'=>'Programas FNR'];
$filas = [];
$stmt = $conexion->query('SELECT fechaEncuesta, tipoEncuesta, segmento, satisfaccion FROM encuesta_respuesta ORDER BY fechaEncuesta DESC');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) $filas[$fila['tipoEncuesta']][] = $fila;
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$segmentosNombres = ['trasplante'=>'Atención de trasplante','seguimiento'=>'Seguimiento del trasplante','salud_reproductiva'=>'Salud Reproductiva','cardiologia'=>'Cardiología','imagenologia'=>'Imagenología','nefrologia'=>'Nefrología','prevencion'=>'Prevención','otro'=>'Otro servicio','fnr_trasplante'=>'Programa de trasplantes','fnr_cardiologia'=>'Programa de cardiología','fnr_nefrologia'=>'Programa de nefrología','fnr_otro'=>'Otro programa FNR'];
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="estilos.css"><title>Administrar encuestas</title></head><body class="pagina-admin"><header><div class="hizq"><h3>S.I.G.S.M.</h3></div><div class="hder"><a class="boton-enlace" href="SIGSM.php">Volver al panel</a></div></header><main class="pagina-contenido"><div class="contf"><div class="fsup"><h2>Respuestas de encuestas</h2><p>Las respuestas están separadas por tipo. El ID es la fecha y hora exacta del envío.</p></div>
<?php foreach ($tipos as $tipo=>$titulo): ?><section class="segmento-admin"><h3><?=h($titulo)?> <small>(<?=count($filas[$tipo] ?? [])?>)</small></h3><div class="tabla-desplazable"><table><thead><tr><th>ID (fecha y hora)</th><th>Servicio/programa</th><th>Satisfacción (1–5)</th></tr></thead><tbody><?php foreach (($filas[$tipo] ?? []) as $f): ?><tr><td><?=h($f['fechaEncuesta'])?></td><td><?=h($segmentosNombres[$f['segmento']] ?? $f['segmento'])?></td><td><?=h($f['satisfaccion'])?></td></tr><?php endforeach; ?><?php if (empty($filas[$tipo])): ?><tr><td colspan="3">Todavía no hay respuestas.</td></tr><?php endif; ?></tbody></table></div></section><?php endforeach; ?>
</div></main></body></html>