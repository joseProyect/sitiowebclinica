<?php
session_start();

// Evitar almacenamiento en caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['Nombre']) || !isset($_SESSION['Apellidos'])) {
    // Destruir la sesión si existe
    session_unset();
    session_destroy();

    // Redirigir al login
    header('Location: ../presentacion/login.php');
    exit();
}

// Obtener el nombre y apellidos del usuario de la sesión
$nombreUsuario = $_SESSION['Nombre'];
$apellidosUsuario = $_SESSION['Apellidos'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/home.css">
    <title>Menú Sidebar</title>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <header>
            <i class='bx bx-menu toggle'></i>
            <div class="logo">
                <i class='bx bx-user'></i>
                <span class="logo-text">
                    <?php echo htmlspecialchars($nombreUsuario) . ' ' . htmlspecialchars($apellidosUsuario); ?>
                </span>
            </div>
        </header>
        <div class="menu">
            <ul class="menu-links">
                <li class="nav-link">
                    <a href="dashboard-admi.php" target="content-area">
                        <i class='bx bx-bar-chart-alt-2'></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="adminUsuarios.php" target="content-area">
                        <i class='bx bx-line-chart'></i>
                        <span class="text">Ver Usuarios</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="adminPacientes.php" target="content-area">
                        <i class='bx bx-calendar'></i>
                        <span class="text">ver pacientes</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="adminDoctor.php" target="content-area">
                        <i class='bx bx-bolt-circle'></i>
                        <span class="text">ver doctores</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="adminHorarios.php" target="content-area">
                        <i class='bx bx-food-menu'></i>
                        <span class="text">ver horarios</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="admincitas.php" target="content-area">
                        <i class='bx bx-time'></i>
                        <span class="text">ver citas</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="logout">
            <a href="./login.php" id="logout-link">
                <i class='bx bx-log-out'></i>
                <span class="text">Salir</span>
            </a>
        </div>
    </nav>

    <!-- Contenido Principal con iframe -->
    <main class="main-content">
        <iframe id="content-area" name="content-area" src="dashboard-admi.php" frameborder="0" style="width: 100%; height: calc(100vh - 60px);"></iframe>
    </main>

    <script src="./js/home-admis.js"></script>
</body>
</html>
