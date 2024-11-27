<?php
include_once "../dao/horarioDAO.php";

// Obtener el ID del doctor
$doctorId = isset($_GET['doctorId']) ? $_GET['doctorId'] : null;

if (!$doctorId) {
    header("Location: adminHorarios.php");
    exit;
}

$horarioDAO = new HorarioDAO();
$horarios = $horarioDAO->obtenerHorariosPorDoctor($doctorId);
$doctor = $horarioDAO->obtenerNombreDoctorPorId($doctorId); // Obtén el nombre del doctor

if (!$doctor) {
    header("Location: adminHorarios.php?msg=doctor_not_found");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios del Doctor <?= htmlspecialchars($doctor['NombreCompleto']) ?></title>
    <link rel="stylesheet" href="./css/verHorarios.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <h1>Horarios del Doctor: <?= htmlspecialchars($doctor['NombreCompleto']) ?></h1>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                </tr>
            </thead>
            <tbody id="horarios-lista">
                <?php if (!empty($horarios)): ?>
                    <?php foreach ($horarios as $horario): ?>
                        <tr>
                            <td><?= htmlspecialchars($horario['Dia']) ?></td>
                            <td><?= htmlspecialchars($horario['Hora_Inicio']) ?></td>
                            <td><?= htmlspecialchars($horario['Hora_Fin']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">No hay horarios disponibles para este doctor.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="adminHorariosA.php" class="volver">Volver</a>
    </div>
</body>
</html>
