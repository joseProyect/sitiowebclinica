<?php
session_start();

// Destruir sesión
session_unset();
session_destroy();

// Evitar que el navegador guarde caché de la página
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirigir al login
header('Location: ../presentacion/login.php');
exit();
?>
