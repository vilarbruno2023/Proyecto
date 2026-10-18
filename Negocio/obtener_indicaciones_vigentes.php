<?php
require_once __DIR__ . "/../Datos/conexion.php";
header("Content-Type: text/html; charset=UTF-8");

$sqlIndicacionesVigentes = "SELECT documento.idDocumento, documento.titulo,
                                   documento.resumen, documento.archivo,
                                   categoria.nombreCategoria
                            FROM documento
                            INNER JOIN categoria
                                ON documento.idCategoria = categoria.idCategoria
                            WHERE documento.estado = :estadoVigente
                            ORDER BY documento.idDocumento DESC";

$consultaIndicaciones = $conexion->prepare($sqlIndicacionesVigentes);
$consultaIndicaciones->execute(['estadoVigente' => 'Vigente']);
$indicacionesVigentes = $consultaIndicaciones->fetchAll(PDO::FETCH_ASSOC);

if (!$indicacionesVigentes) {
    echo '<tr><td colspan="4">No hay indicaciones vigentes disponibles.</td></tr>';
    exit;
}

foreach ($indicacionesVigentes as $indicacion) {
    $tituloIndicacion = htmlspecialchars($indicacion['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
    $nombreArea = htmlspecialchars($indicacion['nombreCategoria'] ?? '', ENT_QUOTES, 'UTF-8');
    $resumenIndicacion = htmlspecialchars($indicacion['resumen'] ?? '', ENT_QUOTES, 'UTF-8');
    $nombreArchivo = basename((string)($indicacion['archivo'] ?? ''));
    $rutaPdf = '../Documentos/' . rawurlencode($nombreArchivo);
    $rutaPdfSegura = htmlspecialchars($rutaPdf, ENT_QUOTES, 'UTF-8');

    echo '<tr class="fila-indicacion" data-area="' . $nombreArea . '">
            <td>' . $tituloIndicacion . '</td>
            <td>' . $nombreArea . '</td>
            <td>' . $resumenIndicacion . '</td>
            <td class="acciones-tabla">
                <a href="' . $rutaPdfSegura . '" target="_blank" rel="noopener">Ver PDF</a>
                <a href="' . $rutaPdfSegura . '" download>Descargar PDF</a>
            </td>
          </tr>';
}
