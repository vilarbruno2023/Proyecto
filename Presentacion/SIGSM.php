<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['idUsuario']) || strtolower((string) ($_SESSION['rol'] ?? '')) !== 'admin') {
    header('Location: inicio.html');
    exit;
}
?>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Proyecto SIGSM</title>
</head>

<body>
    <header>
        <div class="hizq">
            <div>
                <h3>S.I.G.S.M.</h3>
            </div>
        </div>
    </header>
    <nav class="controles-admin">
        <a class="boton-enlace" href="administrar_usuarios.php">Usuarios y credenciales</a>
        <a class="boton-enlace" href="indicaciones.php">Indicaciones y categorías</a>
        <a class="boton-enlace" href="administrar_encuestas.php">Encuestas</a>
        <a class="boton-enlace" href="administrar_flota.php">Personal y vehículos</a>
        <a class="boton-enlace" href="traslados.php">Traslados</a>
        <a class="boton-enlace" href="../Negocio/logout.php">Cerrar sesión</a>
    </nav>

    <main class="pagina-contenido">
        <section class="panel">
            <h1>Panel de administración</h1>
            <p>Desde este panel podés administrar usuarios y credenciales, categorías de indicaciones, encuestas, personal operativo, vehículos y traslados.</p>
            <p>Las indicaciones se crean desde «Indicaciones y categorías» con una cuenta del área Documentación y solo un doctor puede dejarlas vigentes.</p>
        </section>
    </main>
    <script src="cerrar_sesion.js"></script>
</body>

</html>