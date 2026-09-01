<?php
session_start();
require_once __DIR__ . "/../Datos/conexion.php";

$sql = "SELECT d.*, c.nombreCategoria
        FROM documento d
        INNER JOIN categoria c ON d.idCategoria = c.idCategoria
        ORDER BY d.idDocumento DESC";

$documentos = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (empty($documentos)) {
    echo '<tr><td colspan="6">No hay documentos registrados.</td></tr>';
    exit;
}

foreach ($documentos as $doc) {
    $nombreArchivo = $doc['archivo'] ?? '';
    $rutaArchivo = $nombreArchivo !== '' ? '../Documentos/' . htmlspecialchars($nombreArchivo) : '#';

    echo '
    <tr>
        <td>' . htmlspecialchars($doc['titulo']) . '</td>
        <td>' . htmlspecialchars($doc['nombreCategoria']) . '</td>
        <td>' . htmlspecialchars($doc['estado']) . '</td>
        <td>' . htmlspecialchars($doc['resumen']) . '</td>
        <td>' . htmlspecialchars($doc['codigoQR']) . '</td>
        <td>
            <a href="' . $rutaArchivo . '" target="_blank" rel="noopener" download>Ver archivo</a>
        </td>
    </tr>';
}