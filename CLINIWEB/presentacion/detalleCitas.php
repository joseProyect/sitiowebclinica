<?php

require_once '../conexion/conexion.php';

if (!isset($_GET['id'])) {
    header('Location: adminPacientes.php?error=No se especificó un paciente.');
    exit();
}

$pacienteId = $_GET['id'];
$citas = [];
$error = "";

try {
    $conexion = Conexion::getInstancia()->getConexion();
    $sql = "SELECT * FROM Citas WHERE PacienteId = :pacienteId";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':pacienteId', $pacienteId, PDO::PARAM_INT);
    $stmt->execute();
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$citas) {
        $error = "No se encontraron citas para este paciente.";
    }
} catch (PDOException $e) {
    $error = "Error al obtener las citas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Citas</title>
    <link rel="stylesheet" href="./css/detalleCitas.css">
</head>
<body>
    <div class="container">
        <h1>Detalle de Citas</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php else: ?>
            <form>
                <?php foreach ($citas as $index => $cita): ?>
                    <fieldset>
                        <legend>Cita <?php echo $index + 1; ?></legend>
                        <label for="citaId">Cita ID:</label>
                        <input type="text" id="citaId" value="<?php echo htmlspecialchars($cita['CitaId']); ?>" readonly>

                        <label for="fecha">Fecha:</label>
                        <input type="text" id="fecha" value="<?php echo htmlspecialchars($cita['Fecha']); ?>" readonly>

                        <label for="hora">Hora:</label>
                        <input type="text" id="hora" value="<?php echo htmlspecialchars($cita['Hora']); ?>" readonly>

                        <label for="estado">Estado:</label>
                        <input type="text" id="estado" value="<?php echo htmlspecialchars($cita['Estado']); ?>" readonly>

                        <label for="notas">Notas:</label>
                        <textarea id="notas" readonly><?php echo htmlspecialchars($cita['Notas']); ?></textarea>
                    </fieldset>
                <?php endforeach; ?>

                <div class="buttons">
                    <button type="button" class="btn-back" onclick="window.location.href='adminPacientes.php'">Volver</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
