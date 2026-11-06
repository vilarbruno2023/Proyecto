<?php
session_start();
require_once __DIR__ . '/../Negocio/configuracion.php';

// Solo se pueden abrir estas vistas y con los roles indicados.
$paginas = [
    'panel' => ['archivo' => 'SIGSM.html', 'roles' => ['admin']],
    'usuarios' => ['archivo' => 'usuarios.html', 'roles' => ['admin']],
    'indicaciones' => ['archivo' => 'indicaciones.html', 'roles' => ['admin', 'doctor', 'documentacion']],
    'encuestas' => ['archivo' => 'encuestas.html', 'roles' => ['admin']],
    'flota' => ['archivo' => 'flota.html', 'roles' => ['admin']],
    'traslados' => ['archivo' => 'traslados.html', 'roles' => ['admin', 'documentacion', 'doctor', 'operador', 'funcionario']],
];

$pagina = (string) ($_GET['pagina'] ?? '');
if (!isset($paginas[$pagina])) {
    http_response_code(404);
    exit('Página no encontrada.');
}

$rol = strtolower((string) ($_SESSION['rol'] ?? ''));
if (empty($_SESSION['idUsuario'])) {
    header('Location: inicio.html');
    exit;
}

if (!in_array($rol, $paginas[$pagina]['roles'], true)) {
    http_response_code(403);
    exit('No tenés permisos para acceder a esta página.');
}

$directorioVistasPrivadas = DIRECTORIO_PRIVADO . '/Presentacion';
$archivo = $directorioVistasPrivadas . '/' . $paginas[$pagina]['archivo'];
if (!is_file($archivo) || !is_readable($archivo)) {
    error_log('No se puede leer la vista privada solicitada: ' . $pagina);
    http_response_code(500);
    exit('No se pudo cargar la página.');
}

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
readfile($archivo);
