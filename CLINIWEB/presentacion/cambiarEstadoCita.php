<?php
session_start();

// Verificar si el DoctorId está en la sesión
if (!isset($_SESSION['DoctorId'])) {
    header('Location: login.php'); // Redirigir si no está autenticado
    exit();
}

$doctorId = $_SESSION['DoctorId'];

// Conectar a la base de datos
require_once '../conexion/conexion.php';

// Obtener el CitaId de la URL
$citaId = isset($_GET['citaId']) ? $_GET['citaId'] : null;

if (!$citaId) {
    die("Cita no válida.");
}

try {
    $conexion = Conexion::getInstancia()->getConexion();

    // Obtener los datos de la cita
    $sql = "SELECT * FROM Citas WHERE CitaId = :citaId AND DoctorId = :doctorId";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':citaId', $citaId, PDO::PARAM_INT);
    $stmt->bindParam(':doctorId', $doctorId, PDO::PARAM_INT);
    $stmt->execute();

    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cita) {
        die("Cita no encontrada.");
    }

} catch (PDOException $e) {
    echo "Error al obtener la cita: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado = $_POST['estado'];

    try {
        $sql = "UPDATE Citas SET Estado = :estado WHERE CitaId = :citaId";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':citaId', $citaId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo "Estado de la cita actualizado correctamente.";
            header("Location: misCitas.php"); // Redirigir de vuelta a la lista de citas
        } else {
            echo "Error al actualizar el estado.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Estado de Cita</title>
	<link rel="stylesheet" href="./css/cambiarEstadoCita.css">
</head>
<body>
    <h1>Cambiar Estado de la Cita</h1>

    <form method="POST">
        <label for="estado">Nuevo Estado:</label>
        <select id="estado" name="estado">
            <option value="Agendada" <?php if ($cita['Estado'] == 'Agendada') echo 'selected'; ?>>Agendada</option>
            <option value="Cancelada" <?php if ($cita['Estado'] == 'Cancelada') echo 'selected'; ?>>Cancelada</option>
            <option value="Completada" <?php if ($cita['Estado'] == 'Completada') echo 'selected'; ?>>Completada</option>
        </select>
        <button type="submit">Actualizar Estado</button>
    </form>

    <a href="misCitas.php">Volver a las citas</a>

</body>
</html>
