<?php
class Documento
{
    public static function eliminar(PDO $conexion, int $idDocumento, string $directorioDocumentos): bool
    {
        $consulta = $conexion->prepare('SELECT archivo FROM documento WHERE idDocumento = ?');
        $consulta->execute([$idDocumento]);
        $documento = $consulta->fetch(PDO::FETCH_ASSOC);

        if (!$documento) {
            return false;
        }

        $nombreArchivo = basename(str_replace('\\', '/', (string) $documento['archivo']));
        if ($nombreArchivo === '' || $nombreArchivo === '.' || $nombreArchivo === '..') {
            throw new RuntimeException('El nombre del archivo asociado no es válido.');
        }

        $directorioReal = realpath($directorioDocumentos);
        if ($directorioReal === false || !is_dir($directorioReal)) {
            throw new RuntimeException('No se encontró la carpeta de documentos.');
        }

        $rutaArchivo = $directorioReal . DIRECTORY_SEPARATOR . $nombreArchivo;

        $conexion->beginTransaction();
        try {
            $consulta = $conexion->prepare('DELETE FROM documento WHERE idDocumento = ?');
            $consulta->execute([$idDocumento]);

            if (is_file($rutaArchivo) && !unlink($rutaArchivo)) {
                throw new RuntimeException('No se pudo borrar el archivo PDF.');
            }

            $conexion->commit();
        } catch (Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $error;
        }

        return true;
    }
}
