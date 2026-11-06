<?php
function listarTraslados(PDO $conexion, string $nombrePaciente = '', string $codigoVehiculo = ''): array
{
    // Las subconsultas mantienen los traslados aunque no tengan recursos asignados.
    $consultaSQL = "SELECT traslado.*,
            (SELECT nombre FROM personal_traslado WHERE idPersonal = traslado.idChofer) AS chofer,
            (SELECT nombre FROM personal_traslado WHERE idPersonal = traslado.idEnfermero) AS enfermero,
            (SELECT codigo FROM vehiculo WHERE idVehiculo = traslado.idVehiculo) AS codigoVehiculo
            FROM traslado";
    $valores = [];

    // Los filtros buscan coincidencias exactas y solo se aplican si tienen valor.
    if ($nombrePaciente !== '' && $codigoVehiculo !== '') {
        $consultaSQL .= ' WHERE traslado.nombrePaciente = ?' .
            ' AND traslado.idVehiculo IN (SELECT idVehiculo FROM vehiculo WHERE codigo = ?)';
        $valores = [$nombrePaciente, $codigoVehiculo];
    } elseif ($nombrePaciente !== '') {
        $consultaSQL .= ' WHERE traslado.nombrePaciente = ?';
        $valores = [$nombrePaciente];
    } elseif ($codigoVehiculo !== '') {
        $consultaSQL .= ' WHERE traslado.idVehiculo IN (SELECT idVehiculo FROM vehiculo WHERE codigo = ?)';
        $valores = [$codigoVehiculo];
    }

    $consultaSQL .= ' ORDER BY traslado.horaSolicitud DESC LIMIT 300';
    $consulta = $conexion->prepare($consultaSQL);
    $consulta->execute($valores);
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}
function listarRecursosTraslado(PDO $conexion): array
{
    return [
        'personal' => $conexion->query("SELECT idPersonal,nombre,funcion FROM personal_traslado WHERE activo=1 ORDER BY funcion,nombre")->fetchAll(PDO::FETCH_ASSOC),
        'vehiculos' => $conexion->query("SELECT idVehiculo,codigo,tipo,capacidad FROM vehiculo WHERE activo=1 ORDER BY tipo,codigo")->fetchAll(PDO::FETCH_ASSOC)
    ];
}
function obtenerIdRecurso(array $datos, string $campo): ?int
{
    $valor = trim((string) ($datos[$campo] ?? ''));
    if ($valor === '')
        return null;
    $idRecurso = filter_var($valor, FILTER_VALIDATE_INT);
    if ($idRecurso === false || $idRecurso < 1)
        throw new InvalidArgumentException('La asignación de recursos no es válida.');
    return $idRecurso;
}
function validarPersonalTraslado(PDO $conexion, ?int $idPersonal, string $funcion): void
{
    if ($idPersonal === null)
        return;
    $consulta = $conexion->prepare('SELECT COUNT(*) FROM personal_traslado WHERE idPersonal=? AND funcion=? AND activo=1');
    $consulta->execute([$idPersonal, $funcion]);
    if (!(int) $consulta->fetchColumn())
        throw new InvalidArgumentException('El personal seleccionado no está activo o no tiene la función indicada.');
}
function obtenerHoraEstimadaLlegada(array $datos): ?string
{
    $valor = trim((string) ($datos['horaEstimadaLlegada'] ?? ''));
    if ($valor === '')
        return null;
    $fecha = DateTime::createFromFormat('!Y-m-d\TH:i', $valor);
    if (
        $fecha === false ||
        $fecha->format('Y-m-d\TH:i') !== $valor
    )
        throw new InvalidArgumentException('La hora estimada de llegada no es válida.');
    return $fecha->format('Y-m-d H:i:s');
}
function validarDatosTraslado(PDO $conexion, array $datos): array
{
    $tipoTraslado = (string) ($datos['tipoTraslado'] ?? 'paciente');
    $prioridad = (string) ($datos['prioridad'] ?? 'normal');
    $origen = trim((string) ($datos['origen'] ?? ''));
    $destino = trim((string) ($datos['destino'] ?? ''));
    $ruta = trim((string) ($datos['ruta'] ?? ''));
    $nombre = trim((string) ($datos['nombrePaciente'] ?? ''));
    $elemento = trim((string) ($datos['descripcionElemento'] ?? ''));
    $cedula = trim((string) ($datos['cedulaPaciente'] ?? ''));
    $referencia = trim((string) ($datos['pacienteReferencia'] ?? ''));
    if (
        !in_array($tipoTraslado, ['paciente', 'elemento'], true) ||
        !in_array($prioridad, ['normal', 'urgente', 'critica'], true) ||
        !$origen ||
        !$destino ||
        !$ruta ||
        ($tipoTraslado === 'paciente' && !$nombre) ||
        ($tipoTraslado === 'elemento' && !$elemento) ||
        mb_strlen($cedula, 'UTF-8') > 20 ||
        mb_strlen($nombre, 'UTF-8') > 120 ||
        mb_strlen($referencia, 'UTF-8') > 120 ||
        mb_strlen($elemento, 'UTF-8') > 150 ||
        mb_strlen($origen, 'UTF-8') > 255 ||
        mb_strlen($destino, 'UTF-8') > 255 ||
        mb_strlen($ruta, 'UTF-8') > 500
    )
        throw new InvalidArgumentException('Completá los datos obligatorios.');
    $idChofer = obtenerIdRecurso($datos, 'idChofer');
    $idEnfermero = obtenerIdRecurso($datos, 'idEnfermero');
    $idVehiculo = obtenerIdRecurso($datos, 'idVehiculo');
    validarPersonalTraslado($conexion, $idChofer, 'chofer');
    validarPersonalTraslado($conexion, $idEnfermero, 'enfermero');
    if ($idEnfermero !== null && $tipoTraslado !== 'paciente')
        throw new InvalidArgumentException('Solo se puede asignar un enfermero a un traslado de paciente.');
    if ($idVehiculo !== null) {
        $consulta = $conexion->prepare('SELECT tipo FROM vehiculo WHERE idVehiculo=? AND activo=1');
        $consulta->execute([$idVehiculo]);
        $tipoVehiculo = $consulta->fetchColumn();
        if ($tipoVehiculo === false)
            throw new InvalidArgumentException('El vehículo seleccionado no está activo o no existe.');
        if ($tipoTraslado === 'paciente' && $tipoVehiculo === 'utilitario')
            throw new InvalidArgumentException('No se puede asignar un utilitario a un traslado de paciente.');
    }
    return [
        'tipo' => $tipoTraslado,
        'prioridad' => $prioridad,
        'origen' => $origen,
        'destino' => $destino,
        'ruta' => $ruta,
        'nombre' => $tipoTraslado === 'paciente' ? $nombre : null,
        'cedula' => $tipoTraslado === 'paciente' ? ($cedula ?: null) : null,
        'zonaRoja' => $tipoTraslado === 'paciente' && !empty($datos['zonaRoja']) ? 1 : 0,
        'referencia' => $tipoTraslado === 'paciente' ? ($referencia ?: null) : null,
        'elemento' => $tipoTraslado === 'elemento' ? $elemento : null,
        'observaciones' => trim((string) ($datos['observaciones'] ?? '')) ?: null,
        'idChofer' => $idChofer,
        'idEnfermero' => $idEnfermero,
        'idVehiculo' => $idVehiculo,
        'horaEstimadaLlegada' => obtenerHoraEstimadaLlegada($datos)
    ];
}
function crearTraslado(PDO $conexion, array $datosTraslado, int $idUsuario): int
{
    $traslado = validarDatosTraslado($conexion, $datosTraslado);
    $consulta = $conexion->prepare(
        'INSERT INTO traslado(cedulaPaciente,nombrePaciente,zonaRoja,pacienteReferencia,tipoTraslado,descripcionElemento,prioridad,origen,destino,ruta,observaciones,idSolicitante,idChofer,idEnfermero,idVehiculo,horaEstimadaLlegada) ' .
        'VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    $consulta->execute([
        $traslado['cedula'],
        $traslado['nombre'],
        $traslado['zonaRoja'],
        $traslado['referencia'],
        $traslado['tipo'],
        $traslado['elemento'],
        $traslado['prioridad'],
        $traslado['origen'],
        $traslado['destino'],
        $traslado['ruta'],
        $traslado['observaciones'],
        $idUsuario,
        $traslado['idChofer'],
        $traslado['idEnfermero'],
        $traslado['idVehiculo'],
        $traslado['horaEstimadaLlegada']
    ]);
    return (int) $conexion->lastInsertId();
}
function editarTraslado(PDO $conexion, array $datos): void
{
    $idTraslado = filter_var($datos['idTraslado'] ?? null, FILTER_VALIDATE_INT);
    if (!$idTraslado || $idTraslado < 1)
        throw new InvalidArgumentException('El traslado seleccionado no es válido.');

    $consulta = $conexion->prepare('SELECT estado FROM traslado WHERE idTraslado = ?');
    $consulta->execute([$idTraslado]);
    $estadoActual = $consulta->fetchColumn();
    if ($estadoActual === false)
        throw new RuntimeException('No existe el traslado.');
    if (!in_array($estadoActual, ['solicitado', 'programado'], true))
        throw new InvalidArgumentException('Solo se pueden editar traslados solicitados o programados.');

    $traslado = validarDatosTraslado($conexion, $datos);
    $consulta = $conexion->prepare(
        'UPDATE traslado SET cedulaPaciente = ?, nombrePaciente = ?, zonaRoja = ?, ' .
        'pacienteReferencia = ?, tipoTraslado = ?, descripcionElemento = ?, prioridad = ?, ' .
        'origen = ?, destino = ?, ruta = ?, observaciones = ?, idChofer = ?, idEnfermero = ?, ' .
        'idVehiculo = ?, horaEstimadaLlegada = ? WHERE idTraslado = ?'
    );
    $consulta->execute([
        $traslado['cedula'],
        $traslado['nombre'],
        $traslado['zonaRoja'],
        $traslado['referencia'],
        $traslado['tipo'],
        $traslado['elemento'],
        $traslado['prioridad'],
        $traslado['origen'],
        $traslado['destino'],
        $traslado['ruta'],
        $traslado['observaciones'],
        $traslado['idChofer'],
        $traslado['idEnfermero'],
        $traslado['idVehiculo'],
        $traslado['horaEstimadaLlegada'],
        $idTraslado
    ]);
}
function actualizarEstadoTraslado(PDO $conexion, array $datosTraslado): void
{
    $idTraslado = filter_var($datosTraslado['idTraslado'] ?? null, FILTER_VALIDATE_INT);
    $nuevoEstado = (string) ($datosTraslado['estado'] ?? '');
    $estadosPermitidos = ['programado', 'en_curso', 'demorado', 'completado', 'cancelado'];
    if (!$idTraslado || !in_array($nuevoEstado, $estadosPermitidos, true))
        throw new InvalidArgumentException('Estado o traslado inválido.');
    $consulta = $conexion->prepare('SELECT estado FROM traslado WHERE idTraslado=?');
    $consulta->execute([$idTraslado]);
    $estadoActual = $consulta->fetchColumn();
    if ($estadoActual === false)
        throw new RuntimeException('No existe el traslado.');
    // Esta lista define qué cambios de estado permite el sistema.
    $transicionesPermitidas = [
        'solicitado' => ['programado', 'en_curso', 'cancelado'],
        'programado' => ['en_curso', 'cancelado'],
        'en_curso' => ['demorado', 'completado', 'cancelado'],
        'demorado' => ['en_curso', 'completado', 'cancelado'],
        'completado' => [],
        'cancelado' => []
    ];
    if (!in_array($nuevoEstado, $transicionesPermitidas[$estadoActual] ?? [], true))
        throw new InvalidArgumentException('No se permite pasar de ' . str_replace('_', ' ', $estadoActual) . ' a ' . str_replace('_', ' ', $nuevoEstado) . '.');
    // Se registra la salida al iniciar y la llegada al completar el traslado.
    $horaSalida = null;
    $horaLlegada = null;
    if ($nuevoEstado === 'en_curso' && in_array($estadoActual, ['solicitado', 'programado'], true))
        $horaSalida = date('Y-m-d H:i:s');
    if ($nuevoEstado === 'completado')
        $horaLlegada = date('Y-m-d H:i:s');
    $consulta = $conexion->prepare('UPDATE traslado SET estado=?, horaSalida=COALESCE(?,horaSalida), horaLlegada=COALESCE(?,horaLlegada) WHERE idTraslado=?');
    $consulta->execute([$nuevoEstado, $horaSalida, $horaLlegada, $idTraslado]);
}
