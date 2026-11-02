<?php
session_start();
if (empty($_SESSION['idUsuario']) || strtolower((string) ($_SESSION['rol'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$idDocumento = filter_input(INPUT_POST, 'idDocumento', FILTER_VALIDATE_INT);
if (!$idDocumento || $idDocumento < 1) {
    header('Location: ../Presentacion/pagina.php?pagina=indicaciones&error=1');
    exit;
}

require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . DIRECTORY_SEPARATOR . 'Datos' . DIRECTORY_SEPARATOR . 'conexion.php';
require_once DIRECTORIO_PRIVADO . DIRECTORY_SEPARATOR . 'Datos' . DIRECTORY_SEPARATOR . 'Clases' . DIRECTORY_SEPARATOR . 'Documento.php';

try {
    if (!Documento::eliminar($conexion, $idDocumento, __DIR__ . '/../Documentos')) {
        header('Location: ../Presentacion/pagina.php?pagina=indicaciones&error=1');
        exit;
    }
} catch (Throwable $error) {
    error_log('Error al eliminar indicación: ' . $error->getMessage());
    header('Location: ../Presentacion/pagina.php?pagina=indicaciones&error=1');
    exit;
}

header('Location: ../Presentacion/pagina.php?pagina=indicaciones&eliminada=1');
exit;