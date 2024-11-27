<?php
include_once "../dao/horarioDAO.php";

// Obtener el ID del horario a editar
$horarioId = isset($_GET['horarioId']) ? $_GET['horarioId'] : null;

if (!$horarioId) {
    header("Location: verHorarios.php?msg=error");
    exit;
}

$horarioDAO = new HorarioDAO();
$horario = $horarioDAO->obtenerHorarioPorId($horarioId);

if (!$horario) {
    header("Location: verHorarios.php?msg=not_found");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dia = $_POST['dia'];
    $horaInicio = $_POST['horaInicio'];
    $horaFin = $_POST['horaFin'];

    try {
        $horarioDAO->actualizarHorario($horarioId, $horario['DoctorId'], $dia, $horaInicio, $horaFin);
        header("Location: verHorarios.php?doctorId=" . $horario['DoctorId'] . "&msg=updated");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Horario</title>
    <link rel="stylesheet" href="./css/editarHorario.css">
</head>
<body>
    <h1>Editar Horario</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;">Error: <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="dia">Día:</label>
        <select name="dia" id="dia" required>
            <?php foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dia): ?>
                <option value="<?= $dia ?>" <?= $horario['Dia'] === $dia ? 'selected' : '' ?>><?= $dia ?></option>
            <?php endforeach; ?>
        </select>

        <label for="horaInicio">Hora Inicio:</label>
        <input type="time" id="horaInicio" name="horaInicio" value="<?= htmlspecialchars($horario['Hora_Inicio']) ?>" required>

        <label for="horaFin">Hora Fin:</label>
        <input type="time" id="horaFin" name="horaFin" value="<?= htmlspecialchars($horario['Hora_Fin']) ?>" required>

        <button type="submit">Guardar Cambios</button>
        <a href="verHorarios.php?doctorId=<?= $horario['DoctorId'] ?>">Cancelar</a>
    </form>
</body>
</html>
