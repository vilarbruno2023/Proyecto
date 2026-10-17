<?php
session_start();
require_once __DIR__ . "/../Datos/conexion.php";

$sql = "SELECT d.idDocumento, d.titulo, d.resumen, d.estado, d.archivo, c.nombreCategoria 
        FROM documento d
        INNER JOIN categoria c ON d.idCategoria = c.idCategoria
        ORDER BY d.idDocumento DESC";

$documentos = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (empty($documentos)) {
    echo '<p>No hay indicaciones disponibles en este momento.</p>';
    exit;
}

foreach ($documentos as $doc) {
    $archivo = $doc['archivo'] ?? '';
    $rutaArchivo = $archivo !== '' ? '../Documentos/' . $archivo : '#';

    echo '
    <div class="cartas">
        <div class="estado">
            <div>' . htmlspecialchars($doc['estado']) . '</div>
        </div>
        <div>' . htmlspecialchars($doc['titulo']) . '</div>
        <p>' . htmlspecialchars($doc['resumen']) . '</p>
        <div>Área: ' . htmlspecialchars($doc['nombreCategoria']) . '</div>
        <a href="' . htmlspecialchars($rutaArchivo) . '" target="_blank" rel="noopener">Ver PDF</a>
    </div>';
}