<?php
require_once '../conexion/conexion.php';

$conexion = Conexion::getInstancia()->getConexion();

// Inicializar variables para los mensajes
$error = '';
$success = '';

// Obtener el ID de la cita desde la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $error = 'No se especificó el ID de la cita.';
}

$citaId = $_GET['id'] ?? null;

if ($citaId) {
    // Obtener los datos de la cita
    $sqlCita = "SELECT * FROM Citas WHERE CitaId = :citaId";
    $stmtCita = $conexion->prepare($sqlCita);
    $stmtCita->bindParam(':citaId', $citaId);
    $stmtCita->execute();
    $cita = $stmtCita->fetch(PDO::FETCH_ASSOC);

    if (!$cita) {
        $error = 'Cita no encontrada.';
    }
}

// Obtener las especialidades
$sqlEspecialidades = "SELECT * FROM especialidades";
$especialidades = $conexion->query($sqlEspecialidades)->fetchAll(PDO::FETCH_ASSOC);

// Obtener pacientes
$sqlPacientes = "SELECT PacienteId, CONCAT(Nombre, ' ', Ape_Paterno, ' ', Ape_Materno) AS NombreCompleto FROM Pacientes";
$pacientes = $conexion->query($sqlPacientes)->fetchAll(PDO::FETCH_ASSOC);

// Obtener doctores
$sqlDoctores = "SELECT Doctores.DoctorId, CONCAT(Usuarios.Nombre, ' ', Usuarios.Ape_Paterno) AS NombreCompleto 
                FROM Doctores 
                JOIN Usuarios ON Doctores.UsuarioId = Usuarios.UsuarioId";
$doctores = $conexion->query($sqlDoctores)->fetchAll(PDO::FETCH_ASSOC);

// Si se envió el formulario, actualizar la cita
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $pacienteId = $_POST['pacienteId'];
    $doctorId = $_POST['doctorId'];
    $notas = $_POST['notas'];

    // Validar que la fecha no sea anterior a hoy
    $fechaActual = date('Y-m-d');
    if ($fecha < $fechaActual) {
        $error = 'La fecha seleccionada no puede ser anterior a hoy.';
    }

    // Validar que la hora esté dentro del rango permitido (08:00–12:00 y 14:00–18:00)
    $horaFormateada = date('H:i', strtotime($hora));
    if (!$error && !($horaFormateada >= '08:00' && $horaFormateada <= '12:00') && !($horaFormateada >= '14:00' && $horaFormateada <= '18:00')) {
        $error = 'La hora seleccionada debe estar entre las 08:00 y las 12:00 o entre las 14:00 y las 18:00.';
    }

    // Verificar si la hora está ocupada
    if (!$error) {
        $sqlVerificar = "SELECT COUNT(*) FROM Citas WHERE Fecha = :fecha AND Hora = :hora AND DoctorId = :doctorId AND CitaId != :citaId";
        $stmtVerificar = $conexion->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':fecha', $fecha);
        $stmtVerificar->bindParam(':hora', $hora);
        $stmtVerificar->bindParam(':doctorId', $doctorId);
        $stmtVerificar->bindParam(':citaId', $citaId);
        $stmtVerificar->execute();
        $horaOcupada = $stmtVerificar->fetchColumn();

        if ($horaOcupada > 0) {
            $error = 'La hora seleccionada ya está ocupada para esta fecha.';
        }
    }

    // Actualizar la cita
    if (!$error) {
        $sqlActualizar = "UPDATE Citas SET Fecha = :fecha, Hora = :hora, PacienteId = :pacienteId, DoctorId = :doctorId, Notas = :notas WHERE CitaId = :citaId";
        $stmtActualizar = $conexion->prepare($sqlActualizar);
        $stmtActualizar->bindParam(':fecha', $fecha);
        $stmtActualizar->bindParam(':hora', $hora);
        $stmtActualizar->bindParam(':pacienteId', $pacienteId);
        $stmtActualizar->bindParam(':doctorId', $doctorId);
        $stmtActualizar->bindParam(':notas', $notas);
        $stmtActualizar->bindParam(':citaId', $citaId);

        if ($stmtActualizar->execute()) {
            $success = 'Cita actualizada con éxito.';
        } else {
            $error = 'Hubo un error al actualizar la cita.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cita</title>
    <link rel="stylesheet" href="./css/Citas.css"> <!-- Estilos personalizados -->
</head>
<body>
    <div class="container">
        <h2>Editar Cita</h2>

        <!-- Mostrar mensajes de error o éxito -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="fecha">Fecha de la Cita:</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo htmlspecialchars($cita['Fecha']); ?>" min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label for="hora">Hora de la Cita:</label>
                <input type="time" class="form-control" id="hora" name="hora" value="<?php echo htmlspecialchars($cita['Hora']); ?>" required>
            </div>

            <div class="form-group">
                <label for="pacienteId">Paciente:</label>
                <select class="form-control" id="pacienteId" name="pacienteId" required>
                    <?php foreach ($pacientes as $paciente): ?>
                        <option value="<?php echo $paciente['PacienteId']; ?>" <?php echo ($cita['PacienteId'] == $paciente['PacienteId']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($paciente['NombreCompleto']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="doctorId">Doctor:</label>
                <select class="form-control" id="doctorId" name="doctorId" required>
                    <?php foreach ($doctores as $doctor): ?>
                        <option value="<?php echo $doctor['DoctorId']; ?>" <?php echo ($cita['DoctorId'] == $doctor['DoctorId']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($doctor['NombreCompleto']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="notas">Notas:</label>
                <textarea class="form-control" id="notas" name="notas"><?php echo htmlspecialchars($cita['Notas']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
