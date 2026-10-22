<?php
session_start();
$rol = strtolower((string)($_SESSION['rol'] ?? ''));
if (empty($_SESSION['idUsuario']) || $rol !== 'doctor') { http_response_code(403); exit('Solo una cuenta con rol doctor puede cambiar el estado de una indicación.'); }
require_once __DIR__ . '/../Datos/conexion.php';
$id = filter_input(INPUT_POST, 'idDocumento', FILTER_VALIDATE_INT);
$estado = (string)($_POST['estado'] ?? '');
if (!$id || !in_array($estado, ['Vigente','Revisar','Rechazada'], true)) { header('Location: ../Presentacion/indicaciones.php?error=1'); exit; }
$q = $conexion->prepare('UPDATE documento SET estado=? WHERE idDocumento=?');
$q->execute([$estado, $id]);
header('Location: ../Presentacion/indicaciones.php?estado_ok=1'); exit;
