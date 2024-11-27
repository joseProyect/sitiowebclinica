<?php

require_once '../conexion/conexion.php';

if (!isset($_GET['id'])) {
    header('Location: adminCitas.php?error=No se especificó una cita.');
    exit();
}

$citaId = $_GET['id'];
$cita = [];
$error = "";

try {
    $conexion = Conexion::getInstancia()->getConexion();
    $sql = "SELECT 
                Citas.*, 
                Pacientes.Nombre AS PacienteNombre, 
                Pacientes.Ape_Paterno AS PacienteApellido, 
                Usuarios.Nombre AS DoctorNombre, 
                Usuarios.Ape_Paterno AS DoctorApellido
            FROM 
                Citas
            JOIN 
                Pacientes ON Citas.PacienteId = Pacientes.PacienteId
            JOIN 
                Doctores ON Citas.DoctorId = Doctores.DoctorId
            JOIN 
                Usuarios ON Doctores.UsuarioId = Usuarios.UsuarioId
            WHERE 
                Citas.CitaId = :citaId";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':citaId', $citaId, PDO::PARAM_INT);
    $stmt->execute();
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cita) {
        $error = "No se encontró información para esta cita.";
    }
} catch (PDOException $e) {
    $error = "Error al obtener el detalle de la cita: " . $e->getMessage();
}

// Obtener horarios disponibles (sin citas registradas)
$horariosDisponibles = [];
try {
    $sqlHorarios = "SELECT Hora 
                    FROM IntervalosHorario
                    WHERE Disponible = 1
                      AND Hora NOT IN (
                          SELECT Hora
                          FROM Citas
                          WHERE Fecha = :fecha
                      )";
    $stmtHorarios = $conexion->prepare($sqlHorarios);
    $stmtHorarios->bindParam(':fecha', $cita['Fecha']);
    $stmtHorarios->execute();
    $horariosDisponibles = $stmtHorarios->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $error = "Error al obtener los horarios disponibles: " . $e->getMessage();
}

// Actualizar los datos si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    try {
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $estado = $_POST['estado'];
        $notas = $_POST['notas'];

        $sqlUpdate = "UPDATE Citas SET Fecha = :fecha, Hora = :hora, Estado = :estado, Notas = :notas WHERE CitaId = :citaId";
        $stmtUpdate = $conexion->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':fecha', $fecha);
        $stmtUpdate->bindParam(':hora', $hora);
        $stmtUpdate->bindParam(':estado', $estado);
        $stmtUpdate->bindParam(':notas', $notas);
        $stmtUpdate->bindParam(':citaId', $citaId, PDO::PARAM_INT);
        $stmtUpdate->execute();

        header('Location: adminCitas.php?msg=edit_success');
        exit();
    } catch (PDOException $e) {
        $error = "Error al actualizar la cita: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de la Cita</title>
    <link rel="stylesheet" href="./css/detalleCitas.css">
    <script>
        function habilitarEdicion() {
            document.getElementById("fecha").disabled = false;
            document.getElementById("hora").disabled = false;
            document.getElementById("estado").disabled = false;
            document.getElementById("notas").disabled = false;
            document.getElementById("guardar").style.display = "inline-block";
            document.getElementById("editar").style.display = "none";
        }

        // Función para establecer la restricción de fecha mínima
        function establecerFechaMinima() {
            const hoy = new Date();
            const anio = hoy.getFullYear();
            const mes = String(hoy.getMonth() + 1).padStart(2, '0'); // Mes en formato MM
            const dia = String(hoy.getDate()).padStart(2, '0'); // Día en formato DD
            const fechaMinima = `${anio}-${mes}-${dia}`;
            
            // Configurar la restricción en el campo de fecha
            const campoFecha = document.getElementById("fecha");
            campoFecha.setAttribute("min", fechaMinima);
        }

        // Ejecutar la función al cargar la página
        window.onload = establecerFechaMinima;
    </script>
</head>
<body>
    <div class="container">
        <h1>Detalle de la Cita</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php else: ?>
            <form method="POST" action="">
                <fieldset>
                    <legend>Información de la Cita</legend>
                    <label for="paciente">Paciente:</label>
                    <input type="text" id="paciente" value="<?php echo htmlspecialchars($cita['PacienteNombre'] . " " . $cita['PacienteApellido']); ?>" readonly>

                    <label for="fecha">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($cita['Fecha']); ?>" disabled>

                    <label for="hora">Hora:</label>
                    <select id="hora" name="hora" disabled>
                        <?php foreach ($horariosDisponibles as $hora): ?>
                            <option value="<?php echo htmlspecialchars($hora); ?>" <?php echo ($hora === $cita['Hora']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($hora); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" disabled>
                        <option value="Agendada" <?php echo $cita['Estado'] === 'Agendada' ? 'selected' : ''; ?>>Agendada</option>
                        <option value="Cancelada" <?php echo $cita['Estado'] === 'Cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                        <option value="Completada" <?php echo $cita['Estado'] === 'Completada' ? 'selected' : ''; ?>>Completada</option>
                    </select>

                    <label for="notas">Notas:</label>
                    <textarea id="notas" name="notas" disabled><?php echo htmlspecialchars($cita['Notas']); ?></textarea>
                </fieldset>

                <div class="buttons">
                    <button type="button" id="editar" class="btn-edit" onclick="habilitarEdicion()">Editar</button>
                    <button type="submit" id="guardar" name="guardar" class="btn-save" style="display: none;">Guardar</button>
                    <button type="button" class="btn-back" onclick="window.location.href='adminCitas.php'">Volver</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
