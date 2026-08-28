<?php
session_start();
if (empty($_SESSION['usuario'])) {
    header('Location: inicio.html');
    exit;
}
if ($_SESSION['rol'] !== 'administrador') {
    http_response_code(403);
    exit('No tienes permiso para acceder a esta página.');
}
readfile(__DIR__ . '/SIGSM.html');