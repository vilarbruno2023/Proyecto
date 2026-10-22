<?php
session_start();
require_once __DIR__ . "/../Datos/conexion.php";

if (empty($_SESSION['idUsuario']) || !in_array(strtolower((string)($_SESSION['rol'] ?? '')), ['admin','doctor','documentacion'], true)) {
    http_response_code(403);
    echo '<tr><td colspan="5">No tenés permisos para consultar este listado.</td></tr>';
    exit;
}

$sqlIndicaciones = "SELECT documento.idDocumento, documento.titulo,
                           documento.estado, documento.resumen, documento.archivo,
                           categoria.nombreCategoria
                    FROM documento
                    INNER JOIN categoria
                        ON documento.idCategoria = categoria.idCategoria
                    ORDER BY documento.idDocumento DESC";
$consultaIndicaciones = $conexion->query($sqlIndicaciones);
$indicaciones = $consultaIndicaciones->fetchAll(PDO::FETCH_ASSOC);

if (!$indicaciones) {
    echo '<tr><td colspan="5">No hay indicaciones registradas.</td></tr>';
    exit;
}

foreach ($indicaciones as $indicacion) {
    $titulo = htmlspecialchars($indicacion['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
    $area = htmlspecialchars($indicacion['nombreCategoria'] ?? '', ENT_QUOTES, 'UTF-8');
    $estado = htmlspecialchars($indicacion['estado'] ?? '', ENT_QUOTES, 'UTF-8');
    $resumen = htmlspecialchars($indicacion['resumen'] ?? '', ENT_QUOTES, 'UTF-8');
    $nombreArchivo = basename((string)($indicacion['archivo'] ?? ''));
    $rutaPdf = '../Documentos/' . rawurlencode($nombreArchivo);
    $rutaPdfSegura = htmlspecialchars($rutaPdf, ENT_QUOTES, 'UTF-8');

    echo '<tr>
            <td>' . $titulo . '</td>
            <td>' . $area . '</td>
            <td>' . $estado . '</td>
            <td>' . $resumen . '</td>
            <td><a href="' . $rutaPdfSegura . '" target="_blank" rel="noopener">Ver PDF</a></td>
          </tr>';
}
