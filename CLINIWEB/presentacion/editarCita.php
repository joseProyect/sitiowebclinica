<?php
require_once '../conexion/conexion.php';

$conexion = Conexion::getInstancia()->getConexion();

// Obtener el DoctorId desde la sesión
session_start();
if (!isset($_SESSION['DoctorId'])) {
    header('Location: login.php'); // Redirigir si no está autenticado
    exit();
}
$doctorId = $_SESSION['DoctorId'];

// Obtener el ID de la cita a editar
$citaId = $_GET['citaId'] ?? null;

if ($citaId) {
    // Consultar los detalles de la cita
    $sqlCita = "SELECT Citas.Fecha, Citas.Hora, Citas.PacienteId, Citas.Notas 
                FROM Citas 
                WHERE CitaId = :citaId";
    $stmt = $conexion->prepare($sqlCita);
    $stmt->bindParam(':citaId', $citaId);
    $stmt->execute();

    // Verificar si la consulta devuelve datos
    if ($stmt->rowCount() > 0) {
        // Si hay resultados, obtener los datos de la cita
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);
        $fechaSeleccionada = $cita['Fecha'];
        $horaSeleccionada = $cita['Hora'];
        $pacienteSeleccionado = $cita['PacienteId'];
        $notasSeleccionadas = $cita['Notas'];
    } else {
        // Si no se encuentra la cita, redirigir o mostrar error
        header("Location: misCitas.php?error=cita_no_encontrada");
        exit();
    }
}

// Obtener pacientes para el buscador
$pacientes = $conexion->query("SELECT PacienteId, CONCAT(Nombre, ' ', Ape_Paterno, ' ', Ape_Materno) AS NombreCompleto 
                               FROM Pacientes")->fetchAll(PDO::FETCH_ASSOC);

// Obtener el nombre del doctor
$sqlDoctor = "SELECT CONCAT(Usuarios.Nombre, ' ', Usuarios.Ape_Paterno) AS NombreCompleto 
              FROM Doctores 
              JOIN Usuarios ON Doctores.UsuarioId = Usuarios.UsuarioId 
              WHERE DoctorId = :doctorId";
$stmtDoctor = $conexion->prepare($sqlDoctor);
$stmtDoctor->bindParam(':doctorId', $doctorId);
$stmtDoctor->execute();

// Verificar si se obtiene el nombre del doctor
if ($stmtDoctor->rowCount() > 0) {
    $doctorNombre = $stmtDoctor->fetchColumn(); // Obtener el nombre del doctor
} else {
    $doctorNombre = 'Doctor no encontrado'; // Valor por defecto en caso de error
}

// Obtener intervalos horarios disponibles para la fecha seleccionada
$intervalos = $conexion->query("SELECT DISTINCT Hora 
                                FROM Citas 
                                WHERE Fecha = '$fechaSeleccionada' AND DoctorId = '$doctorId'")->fetchAll(PDO::FETCH_COLUMN);

// Procesar formulario cuando se envíe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $pacienteId = $_POST['pacienteId'];
    $notas = $_POST['notas'];

    // Validar que la fecha sea hoy o en el futuro
    $fechaActual = date('Y-m-d');
    if ($fecha < $fechaActual) {
        header("Location: editarCita.php?error=fecha_pasada&citaId=$citaId");
        exit();
    }

    // Validar que la hora esté en el rango permitido
    $horaFormateada = date('H:i', strtotime($hora)); // Convertir a formato de 24 horas para comparación
    if (!($horaFormateada >= '08:00' && $horaFormateada <= '12:00') && !($horaFormateada >= '14:00' && $horaFormateada <= '18:00')) {
        header("Location: editarCita.php?error=hora_invalida&citaId=$citaId");
        exit();
    }

    // Verificar si la hora ya está ocupada para esa fecha
    $sqlVerificar = "SELECT COUNT(*) FROM Citas WHERE Fecha = :fecha AND Hora = :hora AND CitaId != :citaId";
    $stmtVerificar = $conexion->prepare($sqlVerificar);
    $stmtVerificar->bindParam(':fecha', $fecha);
    $stmtVerificar->bindParam(':hora', $hora);
    $stmtVerificar->bindParam(':citaId', $citaId);
    $stmtVerificar->execute();
    $horaOcupada = $stmtVerificar->fetchColumn();

    if ($horaOcupada > 0) {
        header("Location: editarCita.php?error=hora_ocupada&citaId=$citaId");
        exit();
    }

    // Actualizar la cita
    try {
        $sqlUpdate = "UPDATE Citas 
                      SET Fecha = :fecha, Hora = :hora, PacienteId = :pacienteId, Notas = :notas
                      WHERE CitaId = :citaId";
        $stmt = $conexion->prepare($sqlUpdate);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':pacienteId', $pacienteId);
        $stmt->bindParam(':notas', $notas);
        $stmt->bindParam(':citaId', $citaId);
        $stmt->execute();

        header("Location: misCitas.php?success=1");
    } catch (PDOException $e) {
        echo "Error al actualizar cita: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cita</title>
    <link rel="stylesheet" href="./css/modificarCitas.css">
    <script src="./js/buscadorPacientes.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Formulario de Editar Cita</h1>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message" style="color: red; font-size: 14px;">
                <?php
                if ($_GET['error'] === 'fecha_pasada') {
                    echo "La fecha de la cita no puede ser anterior a hoy.";
                } elseif ($_GET['error'] === 'hora_invalida') {
                    echo "La hora seleccionada debe estar entre las 8:00 AM - 12:00 PM o 2:00 PM - 6:00 PM.";
                } elseif ($_GET['error'] === 'hora_ocupada') {
                    echo "La hora seleccionada ya está ocupada para esta fecha.";
                }
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="formEditarCita">
            <fieldset>
                <legend>Información de la Cita</legend>
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($fechaSeleccionada); ?>" required>
            </fieldset>

            <fieldset>
                <legend>Horario</legend>
                <label for="hora">Hora:</label>
                <select id="hora" name="hora" required>
                    <option value="">Seleccione una hora</option>
                    <?php 
                    // Mostrar horas disponibles
                    $horasDisponibles = ['08:00', '09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
                    foreach ($horasDisponibles as $horaDisponible): 
                    ?>
                        <option value="<?php echo $horaDisponible; ?>" <?php echo $horaDisponible === $horaSeleccionada ? 'selected' : ''; ?>>
                            <?php echo $horaDisponible; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </fieldset>

            <fieldset>
                <legend>Datos del Paciente</legend>
                <label for="pacienteId">Buscar Paciente:</label>
                <input type="text" id="buscadorPaciente" placeholder="Escriba el nombre del paciente">
                <select id="pacienteId" name="pacienteId" required>
                    <option value="">Seleccione un paciente</option>
                    <?php foreach ($pacientes as $paciente): ?>
                        <option value="<?php echo $paciente['PacienteId']; ?>" <?php echo $paciente['PacienteId'] == $pacienteSeleccionado ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($paciente['NombreCompleto']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </fieldset>

            <fieldset>
                <legend>Notas</legend>
                <textarea name="notas" id="notas" rows="4"><?php echo htmlspecialchars($notasSeleccionadas); ?></textarea>
            </fieldset>

		<div class="actions">
			<button id="botonActualizarCita" type="submit">Actualizar Cita</button>
			<a id="botonRegresar" href="misCitas.php">Regresar</a>
		</div>

        </form>
    </div>
</body>
</html>
