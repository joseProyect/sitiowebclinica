<?php
require_once '../conexion/conexion.php';

// Verificar si el ID del paciente está en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: adminPacientes.php?error=No se especificó un paciente.');
    exit();
}

$id = $_GET['id'];
$paciente = [];
$citas = [];
$error = "";

// Obtener detalles del paciente y sus citas
try {
    $conexion = Conexion::getInstancia()->getConexion();

    // Consultar información del paciente
    $sqlPaciente = "SELECT * FROM Pacientes WHERE PacienteId = :id";
    $stmtPaciente = $conexion->prepare($sqlPaciente);
    $stmtPaciente->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtPaciente->execute();
    $paciente = $stmtPaciente->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        header('Location: adminPacientes.php?error=Paciente no encontrado.');
        exit();
    }

    // Consultar historial de citas del paciente
    $sqlCitas = "SELECT * FROM Citas WHERE PacienteId = :id ORDER BY Fecha, Hora ASC";
    $stmtCitas = $conexion->prepare($sqlCitas);
    $stmtCitas->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCitas->execute();
    $citas = $stmtCitas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error al obtener los datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial del Paciente</title>
    <link rel="stylesheet" href="./css/historialPaciente.css">
</head>
<body>
    <div class="container">
        <h1>Historial del Paciente</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php else: ?>
            <div id="fichaPaciente">
                <!-- Información del paciente -->
                <fieldset>
                    <legend>Información del Paciente</legend>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($paciente['Nombre']); ?></p>
                    <p><strong>Apellido Paterno:</strong> <?php echo htmlspecialchars($paciente['Ape_Paterno']); ?></p>
                    <p><strong>Apellido Materno:</strong> <?php echo htmlspecialchars($paciente['Ape_Materno']); ?></p>
                    <p><strong>DNI:</strong> <?php echo htmlspecialchars($paciente['DNI']); ?></p>
                    <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($paciente['Fecha_Nacimiento']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($paciente['Telefono']); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($paciente['Direccion']); ?></p>
                    <p><strong>Sexo:</strong> <?php echo htmlspecialchars($paciente['Sexo']); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($paciente['Estado']); ?></p>
                </fieldset>

                <!-- Historial de citas -->
                <fieldset>
                    <legend>Historial de Citas</legend>
                    <?php if (empty($citas)): ?>
                        <p>No hay citas registradas para este paciente.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Estado</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($citas as $cita): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($cita['Fecha']); ?></td>
                                        <td><?php echo htmlspecialchars($cita['Hora']); ?></td>
                                        <td><?php echo htmlspecialchars($cita['Estado']); ?></td>
                                        <td><?php echo htmlspecialchars($cita['Notas']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </fieldset>
            </div>

            <!-- Botones de acción -->
            <div class="buttons">
                <button type="button" onclick="window.location.href='adminPacientes.php'">Volver</button>
                <button type="button" onclick="window.location.href='exportarHistorialPaciente.php?id=<?php echo $id; ?>'">Descargar Excel</button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
