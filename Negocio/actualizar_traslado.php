<?php
session_start();
$rol = strtolower((string) ($_SESSION['rol'] ?? ''));
if (!isset($_SESSION['idUsuario']) || !in_array($rol, ['admin', 'operador'], true)) {
    http_response_code(403);
    exit('Acceso denegado');
}
require_once __DIR__ . '/../Datos/conexion.php';
$id = filter_input(INPUT_POST, 'idTraslado', FILTER_VALIDATE_INT);
$chofer = filter_input(INPUT_POST, 'idChofer', FILTER_VALIDATE_INT);
$enfermero = filter_input(INPUT_POST, 'idEnfermero', FILTER_VALIDATE_INT);
$vehiculo = filter_input(INPUT_POST, 'idVehiculo', FILTER_VALIDATE_INT);
$estado = (string) ($_POST['estado'] ?? 'solicitado');
$estados = ['solicitado', 'programado', 'en_curso', 'completado', 'cancelado'];
$salida = trim((string) ($_POST['horaSalida'] ?? ''));
$estimada = trim((string) ($_POST['horaEstimadaLlegada'] ?? ''));
$llegada = trim((string) ($_POST['horaLlegada'] ?? ''));
if (!$id || !in_array($estado, $estados, true)) {
    header('Location: ../Presentacion/traslados.php?error=1');
    exit;
}
function fechaSql($v)
{
    return $v === '' ? null : str_replace('T', ' ', $v) . (strlen($v) === 16 ? ':00' : '');
}
try {
    if ($chofer) {
        $q = $conexion->prepare("SELECT COUNT(*) FROM personal_traslado WHERE idPersonal=? AND funcion='chofer' AND activo=1");
        $q->execute([$chofer]);
        if (!$q->fetchColumn())
            throw new Exception('Chofer inválido');
    }
    if ($enfermero) {
        $q = $conexion->prepare("SELECT COUNT(*) FROM personal_traslado WHERE idPersonal=? AND funcion='enfermero' AND activo=1");
        $q->execute([$enfermero]);
        if (!$q->fetchColumn())
            throw new Exception('Enfermero inválido');
    }
    if ($vehiculo) {
        $q = $conexion->prepare('SELECT tipo,requiere_asistencia FROM vehiculo WHERE idVehiculo=? AND activo=1');
        $q->execute([$vehiculo]);
        $v = $q->fetch(PDO::FETCH_ASSOC);
        if (!$v)
            throw new Exception('Vehículo inválido');
        $q = $conexion->prepare('SELECT tipoTraslado FROM traslado WHERE idTraslado=?');
        $q->execute([$id]);
        $tipo = $q->fetchColumn();
        if ($tipo === 'paciente' && $v['tipo'] === 'utilitario')
            throw new Exception('Ese vehículo no está habilitado para traslados de pacientes.');
    }
    if ($vehiculo && !$chofer)
        throw new Exception('Asigná un chofer antes de asignar un vehículo.');
    $q = $conexion->prepare('SELECT tipoTraslado FROM traslado WHERE idTraslado=?');
    $q->execute([$id]);
    $tipoFinal = $q->fetchColumn();
    if ($tipoFinal === 'paciente' && $vehiculo && !$enfermero)
        throw new Exception('Todo traslado de paciente debe tener un enfermero asignado.');
    $q = $conexion->prepare('UPDATE traslado SET idChofer=:chofer,idEnfermero=:enfermero,idVehiculo=:vehiculo,estado=:estado,horaSalida=:salida,horaEstimadaLlegada=:estimada,horaLlegada=:llegada WHERE idTraslado=:id');
    $q->execute([':chofer' => $chofer ?: null, ':enfermero' => $enfermero ?: null, ':vehiculo' => $vehiculo ?: null, ':estado' => $estado, ':salida' => fechaSql($salida), ':estimada' => fechaSql($estimada), ':llegada' => fechaSql($llegada), ':id' => $id]);
    header('Location: ../Presentacion/traslados.php?ok=1');
    exit;
} catch (Throwable $e) {
    error_log('Actualizar traslado: ' . $e->getMessage());
    header('Location: ../Presentacion/traslados.php?error=1');
    exit;
}
