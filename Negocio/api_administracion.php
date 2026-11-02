<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (empty($_SESSION['idUsuario'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Iniciá sesión.']);
    exit;
}

$rol = strtolower((string) ($_SESSION['rol'] ?? ''));
$accion = (string) ($_GET['accion'] ?? $_POST['accion'] ?? 'estado');
$soloAdmin = [
    'usuarios',
    'encuestas',
    'flota',
    'crear_usuario',
    'cambiar_clave',
    'crear_personal',
    'crear_vehiculo',
    'estado_personal',
    'estado_vehiculo',
    'crear_categoria',
    'eliminar_indicacion'
];
$rolesIndicaciones = ['admin', 'doctor', 'documentacion'];

if (in_array($accion, $soloAdmin, true) && $rol !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Acceso denegado.']);
    exit;
}

if ($accion === 'indicaciones' && !in_array($rol, $rolesIndicaciones, true)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Acceso denegado.']);
    exit;
}

if ($accion === 'crear_indicacion' && $rol !== 'documentacion') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Solo Documentación puede crear indicaciones.']);
    exit;
}

if ($accion === 'aprobar_indicacion' && $rol !== 'doctor') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Solo un doctor puede revisar indicaciones.']);
    exit;
}

require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . DIRECTORY_SEPARATOR . 'Datos' . DIRECTORY_SEPARATOR . 'conexion.php';
require_once DIRECTORIO_PRIVADO . DIRECTORY_SEPARATOR . 'Datos' . DIRECTORY_SEPARATOR . 'Clases' . DIRECTORY_SEPARATOR . 'Documento.php';

function responderJson($respuesta)
{
    echo json_encode(
        ['ok' => true] + $respuesta,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

function requerirMetodoPost()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new RuntimeException('Método no permitido.');
    }
}

try {
    switch ($accion) {
        case 'estado':
            responderJson([
                'usuario' => $_SESSION['nombre'] ?? '',
                'rol' => $rol
            ]);

        case 'indicaciones':
            $categorias = $conexion
                ->query('SELECT idCategoria, nombreCategoria FROM categoria ORDER BY nombreCategoria')
                ->fetchAll(PDO::FETCH_ASSOC);
            $indicaciones = $conexion
                ->query(
                    'SELECT d.idDocumento, d.titulo, d.resumen, d.archivo, d.estado, d.idCategoria, ' .
                    'c.nombreCategoria, f.nombreUsuario AS creador ' .
                    'FROM documento d ' .
                    'JOIN categoria c ON c.idCategoria = d.idCategoria ' .
                    'JOIN funcionario f ON f.idUsuario = d.idUsuario ' .
                    'ORDER BY d.idDocumento DESC'
                )
                ->fetchAll(PDO::FETCH_ASSOC);

            responderJson([
                'categorias' => $categorias,
                'indicaciones' => $indicaciones,
                'puedeCrear' => $rol === 'documentacion',
                'puedeAprobar' => $rol === 'doctor',
                'puedeCategorias' => $rol === 'admin',
                'puedeEliminar' => $rol === 'admin'
            ]);

        case 'usuarios':
            responderJson([
                'usuarios' => $conexion
                    ->query('SELECT idUsuario, nombreUsuario, rol FROM funcionario ORDER BY nombreUsuario')
                    ->fetchAll(PDO::FETCH_ASSOC)
            ]);

        case 'encuestas':
            responderJson([
                'respuestas' => $conexion
                    ->query(
                        'SELECT fechaEncuesta, tipoEncuesta, segmento, satisfaccion ' .
                        'FROM encuesta_respuesta ORDER BY fechaEncuesta DESC'
                    )
                    ->fetchAll(PDO::FETCH_ASSOC)
            ]);

        case 'flota':
            responderJson([
                'personal' => $conexion
                    ->query('SELECT * FROM personal_traslado ORDER BY funcion, nombre')
                    ->fetchAll(PDO::FETCH_ASSOC),
                'vehiculos' => $conexion
                    ->query('SELECT * FROM vehiculo ORDER BY tipo, codigo')
                    ->fetchAll(PDO::FETCH_ASSOC)
            ]);

        case 'crear_usuario':
            requerirMetodoPost();

            $nombreUsuario = trim((string) ($_POST['nombreUsuario'] ?? ''));
            $contrasenia = (string) ($_POST['contrasenia'] ?? '');
            $rolUsuario = (string) ($_POST['rol'] ?? '');

            if (
                !preg_match('/^[a-zA-Z0-9._-]{3,50}$/', $nombreUsuario) ||
                strlen($contrasenia) < 10 ||
                !in_array($rolUsuario, ['funcionario', 'documentacion', 'doctor', 'operador', 'admin'], true)
            ) {
                throw new InvalidArgumentException(
                    'Revisá usuario, contraseña (10 caracteres mínimo) y rol.'
                );
            }

            $consulta = $conexion->prepare(
                'INSERT INTO funcionario (nombreUsuario, contrasenia, rol) VALUES (?, ?, ?)'
            );
            $consulta->execute([
                $nombreUsuario,
                password_hash($contrasenia, PASSWORD_DEFAULT),
                $rolUsuario
            ]);
            responderJson(['mensaje' => 'Usuario creado.']);

        case 'cambiar_clave':
            requerirMetodoPost();

            $idUsuario = filter_var($_POST['idUsuario'] ?? null, FILTER_VALIDATE_INT);
            $nuevaContrasenia = (string) ($_POST['nuevaContrasenia'] ?? '');

            if (!$idUsuario || strlen($nuevaContrasenia) < 10) {
                throw new InvalidArgumentException('Contraseña inválida.');
            }

            $consulta = $conexion->prepare(
                'UPDATE funcionario SET contrasenia = ? WHERE idUsuario = ?'
            );
            $consulta->execute([
                password_hash($nuevaContrasenia, PASSWORD_DEFAULT),
                $idUsuario
            ]);
            responderJson(['mensaje' => 'Contraseña actualizada.']);

        case 'crear_personal':
            requerirMetodoPost();

            $nombrePersonal = trim((string) ($_POST['nombre'] ?? ''));
            $documentoPersonal = trim((string) ($_POST['documento'] ?? ''));
            $funcionPersonal = (string) ($_POST['funcion'] ?? '');

            if (!$nombrePersonal || !in_array($funcionPersonal, ['doctor', 'chofer', 'enfermero', 'administrativo'], true)) {
                throw new InvalidArgumentException('Datos de personal inválidos.');
            }

            $consulta = $conexion->prepare(
                'INSERT INTO personal_traslado (nombre, documento, funcion) VALUES (?, ?, ?)'
            );
            $consulta->execute([
                $nombrePersonal,
                $documentoPersonal ?: null,
                $funcionPersonal
            ]);
            responderJson(['mensaje' => 'Personal registrado.']);

        case 'crear_vehiculo':
            requerirMetodoPost();

            $codigoVehiculo = trim((string) ($_POST['codigo'] ?? ''));
            $tipoVehiculo = (string) ($_POST['tipo'] ?? '');
            $capacidadVehiculo = filter_var($_POST['capacidad'] ?? null, FILTER_VALIDATE_INT);

            if (
                !$codigoVehiculo ||
                !in_array($tipoVehiculo, ['ambulancia', 'auto', 'utilitario', 'otro'], true) ||
                !$capacidadVehiculo ||
                $capacidadVehiculo < 1 ||
                $capacidadVehiculo > 50
            ) {
                throw new InvalidArgumentException('Datos de vehículo inválidos.');
            }

            $consulta = $conexion->prepare(
                'INSERT INTO vehiculo ' .
                '(codigo, tipo, descripcion, capacidad, requiere_asistencia) ' .
                'VALUES (?, ?, ?, ?, ?)'
            );
            $consulta->execute([
                $codigoVehiculo,
                $tipoVehiculo,
                trim((string) ($_POST['descripcion'] ?? '')) ?: null,
                $capacidadVehiculo,
                isset($_POST['requiere_asistencia']) ? 1 : 0
            ]);
            responderJson(['mensaje' => 'Vehículo registrado.']);

        case 'estado_personal':
        case 'estado_vehiculo':
            requerirMetodoPost();

            $idRegistro = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            $estadoActivo = (int) ($_POST['activo'] ?? -1);

            if (!$idRegistro || !in_array($estadoActivo, [0, 1], true)) {
                throw new InvalidArgumentException('Datos inválidos.');
            }

            if ($accion === 'estado_personal') {
                $consulta = $conexion->prepare(
                    'UPDATE personal_traslado SET activo = ? WHERE idPersonal = ?'
                );
            } else {
                $consulta = $conexion->prepare(
                    'UPDATE vehiculo SET activo = ? WHERE idVehiculo = ?'
                );
            }

            $consulta->execute([$estadoActivo, $idRegistro]);
            responderJson(['mensaje' => 'Estado actualizado.']);

        case 'crear_categoria':
            requerirMetodoPost();

            $nombreCategoria = trim((string) ($_POST['nombreCategoria'] ?? ''));

            if (!$nombreCategoria || mb_strlen($nombreCategoria) > 50) {
                throw new InvalidArgumentException('Nombre de categoría inválido.');
            }

            $consulta = $conexion->prepare('INSERT INTO categoria (nombreCategoria) VALUES (?)');
            $consulta->execute([$nombreCategoria]);
            responderJson(['mensaje' => 'Categoría agregada.']);

        case 'aprobar_indicacion':
            requerirMetodoPost();

            $idDocumento = filter_var($_POST['idDocumento'] ?? null, FILTER_VALIDATE_INT);
            $estadoIndicacion = (string) ($_POST['estado'] ?? '');

            if (!$idDocumento || !in_array($estadoIndicacion, ['Revisar', 'Vigente', 'Rechazada'], true)) {
                throw new InvalidArgumentException('Datos inválidos.');
            }

            $consulta = $conexion->prepare(
                'UPDATE documento SET estado = ? WHERE idDocumento = ?'
            );
            $consulta->execute([$estadoIndicacion, $idDocumento]);
            responderJson(['mensaje' => 'Estado actualizado.']);

        case 'eliminar_indicacion':
            requerirMetodoPost();

            $idDocumento = filter_var($_POST['idDocumento'] ?? null, FILTER_VALIDATE_INT);
            if (!$idDocumento || $idDocumento < 1) {
                throw new InvalidArgumentException('Indicación inválida.');
            }

            $eliminado = Documento::eliminar(
                $conexion,
                $idDocumento,
                __DIR__ . '/../Documentos'
            );

            if (!$eliminado) {
                http_response_code(404);
                echo json_encode(
                    ['ok' => false, 'error' => 'No se encontró la indicación.'],
                    JSON_UNESCAPED_UNICODE
                );
                exit;
            }

            responderJson(['mensaje' => 'Indicación eliminada.']);

        default:
            http_response_code(400);
            throw new InvalidArgumentException('Acción desconocida.');
    }
} catch (InvalidArgumentException $errorValidacion) {
    http_response_code(400);
    echo json_encode(
        ['ok' => false, 'error' => $errorValidacion->getMessage()],
        JSON_UNESCAPED_UNICODE
    );
} catch (PDOException $errorBaseDatos) {
    error_log('Administración SIGSM: ' . $errorBaseDatos->getMessage());
    http_response_code(409);
    echo json_encode(
        [
            'ok' => false,
            'error' => 'No se pudo guardar. Verificá que el dato no esté duplicado y que la base esté actualizada.'
        ],
        JSON_UNESCAPED_UNICODE
    );
} catch (RuntimeException $errorOperacion) {
    error_log('Administración SIGSM: ' . $errorOperacion->getMessage());
    if (http_response_code() < 400) {
        http_response_code(500);
    }
    echo json_encode(
        ['ok' => false, 'error' => $errorOperacion->getMessage()],
        JSON_UNESCAPED_UNICODE
    );
} catch (Throwable $error) {
    error_log('Administración SIGSM: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error interno.'], JSON_UNESCAPED_UNICODE);
}
