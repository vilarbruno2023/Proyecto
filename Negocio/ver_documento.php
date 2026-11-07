<?php
session_start();
require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . '/Datos/conexion.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Documento inválido.');
}

$consulta = $conexion->prepare('SELECT archivo, estado FROM documento WHERE idDocumento = ?');
$consulta->execute([$id]);
$documento = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$documento) {
    http_response_code(404);
    exit('Documento no encontrado.');
}

// Los pacientes acceden solo a documentos vigentes.
$rolesConAccesoARevision = ['admin', 'doctor', 'documentacion'];
$rol = strtolower((string) ($_SESSION['rol'] ?? ''));
$puedeVerNoVigente = !empty($_SESSION['idUsuario']) &&
    in_array($rol, $rolesConAccesoARevision, true);

if ($documento['estado'] !== 'Vigente' && !$puedeVerNoVigente) {
    http_response_code(403);
    exit('Documento no disponible.');
}

$nombre = basename(str_replace('\\', '/', $documento['archivo']));
if (!is_dir(DIRECTORIO_DOCUMENTOS) || !is_readable(DIRECTORIO_DOCUMENTOS)) {
    error_log('No existe o no se puede leer el directorio de documentos: ' . DIRECTORIO_DOCUMENTOS);
    http_response_code(500);
    exit('El almacenamiento de documentos no está disponible.');
}
$ruta = DIRECTORIO_DOCUMENTOS . '/' . $nombre;
if ($nombre === '' || !is_file($ruta)) {
    http_response_code(404);
    exit('Archivo no encontrado.');
}

if (strtolower(pathinfo($nombre, PATHINFO_EXTENSION)) !== 'pdf') {
    http_response_code(403);
    exit('Solo se permiten archivos PDF.');
}
header('Content-Type: application/pdf');
header('Cache-Control: no-store');
header('Content-Disposition: inline; filename="' . $nombre . '"');
header('X-Content-Type-Options: nosniff');
readfile($ruta);
