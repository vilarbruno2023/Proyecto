<?php
session_start();
require_once "conexion.php";

$sql = "SELECT d.titulo, d.resumen, d.estado, c.nombreCategoria 
        FROM documento d
        INNER JOIN categoria c ON d.idCategoria = c.idCategoria
        ORDER BY d.idDocumento DESC";

$documentos = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (empty($documentos)) {
    echo '<p>No hay indicaciones disponibles en este momento.</p>';
    exit;
}

foreach ($documentos as $doc) {
    echo '
    <div class="cartas">
        <div class="estado">
            <div>' . htmlspecialchars($doc['estado']) . '</div>
        </div>
        <div>' . htmlspecialchars($doc['titulo']) . '</div>
        <p>' . htmlspecialchars($doc['resumen']) . '</p>
        <div>Área: ' . htmlspecialchars($doc['nombreCategoria']) . '</div>
        <a href="#">Ver indicación</a>
    </div>';
}