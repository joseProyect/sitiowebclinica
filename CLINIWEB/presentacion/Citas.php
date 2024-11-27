<?php
require_once '../conexion/conexion.php';

$conexion = Conexion::getInstancia()->getConexion();

// Obtener el DoctorId desde la sesión
session_start();
if (!isset($_SESSION['DoctorId'])) {
    echo json_encode(['error' => 'No autenticado']); // No redirigir, enviar error como JSON
    exit();
}

// Variables inicializadas para mantener los datos del formulario
$fechaSeleccionada = $_POST['fecha'] ?? $_GET['fecha'] ?? date('Y-m-d');
$horaSeleccionada = $_POST['hora'] ?? null;
$pacienteSeleccionado = $_POST['pacienteId'] ?? null;
$especialidadSeleccionada = $_POST['especialidadId'] ?? $_GET['especialidadId'] ?? null;
$doctorSeleccionado = $_POST['doctorId'] ?? null;
$notasIngresadas = $_POST['notas'] ?? null;

// Obtener especialidades para el filtro
$sqlEspecialidades = "SELECT * FROM especialidades";
$especialidades = $conexion->query($sqlEspecialidades)->fetchAll(PDO::FETCH_ASSOC);

// Obtener pacientes para el buscador
$pacientes = $conexion->query("SELECT PacienteId, CONCAT(Nombre, ' ', Ape_Paterno, ' ', Ape_Materno) AS NombreCompleto FROM Pacientes")->fetchAll(PDO::FETCH_ASSOC);

// Obtener los doctores según la especialidad seleccionada
if ($especialidadSeleccionada) {
    // Filtrar los doctores por especialidad
    $sqlDoctores = "SELECT Doctores.DoctorId, CONCAT(Usuarios.Nombre, ' ', Usuarios.Ape_Paterno) AS NombreCompleto
                    FROM Doctores
                    JOIN Usuarios ON Doctores.UsuarioId = Usuarios.UsuarioId
                    WHERE Doctores.EspecialidadId = :especialidadId";
    $stmtDoctores = $conexion->prepare($sqlDoctores);
    $stmtDoctores->bindParam(':especialidadId', $especialidadSeleccionada);
    $stmtDoctores->execute();
    $doctores = $stmtDoctores->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Obtener todos los doctores si no se seleccionó especialidad
    $sqlDoctores = "SELECT Doctores.DoctorId, CONCAT(Usuarios.Nombre, ' ', Usuarios.Ape_Paterno) AS NombreCompleto
                    FROM Doctores
                    JOIN Usuarios ON Doctores.UsuarioId = Usuarios.UsuarioId";
    $doctores = $conexion->query($sqlDoctores)->fetchAll(PDO::FETCH_ASSOC);
}

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $pacienteId = $_POST['pacienteId'];
    $doctorId = $_POST['doctorId'];
    $notas = $_POST['notas'];

    // Validar que la fecha sea hoy o en el futuro
    $fechaActual = date('Y-m-d');
    if ($fecha < $fechaActual) {
        header("Location: Citas.php?error=fecha_pasada");
        exit();
    }

    // Verificar si la hora ya está ocupada para esa fecha y doctor
    $sqlVerificar = "SELECT COUNT(*) FROM Citas WHERE Fecha = :fecha AND Hora = :hora AND DoctorId = :doctorId";
    $stmtVerificar = $conexion->prepare($sqlVerificar);
    $stmtVerificar->bindParam(':fecha', $fecha);
    $stmtVerificar->bindParam(':hora', $hora);
    $stmtVerificar->bindParam(':doctorId', $doctorId);
    $stmtVerificar->execute();
    $horaOcupada = $stmtVerificar->fetchColumn();

    if ($horaOcupada > 0) {
        header("Location: Citas.php?error=hora_ocupada");
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

    // Responder con éxito
    header("Location: Citas.php?success=1");
    exit();
}

// Obtener las horas ocupadas para la fecha seleccionada y el doctor
$sqlHorasOcupadas = "SELECT Hora FROM Citas WHERE Fecha = :fecha AND DoctorId = :doctorId";
$stmtHorasOcupadas = $conexion->prepare($sqlHorasOcupadas);
$stmtHorasOcupadas->bindParam(':fecha', $fechaSeleccionada);
$stmtHorasOcupadas->bindParam(':doctorId', $doctorSeleccionado);
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
    <link rel="stylesheet" href="./css/Citas.css"> <!-- Estilos personalizados -->
</head>
<body>
    <div class="container">
        <h2>Agregar Cita</h2>

        <!-- Formulario para agregar una cita -->
        <form id="formAgregarCita" method="POST">
            <div class="form-group">
                <label for="fecha">Fecha de la Cita:</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo htmlspecialchars($fechaSeleccionada); ?>" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="hora">Hora de la Cita:</label>
                <select class="form-control" id="hora" name="hora" required>
                    <?php foreach ($intervalos as $horaDisponible): ?>
                        <option value="<?php echo $horaDisponible; ?>" <?php echo ($horaSeleccionada == $horaDisponible) ? 'selected' : ''; ?>><?php echo $horaDisponible; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="pacienteBuscar">Paciente:</label>
                <input type="text" class="form-control" id="pacienteBuscar" name="pacienteBuscar" placeholder="Buscar paciente por nombre" autocomplete="off" onkeyup="buscarPaciente()" required>
                <div id="pacientesList" class="list-group mt-2" style="display: none;"></div>
                <input type="hidden" id="pacienteId" name="pacienteId">
            </div>

            <div class="form-group">
                <label for="especialidadId">Especialidad:</label>
                <select class="form-control" id="especialidadId" name="especialidadId" required>
                    <option value="">Seleccione una especialidad</option>
                    <?php foreach ($especialidades as $especialidad): ?>
                        <option value="<?php echo $especialidad['EspecialidadId']; ?>" <?php echo ($especialidadSeleccionada == $especialidad['EspecialidadId']) ? 'selected' : ''; ?>>
                            <?php echo $especialidad['Nombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="doctorId">Doctor:</label>
                <select class="form-control" id="doctorId" name="doctorId" required>
                    <option value="">Seleccione un doctor</option>
                    <?php foreach ($doctores as $doctor): ?>
                        <option value="<?php echo $doctor['DoctorId']; ?>" <?php echo ($doctorSeleccionado == $doctor['DoctorId']) ? 'selected' : ''; ?>>
                            <?php echo $doctor['NombreCompleto']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="notas">Notas:</label>
                <textarea class="form-control" id="notas" name="notas"><?php echo htmlspecialchars($notasIngresadas); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Agregar Cita</button>
        </form>

        <!-- Mostrar mensajes de error o éxito -->
        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] == 'fecha_pasada'): ?>
                <div class="alert alert-danger">La fecha de la cita no puede ser anterior a hoy.</div>
            <?php elseif ($_GET['error'] == 'hora_ocupada'): ?>
                <div class="alert alert-danger">La hora seleccionada ya está ocupada para esta fecha.</div>
            <?php endif; ?>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="alert alert-success">Cita agregada con éxito.</div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Función para realizar la búsqueda de pacientes
        function buscarPaciente() {
            var input = $('#pacienteBuscar').val().toLowerCase();
            var pacientes = <?php echo json_encode($pacientes); ?>; // Cargar la lista de pacientes desde PHP

            // Filtrar pacientes que coinciden con el texto ingresado
            var resultados = pacientes.filter(function(paciente) {
                return paciente.NombreCompleto.toLowerCase().includes(input);
            });

            // Mostrar los resultados
            mostrarResultados(resultados);
        }

        // Función para mostrar los pacientes en la lista
        function mostrarResultados(pacientes) {
            var lista = $('#pacientesList');
            lista.empty(); // Limpiar resultados previos

            if (pacientes.length === 0) {
                lista.hide();
                return;
            }

            lista.show();

            pacientes.forEach(function(paciente) {
                var item = $('<a href="#" class="list-group-item list-group-item-action">')
                    .text(paciente.NombreCompleto)
                    .on('click', function() {
                        // Al hacer clic, seleccionar al paciente y actualizar el input y el campo oculto
                        $('#pacienteBuscar').val(paciente.NombreCompleto);
                        $('#pacienteId').val(paciente.PacienteId);
                        lista.hide();
                    });
                lista.append(item);
            });
        }
    </script>
</body>
</html>
