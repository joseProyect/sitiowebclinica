<?php
include_once "../dao/horarioDAO.php";

// Crear instancia del DAO de horarios
$horarioDAO = new HorarioDAO();
$doctores = $horarioDAO->obtenerDoctores(); // Obtener la lista de doctores
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Horarios</title>
    <link rel="stylesheet" href="./css/adminHorarios.css">
    <!-- Enlace a Font Awesome para los iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <h1>Gestión de Horarios</h1>
    <div class="container">
        <h2>Lista de Doctores</h2>
        <div class="tarjetas">
            <?php if (!empty($doctores)): ?>
                <?php foreach ($doctores as $doctor): ?>
                    <?php 
                    // Verificar si las claves necesarias existen
                    $nombre = isset($doctor['Nombre']) ? $doctor['Nombre'] : 'N/A';
                    $apePaterno = isset($doctor['Ape_Paterno']) ? $doctor['Ape_Paterno'] : 'N/A';
                    $apeMaterno = isset($doctor['Ape_Materno']) ? $doctor['Ape_Materno'] : 'N/A';
                    ?>
                    <div class="tarjeta">
                        <div class="info">
                            <h3><?= htmlspecialchars($nombre . " " . $apePaterno . " " . $apeMaterno) ?></h3>
                        </div>
                        <div class="acciones">
                            <!-- Botón para Ver Horarios -->
                            <a href="verHorarios.php?doctorId=<?= $doctor['DoctorId'] ?>" class="accion">
                                <i class="fa-solid fa-calendar-days"></i> Ver Horarios
                            </a>
                            <!-- Botón para Agregar Horario -->
                            <a href="agregarHorario.php?doctorId=<?= $doctor['DoctorId'] ?>" class="accion">
                                <i class="fa-solid fa-plus"></i> Agregar Horario
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay doctores disponibles.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
