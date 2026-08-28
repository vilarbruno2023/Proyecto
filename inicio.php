<?php
session_start();

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo 'Acceso no válido.';
	exit;
}

$usuario = $_POST['nombreUsuario'] ?? '';
$contraseña = $_POST['contraseña'] ?? '';

$conexion = obtenerConexion();
$consulta = $conexion->prepare(
	'SELECT nombreUsuario, contraseña, rol FROM funcionario WHERE nombreUsuario = ?'
);
$consulta->execute([$usuario]);
$funcionario = $consulta->fetch();

if (!$funcionario || $contraseña !== $funcionario['contraseña']) {
	echo 'Usuario o contraseña incorrectos.';
	exit;
}

$_SESSION['usuario'] = $funcionario['nombreUsuario'];
$_SESSION['rol'] = $funcionario['rol'];

$pagina = '../index.html';
if ($funcionario['rol'] === 'administrador') {
	$pagina = '../SIGSM.php';
}
?>
<script>
	localStorage.setItem('sigsm_usuario', <?= json_encode($funcionario['nombreUsuario']) ?>);
	window.location.href = <?= json_encode($pagina) ?>;
</script>

