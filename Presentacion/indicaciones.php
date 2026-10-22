<?php
session_start();
$rol = strtolower((string) ($_SESSION['rol'] ?? ''));
if (empty($_SESSION['idUsuario']) || !in_array($rol, ['admin', 'documentacion', 'doctor'], true)) {
    header('Location: inicio.html');
    exit;
}
require_once __DIR__ . '/../Datos/conexion.php';
function h($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
$categorias = $conexion->query('SELECT idCategoria,nombreCategoria FROM categoria ORDER BY nombreCategoria')->fetchAll(PDO::FETCH_ASSOC);
$indicaciones = $conexion->query('SELECT d.idDocumento,d.titulo,d.resumen,d.archivo,d.estado,d.idCategoria,c.nombreCategoria,f.nombreUsuario AS creador FROM documento d JOIN categoria c ON c.idCategoria=d.idCategoria JOIN funcionario f ON f.idUsuario=d.idUsuario ORDER BY d.idDocumento DESC')->fetchAll(PDO::FETCH_ASSOC);
$puedeCrear = $rol === 'documentacion';
$puedeAprobar = $rol === 'doctor';
?><!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="estilos.css">
    <title>Indicaciones | SIGSM</title>
</head>

<body class="pagina-admin">
    <header>
        <div class="hizq">
            <h3>S.I.G.S.M. | Indicaciones</h3>
        </div>
        <div class="hder"><a class="boton-enlace" href="<?= $rol === 'admin' ? 'SIGSM.php' : 'index.html' ?>">Volver</a><a
                class="boton-enlace" href="../Presentacion/index.html" onclick="return confirm('¿Estás seguro de que quieres cerrar sesión?');">Cerrar sesión</a></div>
    </header>
    <main class="pagina-contenido">
        <?php if (isset($_GET['ok'])): ?>
            <p class="aviso aviso-ok">Indicación enviada a revisión.</p><?php endif; ?>
        <?php if (isset($_GET['estado_ok'])): ?>
            <p class="aviso aviso-ok">Estado actualizado.</p><?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p class="aviso aviso-error">No se pudo completar la operación. Revisá los datos y los permisos.</p>
        <?php endif; ?>
        <?php if ($puedeCrear): ?>
            <section class="panel">
                <h1>Nueva indicación</h1>
                <p>Las indicaciones se guardan como <strong>Revisar</strong> y no se muestran al público hasta que un doctor
                    las apruebe.</p>
                <form class="formulario-grid" action="../Negocio/guardar_documento.php" method="post"
                    enctype="multipart/form-data">
                    <label>Título<input name="titulo" maxlength="100" required></label><label>Categoría<select
                            name="idCategoria" required>
                            <option value="">Seleccioná una categoría</option><?php foreach ($categorias as $c): ?>
                                <option value="<?= h($c['idCategoria']) ?>"><?= h($c['nombreCategoria']) ?></option>
                            <?php endforeach; ?>
                        </select></label>
                    <label class="ancho-completo">Resumen para el público<textarea name="resumen" maxlength="4000"
                            rows="3"></textarea></label><label class="ancho-completo">Archivo PDF<input type="file"
                            name="archivo" accept="application/pdf,.pdf" required></label><button type="submit">Enviar a
                        revisión</button>
                </form>
            </section><?php endif; ?>
        <?php if ($rol === 'admin'): ?>
            <section class="panel">
                <h2>Administrar categorías</h2>
                <p>Las categorías se guardan en la base de datos; no es necesario modificar el código para añadir una.</p>
                <form class="formulario-inline" action="../Negocio/guardar_categoria.php" method="post"><input
                        name="nombreCategoria" maxlength="50" placeholder="Nombre de la nueva categoría" required><button
                        type="submit">Agregar categoría</button></form><?php if (isset($_GET['cat_ok'])): ?>
                    <p class="aviso aviso-ok">Categoría agregada.</p><?php elseif (isset($_GET['cat_error'])): ?>
                    <p class="aviso aviso-error">No se pudo agregar la categoría; puede que ya exista.</p><?php endif; ?>
            </section><?php endif; ?>
        <section class="panel">
            <h2>Indicaciones registradas</h2>
            <p>Solo las indicaciones en estado «Vigente» se publican en el índice.</p>
            <div class="tabla-desplazable">
                <table id="tablaIndicacionesAdmin">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Resumen</th>
                            <th>Archivo</th><?php if ($puedeAprobar): ?>
                                <th>Revisión médica</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($indicaciones as $d): ?>
                            <tr>
                                <td><?= h($d['titulo']) ?></td>
                                <td><?= h($d['nombreCategoria']) ?></td>
                                <td><span class="estado estado-<?= strtolower(h($d['estado'])) ?>"><?= h($d['estado']) ?></span>
                                </td>
                                <td><?= h($d['resumen']) ?></td>
                                <td><a href="../Documentos/<?= rawurlencode(basename($d['archivo'])) ?>" target="_blank"
                                        rel="noopener">Ver PDF</a></td><?php if ($puedeAprobar): ?>
                                    <td>
                                        <form class="formulario-inline" method="post"
                                            action="../Negocio/actualizar_estado_documento.php"><input type="hidden"
                                                name="idDocumento" value="<?= h($d['idDocumento']) ?>"><select name="estado"
                                                aria-label="Nuevo estado">
                                                <option value="Revisar" <?= $d['estado'] === 'Revisar' ? 'selected' : '' ?>>Revisar
                                                </option>
                                                <option value="Vigente" <?= $d['estado'] === 'Vigente' ? 'selected' : '' ?>>Vigente
                                                </option>
                                                <option value="Rechazada" <?= $d['estado'] === 'Rechazada' ? 'selected' : '' ?>>
                                                    Rechazada</option>
                                            </select><button type="submit">Guardar</button></form>
                                    </td><?php endif; ?>
                            </tr><?php endforeach; ?>
                        <?php if (!$indicaciones): ?>
                            <tr>
                                <td colspan="<?= $puedeAprobar ? 6 : 5 ?>">No hay indicaciones registradas.</td>
                            </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
    <script src="cerrar_sesion.js"></script>

</html>