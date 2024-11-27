<?php
session_start();

// Evitar caché para asegurar siempre datos actualizados
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <form action="../controladores/validarLogin.php" method="POST">
            <h1>Inicio de Sesión</h1>
            
            <!-- Mostrar mensajes de error -->
            <?php
            if (isset($_SESSION['error'])) {
                echo "<p class='error-message'>" . $_SESSION['error'] . "</p>";
                unset($_SESSION['error']); // Limpiar el mensaje de error después de mostrarlo
            }
            ?>
            
            <label for="codigo">Código:</label>
            <input type="text" id="codigo" name="codigo" required>
            
            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contraseña" name="contraseña" required>
            
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>