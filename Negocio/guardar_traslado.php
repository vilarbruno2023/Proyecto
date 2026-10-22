<?php
session_start();
if (empty($_SESSION['idUsuario'])) { header('Location: ../Presentacion/inicio.html'); exit; }
require_once __DIR__ . '/../Datos/conexion.php';
$tipo = (string)($_POST['tipoTraslado'] ?? 'paciente');
$cedula = trim((string)($_POST['cedulaPaciente'] ?? ''));
$nombrePaciente = trim((string)($_POST['nombrePaciente'] ?? ''));
$zonaRoja = isset($_POST['zonaRoja']) ? 1 : 0;
$ref = trim((string)($_POST['pacienteReferencia'] ?? ''));
$elemento = trim((string)($_POST['descripcionElemento'] ?? ''));
$prioridad = (string)($_POST['prioridad'] ?? 'normal');
$origen = trim((string)($_POST['origen'] ?? ''));
$destino = trim((string)($_POST['destino'] ?? ''));
$ruta = trim((string)($_POST['ruta'] ?? ''));
$obs = trim((string)($_POST['observaciones'] ?? ''));
$invalid = !in_array($tipo, ['paciente','elemento'], true) || !in_array($prioridad, ['normal','urgente','critica'], true) || $origen === '' || $destino === '' || $ruta === '' || ($tipo === 'paciente' && $nombrePaciente === '') || ($tipo === 'elemento' && $elemento === '');
if ($invalid) { header('Location: ../Presentacion/traslados.php?error=1'); exit; }
try {
 $q=$conexion->prepare('INSERT INTO traslado(cedulaPaciente,nombrePaciente,zonaRoja,pacienteReferencia,tipoTraslado,descripcionElemento,prioridad,origen,destino,ruta,observaciones,idSolicitante) VALUES(:cedula,:nombre,:zona,:referencia,:tipo,:elemento,:prioridad,:origen,:destino,:ruta,:obs,:solicitante)');
 $q->execute([':cedula'=>$tipo==='paciente' ? ($cedula ?: null) : null, ':nombre'=>$tipo==='paciente' ? $nombrePaciente : null, ':zona'=>$tipo==='paciente' ? $zonaRoja : 0, ':referencia'=>$ref ?: null, ':tipo'=>$tipo, ':elemento'=>$elemento ?: null, ':prioridad'=>$prioridad, ':origen'=>$origen, ':destino'=>$destino, ':ruta'=>$ruta, ':obs'=>$obs ?: null, ':solicitante'=>(int)$_SESSION['idUsuario']]);
 header('Location: ../Presentacion/traslados.php?ok=1'); exit;
} catch (PDOException $e) {
 error_log('Guardar traslado: '.$e->getMessage());
 header('Location: ../Presentacion/traslados.php?error=1'); exit;
}
