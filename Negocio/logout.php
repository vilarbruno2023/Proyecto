<?php
session_start();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $parametrosCookieSesion = session_get_cookie_params();
    // También se elimina la cookie que identifica la sesión en el navegador.
    setcookie(session_name(), '', time() - 42000, $parametrosCookieSesion['path'], $parametrosCookieSesion['domain'], $parametrosCookieSesion['secure'], $parametrosCookieSesion['httponly']);
}
session_destroy();
header('Location: ../Presentacion/inicio.html');
exit;
