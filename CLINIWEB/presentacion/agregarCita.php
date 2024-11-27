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

// Obtener el nombre del doctor
$sqlDoctor = "SELECT CONCAT(Usuarios.Nombre, ' ', Usuarios.Ape_Paterno) AS NombreCompleto
              FROM Doctores
              JOIN Usuarios ON Doctores.UsuarioId = Usuarios.UsuarioId
              WHERE DoctorId = :doctorId";
$stmtDoctor = $conexion->prepare($sqlDoctor);
$stmtDoctor->bindParam(':doctorId', $doctorId);
$stmtDoctor->execute();
$doctorNombre = $stmtDoctor->fetchColumn();

if (!$doctorNombre) {
    // Si no se encuentra el nombre del doctor, redirigir o mostrar error
    echo "No se pudo obtener el nombre del doctor.";
    exit();
}

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $pacienteId = $_POST['pacienteId'];
    $notas = $_POST['notas'];

    // Validar que la fecha sea hoy o en el futuro
    $fechaActual = date('Y-m-d');
    if ($fecha < $fechaActual) {
        header("Location: agregarCita.php?error=fecha_pasada");
        exit();
    }

    // Verificar si la fecha es un domingo
    $diaSeleccionado = date('w', strtotime($fecha)); // 0 = Domingo
    if ($diaSeleccionado == 0) {
        header("Location: agregarCita.php?error=domingo");
        exit();
    }

    // Validar que la hora esté en el rango permitido (8:00 - 12:00 o 14:00 - 18:00)
    $horaFormateada = date('H:i', strtotime($hora)); // Convertir a formato de 24 horas para comparación
    if (!($horaFormateada >= '08:00' && $horaFormateada <= '12:00') && !($horaFormateada >= '14:00' && $horaFormateada <= '18:00')) {
        header("Location: agregarCita.php?error=hora_invalida");
        exit();
    }

    try {
        // Verificar si la hora ya está ocupada para esa fecha y doctor
        $sqlVerificar = "SELECT COUNT(*) FROM Citas WHERE Fecha = :fecha AND Hora = :hora AND DoctorId = :doctorId";
        $stmtVerificar = $conexion->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':fecha', $fecha);
        $stmtVerificar->bindParam(':hora', $hora);
        $stmtVerificar->bindParam(':doctorId', $doctorId);
        $stmtVerificar->execute();
        $horaOcupada = $stmtVerificar->fetchColumn();

        if ($horaOcupada > 0) {
            header("Location: agregarCita.php?error=hora_ocupada");
            exit();
        }

        // Inserción de la cita
        $sql = "INSERT INTO Citas (Fecha, Hora, PacienteId, DoctorId, Estado, Notas) 
                VALUES (:fecha, :hora, :pacienteId, :doctorId, 'Agendada', :notas)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':pacienteId', $pacienteId);
        $stmt->bindParam(':doctorId', $doctorId);
        $stmt->bindParam(':notas', $notas);
        $stmt->execute();

        header("Location: misCitas.php?success=1");
    } catch (PDOException $e) {
        echo "Error al agregar cita: " . $e->getMessage();
    }
}

// Obtener pacientes para el buscador
$pacientes = $conexion->query("
    SELECT PacienteId, CONCAT(Nombre, ' ', Ape_Paterno, ' ', Ape_Materno) AS NombreCompleto 
    FROM Pacientes
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener las horas ocupadas para la fecha seleccionada y el doctor
$fechaSeleccionada = $_GET['fecha'] ?? date('Y-m-d'); // Fecha seleccionada o actual
$sqlHorasOcupadas = "
    SELECT Hora 
    FROM Citas 
    WHERE Fecha = :fecha AND DoctorId = :doctorId
";
$stmtHorasOcupadas = $conexion->prepare($sqlHorasOcupadas);
$stmtHorasOcupadas->bindParam(':fecha', $fechaSeleccionada);
$stmtHorasOcupadas->bindParam(':doctorId', $doctorId);
$stmtHorasOcupadas->execute();
$horasOcupadas = $stmtHorasOcupadas->fetchAll(PDO::FETCH_ASSOC);

// Crear un array con las horas ocupadas
$horasOcupadasArray = array_column($horasOcupadas, 'Hora');

// Definir las horas disponibles (de 8:00 a 12:00 y de 14:00 a 18:00)
$horasDisponibles = [
    '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
    '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30'
];

// Filtrar las horas disponibles, eliminando las ocupadas
$intervalos = array_filter($horasDisponibles, function($hora) use ($horasOcupadasArray) {
    return !in_array($hora, $horasOcupadasArray);
});

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cita</title>
    <link rel="stylesheet" href="./css/docCitas.css">
    <script src="./js/buscadorPacientes.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Formulario de Agregar Cita</h1>
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message" style="color: red; font-size: 14px;">
                <?php
                if ($_GET['error'] === 'domingo') {
                    echo "No se pueden seleccionar los domingos.";
                } elseif ($_GET['error'] === 'fecha_pasada') {
                    echo "La fecha de la cita no puede ser anterior a hoy.";
                } elseif ($_GET['error'] === 'hora_ocupada') {
                    echo "La hora seleccionada ya está ocupada para esta fecha.";
                } elseif ($_GET['error'] === 'hora_invalida') {
                    echo "La hora seleccionada debe estar entre las 8:00 AM - 12:00 PM o 2:00 PM - 6:00 PM.";
                }
                ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="" id="formAgregarCita">
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
                    <?php foreach ($intervalos as $intervalo): ?>
                        <option value="<?php echo $intervalo; ?>">
                            <?php echo htmlspecialchars($intervalo); ?>
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
                        <option value="<?php echo $paciente['PacienteId']; ?>">
                            <?php echo htmlspecialchars($paciente['NombreCompleto']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </fieldset>

            <fieldset>
                <legend>Datos del Doctor</legend>
                <label for="doctor">Doctor:</label>
                <input type="text" id="doctor" value="<?php echo htmlspecialchars($doctorNombre); ?>" readonly>
                <input type="hidden" id="doctorId" name="doctorId" value="<?php echo $doctorId; ?>">
            </fieldset>

            <fieldset>
                <legend>Notas Adicionales</legend>
                <label for="notas">Notas:</label>
                <textarea id="notas" name="notas" placeholder="Agregue cualquier detalle adicional..."></textarea>
            </fieldset>

            <div class="actions">
                <input type="submit" value="Agregar Cita">
                <button type="button" id="btnRegresar">Regresar</button>
            </div>
        </form>
    </div>

	<script>
		document.getElementById('fecha').addEventListener('change', function() {
			const fecha = this.value;
			window.location.href = `agregarCita.php?fecha=${fecha}`;
		});

		// Función para regresar a la página misCitas.php
		document.getElementById('btnRegresar').addEventListener('click', function() {
			window.location.href = 'misCitas.php';
		});
	</script>

</body>
</html>
