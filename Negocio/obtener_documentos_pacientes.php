<?php
require_once __DIR__ . "/../Datos/conexion.php";

$sql = "SELECT d.titulo, d.resumen, d.archivo, c.nombreCategoria
        FROM documento AS d
        INNER JOIN categoria AS c ON c.idCategoria = d.idCategoria
        WHERE d.estado = :estadoVigente
        ORDER BY d.idDocumento DESC";

$consultaDocumentos = $conexion->prepare($sql);
$consultaDocumentos->execute(['estadoVigente' => 'Vigente']);
$documentosVigentes = $consultaDocumentos->fetchAll(PDO::FETCH_ASSOC);

if (!$documentosVigentes) {
    echo '<p class="sin-resultados">No hay indicaciones vigentes disponibles.</p>';
    exit;
}

foreach ($documentosVigentes as $documento) {
    $titulo = htmlspecialchars($documento['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
    $resumen = htmlspecialchars($documento['resumen'] ?? '', ENT_QUOTES, 'UTF-8');
    $area = htmlspecialchars($documento['nombreCategoria'] ?? '', ENT_QUOTES, 'UTF-8');
    $nombreArchivo = basename($documento['archivo'] ?? '');
    $rutaPdf = $nombreArchivo !== ''
        ? '../Documentos/' . rawurlencode($nombreArchivo)
        : '#';
    $rutaPdfSegura = htmlspecialchars($rutaPdf, ENT_QUOTES, 'UTF-8');

    echo '<article class="tarjeta-indicacion" data-area="' . $area . '">
            <h3 class="titulo-indicacion">' . $titulo . '</h3>
            <p class="resumen-indicacion">' . ($resumen !== '' ? $resumen : 'Sin resumen disponible.') . '</p>
            <p class="area-indicacion"><strong>Área:</strong> ' . $area . '</p>
            <a class="enlace-pdf" href="' . $rutaPdfSegura . '" target="_blank" rel="noopener">Ver PDF</a>
          </article>';
}
