<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header('Location: ../Presentacion/inicio.html');
    exit;
}
require_once __DIR__ . '/../Datos/conexion.php';
$tipo = $_POST['tipoTraslado'] ?? 'paciente';
$cedula = trim((string) ($_POST['cedulaPaciente'] ?? ''));
$ref = trim((string) ($_POST['pacienteReferencia'] ?? ''));
$elemento = trim((string) ($_POST['descripcionElemento'] ?? ''));
$prioridad = $_POST['prioridad'] ?? 'normal';
$origen = trim((string) ($_POST['origen'] ?? ''));
$destino = trim((string) ($_POST['destino'] ?? ''));
$ruta = trim((string) ($_POST['ruta'] ?? ''));
$obs = trim((string) ($_POST['observaciones'] ?? ''));
if (!in_array($tipo, ['paciente', 'elemento'], true) || !in_array($prioridad, ['normal', 'urgente', 'critica'], true) || $origen === '' || $destino === '' || $ruta === '' || ($tipo === 'paciente' && $cedula === '') || ($tipo === 'elemento' && $elemento === '')) {
    header('Location: ../Presentacion/traslados.php?error=1');
    exit;
}
try {
    $q = $conexion->prepare('INSERT INTO traslado (cedulaPaciente,pacienteReferencia,tipoTraslado,descripcionElemento,prioridad,origen,destino,ruta,observaciones,idSolicitante) VALUES (:cedula,:referencia,:tipo,:elemento,:prioridad,:origen,:destino,:ruta,:obs,:solicitante)');
    $q->execute([':cedula' => $tipo === 'paciente' ? $cedula : 'N/A', ':referencia' => $ref ?: null, ':tipo' => $tipo, ':elemento' => $elemento ?: null, ':prioridad' => $prioridad, ':origen' => $origen, ':destino' => $destino, ':ruta' => $ruta, ':obs' => $obs ?: null, ':solicitante' => $_SESSION['idUsuario']]);
    header('Location: ../Presentacion/traslados.php?ok=1');
    exit;
} catch (PDOException $e) {
    error_log('Guardar traslado: ' . $e->getMessage());
    header('Location: ../Presentacion/traslados.php?error=1');
    exit;
}
