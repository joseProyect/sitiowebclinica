<?php
require_once '../conexion/conexion.php';

if (!isset($_GET['id'])) {
    header('Location: adminPacientes.php?error=No se especificó un paciente.');
    exit();
}

$id = $_GET['id'];
$error = "";

try {
    $conexion = Conexion::getInstancia()->getConexion();
    $sql = "SELECT * FROM Pacientes WHERE PacienteId = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        header('Location: adminPacientes.php?error=Paciente no encontrado.');
        exit();
    }
} catch (PDOException $e) {
    $error = "Error al obtener los detalles del paciente: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Paciente</title>
    <link rel="stylesheet" href="./css/detallePaciente.css">
</head>
<body>
    <div class="container">
        <h1>Detalle del Paciente</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php else: ?>
            <form>
                <fieldset>
                    <legend>Información Personal</legend>
                    <p><strong>Paciente ID:</strong> <?php echo htmlspecialchars($paciente['PacienteId']); ?></p>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($paciente['Nombre']); ?></p>
                    <p><strong>Apellido Paterno:</strong> <?php echo htmlspecialchars($paciente['Ape_Paterno']); ?></p>
                    <p><strong>Apellido Materno:</strong> <?php echo htmlspecialchars($paciente['Ape_Materno']); ?></p>
                </fieldset>

                <fieldset>
                    <legend>Contacto</legend>
                    <p><strong>DNI:</strong> <?php echo htmlspecialchars($paciente['DNI']); ?></p>
                    <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($paciente['Fecha_Nacimiento']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($paciente['Telefono']); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($paciente['Direccion']); ?></p>
                </fieldset>

                <fieldset>
                    <legend>Información Adicional</legend>
                    <p><strong>Sexo:</strong> <?php echo htmlspecialchars($paciente['Sexo']); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($paciente['Estado']); ?></p>
                </fieldset>

                <div class="buttons">
                    <button type="button" onclick="window.location.href='adminPacientes.php'">Volver</button>
                    <button type="button" onclick="window.location.href='editarPaciente.php?id=<?php echo $paciente['PacienteId']; ?>'">Editar</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
