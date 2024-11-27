<?php
require_once '../conexion/Conexion.php'; // Incluye la conexión centralizada

$doctorId = isset($_GET['doctorId']) ? $_GET['doctorId'] : null;

if (!$doctorId) {
    header("Location: adminHorarios.php");
    exit;
}

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = Conexion::getInstancia()->getConexion();

    $diasPermitidos = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    $diasSeleccionados = isset($_POST['dias']) ? $_POST['dias'] : [];
    $horaInicio = $_POST['horaInicio'];
    $horaFin = $_POST['horaFin'];

    try {
        $stmt = $conexion->prepare("INSERT INTO Horarios (DoctorId, Dia, Hora_Inicio, Hora_Fin) 
                                    VALUES (:doctorId, :dia, :horaInicio, :horaFin)");

        foreach ($diasSeleccionados as $dia) {
            if (in_array($dia, $diasPermitidos)) {
                $stmt->execute([
                    ':doctorId' => $doctorId,
                    ':dia' => $dia,
                    ':horaInicio' => $horaInicio,
                    ':horaFin' => $horaFin,
                ]);
            }
        }

        $successMessage = "¡Horario agregado exitosamente!";
    } catch (PDOException $e) {
        die("Error al guardar los horarios: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Horario</title>
    <link rel="stylesheet" href="./css/gregarHorario.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Panel izquierdo -->
        <div class="sidebar">
            <h2>Objetivos</h2>
            <ul>
                <li>Mejorar horarios</li>
                <li>Organizar días</li>
                <li>Establecer metas claras</li>
            </ul>
        </div>

        <!-- Panel principal -->
        <div class="main">
            <h1>Planeador semanal</h1>
            <p>Semana del 15 al 19 de marzo</p>

            <!-- Mensaje de éxito -->
            <?php if ($successMessage): ?>
            <div class="success-message">
                <p><?= htmlspecialchars($successMessage) ?></p>
            </div>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="hidden" name="doctorId" value="<?= htmlspecialchars($doctorId) ?>">

                <div class="form-group">
                    <!-- Contenedor de días -->
                    <div class="days-container">
                        <h3>Selecciona los días:</h3>
                        <?php foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dia): ?>
                            <label class="day-card">
                                <input type="checkbox" name="dias[]" value="<?= htmlspecialchars($dia) ?>">
                                <?= htmlspecialchars($dia) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Contenedor de horas -->
                    <div class="time-container">
                        <div id="clock" class="clock"></div>

                        <label for="horaInicio">Hora Inicio:</label>
                        <input type="text" id="horaInicio" name="horaInicio" required>

                        <label for="horaFin">Hora Fin:</label>
                        <input type="text" id="horaFin" name="horaFin" required>
                    </div>
                </div>

                <div class="buttons">
                    <button type="submit">Guardar</button>
                    <a href="adminHorarios.php" class="btn-volver">Volver</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            flatpickr("#horaInicio", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
            });

            flatpickr("#horaFin", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
            });

            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
            }
            setInterval(updateClock, 1000);
            updateClock();
        });
    </script>
</body>
</html>
