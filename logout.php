<?php
session_start();
session_destroy();
?>
<script>
    localStorage.removeItem('sigsm_usuario');
    window.location.href = 'inicio.html';
</script>
