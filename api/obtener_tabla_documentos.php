<?php
session_start();
require_once "conexion.php";

$sql = "SELECT *
        FROM documento d
        INNER JOIN categoria c ON d.idCategoria = c.idCategoria
        ORDER BY d.idDocumento DESC";

$documentos = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (empty($documentos)) {
    echo '<tr><td colspan="6">No hay documentos registrados.</td></tr>';
    exit;
}

foreach ($documentos as $doc) {
    echo '
    <tr>
        <td>' . htmlspecialchars($doc['titulo']) . '</td>
        <td>' . htmlspecialchars($doc['nombreCategoria']) . '</td>
        <td>' . htmlspecialchars($doc['estado']) . '</td>
        <td>' . htmlspecialchars($doc['resumen']) . '</td>
        <td>' . htmlspecialchars($doc['codigoQR']) . '</td>
        <td>
            <form action="../api/eliminar_documento.php" method="POST" style="display:inline;">
                <input type="hidden" name="idDocumento" value="' . $doc['idDocumento'] . '">
                <button type="submit" onclick="return confirm(\'¿Seguro de eliminar esta indicación?\');">Eliminar</button>
            </form>
        </td>
    </tr>';
}