<?php
session_start();
if (empty($_SESSION['idUsuario'])) {
    header('Location: inicio.html');
    exit;
}
header('Location: encuestas.html');
exit;
