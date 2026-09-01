<?php
require_once __DIR__ . '/../Datos/conexion.php';

$codigoQR = trim((string)($_GET['codigoQR'] ?? ''));

if ($codigoQR === '') {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'mensaje' => 'No se indicó el documento.']);
    exit;
}

$sql = "SELECT d.*, c.nombreCategoria
        FROM documento d
        INNER JOIN categoria c ON d.idCategoria = c.idCategoria
        WHERE d.codigoQR = :codigoQR
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->execute(['codigoQR' => $codigoQR]);
$documento = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');

if (!$documento) {
    echo json_encode(['ok' => false, 'mensaje' => 'No se encontró el documento solicitado.']);
    exit;
}

$archivo = $documento['archivo'] ?? '';
$pdfUrl = $archivo !== '' ? '../Documentos/' . $archivo : '#';

echo json_encode([
    'ok' => true,
    'titulo' => $documento['titulo'],
    'estado' => $documento['estado'],
    'categoria' => $documento['nombreCategoria'],
    'codigoQR' => $documento['codigoQR'],
    'resumen' => $documento['resumen'] ?? '',
    'pdfUrl' => $pdfUrl,
]);
