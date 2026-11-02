<?php
require_once __DIR__ . '/configuracion.php';
require_once DIRECTORIO_PRIVADO . DIRECTORY_SEPARATOR . 'Datos' . DIRECTORY_SEPARATOR . 'conexion.php';
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
$q = $conexion->query("SELECT d.idDocumento,d.titulo,d.resumen,d.archivo,c.nombreCategoria FROM documento d JOIN categoria c ON c.idCategoria=d.idCategoria WHERE d.estado='Vigente' ORDER BY d.idDocumento DESC");
$indicaciones = $q->fetchAll(PDO::FETCH_ASSOC);

foreach ($indicaciones as $indicacion) {
    $titulo = htmlspecialchars((string) $indicacion['titulo'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $categoria = htmlspecialchars((string) $indicacion['nombreCategoria'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $resumen = htmlspecialchars((string) ($indicacion['resumen'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $archivo = rawurlencode(basename((string) $indicacion['archivo']));

    echo '<tr class="fila-indicacion" data-area="' . $categoria . '">';
    echo '<td>' . $titulo . '</td>';
    echo '<td>' . $categoria . '</td>';
    echo '<td>' . $resumen . '</td>';
    echo '<td><a target="_blank" rel="noopener" href="../Documentos/' . $archivo . '">Ver PDF</a></td>';
    echo '</tr>';
}
