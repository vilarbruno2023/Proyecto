<?php
function listarTraslados(PDO $db): array
{
    $sql = "SELECT t.*, c.nombre AS chofer, e.nombre AS enfermero, v.codigo AS codigoVehiculo
            FROM traslado t
            LEFT JOIN personal_traslado c ON c.idPersonal=t.idChofer
            LEFT JOIN personal_traslado e ON e.idPersonal=t.idEnfermero
            LEFT JOIN vehiculo v ON v.idVehiculo=t.idVehiculo
            ORDER BY t.horaSolicitud DESC LIMIT 300";
    return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
function listarRecursosTraslado(PDO $db): array
{
    return [
        'personal' => $db->query("SELECT idPersonal,nombre,funcion FROM personal_traslado WHERE activo=1 ORDER BY funcion,nombre")->fetchAll(PDO::FETCH_ASSOC),
        'vehiculos' => $db->query("SELECT idVehiculo,codigo,tipo,capacidad FROM vehiculo WHERE activo=1 ORDER BY tipo,codigo")->fetchAll(PDO::FETCH_ASSOC)
    ];
}
function crearTraslado(PDO $db, array $d, int $usuario): int
{
    $tipo = (string) ($d['tipoTraslado'] ?? 'paciente');
    $prioridad = (string) ($d['prioridad'] ?? 'normal');
    $origen = trim((string) ($d['origen'] ?? ''));
    $destino = trim((string) ($d['destino'] ?? ''));
    $ruta = trim((string) ($d['ruta'] ?? ''));
    $nombre = trim((string) ($d['nombrePaciente'] ?? ''));
    $elemento = trim((string) ($d['descripcionElemento'] ?? ''));
    if (!in_array($tipo, ['paciente', 'elemento'], true) || !in_array($prioridad, ['normal', 'urgente', 'critica'], true) || !$origen || !$destino || !$ruta || ($tipo === 'paciente' && !$nombre) || ($tipo === 'elemento' && !$elemento))
        throw new InvalidArgumentException('Completá los datos obligatorios.');
    $q = $db->prepare('INSERT INTO traslado(cedulaPaciente,nombrePaciente,zonaRoja,pacienteReferencia,tipoTraslado,descripcionElemento,prioridad,origen,destino,ruta,observaciones,idSolicitante) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
    $q->execute([$tipo === 'paciente' ? (trim((string) ($d['cedulaPaciente'] ?? '')) ?: null) : null, $tipo === 'paciente' ? $nombre : null, $tipo === 'paciente' && !empty($d['zonaRoja']) ? 1 : 0, trim((string) ($d['pacienteReferencia'] ?? '')) ?: null, $tipo, $tipo === 'elemento' ? $elemento : null, $prioridad, $origen, $destino, $ruta, trim((string) ($d['observaciones'] ?? '')) ?: null, $usuario]);
    return (int) $db->lastInsertId();
}
function actualizarEstadoTraslado(PDO $db, array $d): void
{
    $id = filter_var($d['idTraslado'] ?? null, FILTER_VALIDATE_INT);
    $estado = (string) ($d['estado'] ?? '');
    $permitidos = ['en_curso', 'demorado', 'completado', 'cancelado'];
    if (!$id || !in_array($estado, $permitidos, true))
        throw new InvalidArgumentException('Estado o traslado inválido.');
    $q = $db->prepare('SELECT estado FROM traslado WHERE idTraslado=?');
    $q->execute([$id]);
    $actual = $q->fetchColumn();
    if ($actual === false)
        throw new RuntimeException('No existe el traslado.');
    $transiciones = ['solicitado' => ['en_curso', 'cancelado'], 'programado' => ['en_curso', 'cancelado'], 'en_curso' => ['demorado', 'completado', 'cancelado'], 'demorado' => ['en_curso', 'completado', 'cancelado'], 'completado' => [], 'cancelado' => []];
    if (!in_array($estado, $transiciones[$actual] ?? [], true))
        throw new InvalidArgumentException('No se permite pasar de ' . str_replace('_', ' ', $actual) . ' a ' . str_replace('_', ' ', $estado) . '.');
    $salida = null;
    $llegada = null;
    if ($estado === 'en_curso' && in_array($actual, ['solicitado', 'programado'], true))
        $salida = date('Y-m-d H:i:s');
    if ($estado === 'completado')
        $llegada = date('Y-m-d H:i:s');
    $q = $db->prepare('UPDATE traslado SET estado=?, horaSalida=COALESCE(?,horaSalida), horaLlegada=COALESCE(?,horaLlegada) WHERE idTraslado=?');
    $q->execute([$estado, $salida, $llegada, $id]);
}
