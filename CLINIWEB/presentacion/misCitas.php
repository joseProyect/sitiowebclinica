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

try {
    $conexion = Conexion::getInstancia()->getConexion();
    $sql = "SELECT 
                c.CitaId,
                c.Fecha,
                c.Hora,
                c.Estado,
                c.Notas,
                p.Nombre AS PacienteNombre,
                p.Ape_Paterno AS PacienteApellidoPaterno,
                p.Ape_Materno AS PacienteApellidoMaterno,
                p.DNI AS PacienteDNI
            FROM Citas c
            JOIN Pacientes p ON c.PacienteId = p.PacienteId
            WHERE c.DoctorId = :doctorId AND c.Fecha >= CURDATE()"; // Solo citas desde hoy al futuro
    $sql .= " ORDER BY c.Fecha ASC, c.Hora ASC"; // Ordenar las citas

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':doctorId', $doctorId, PDO::PARAM_INT);
    $stmt->execute();
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener las citas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas</title>
    <link rel="stylesheet" href="./css/misCitas.css">
    <script src="./js/misCita.js" defer></script>
</head>
<body>
    <header class="menu">
        <button id="btnAgregar">Agregar</button>
        <button id="btnEditar" disabled>Editar</button>
        <button id="btnCambiarEstado" disabled>Cambiar Estado</button>
    </header>
    <div class="container">
        <h1>Mis Citas</h1>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php else: ?>
            <div class="citas-grid">
                <?php foreach ($citas as $cita): ?>
                    <div class="cita-card" data-id="<?php echo $cita['CitaId']; ?>" data-estado="<?php echo $cita['Estado']; ?>">
                        <h3><?php echo htmlspecialchars($cita['PacienteNombre'] . ' ' . $cita['PacienteApellidoPaterno'] . ' ' . $cita['PacienteApellidoMaterno']); ?></h3>
                        <p><strong>Fecha:</strong> <?php echo htmlspecialchars($cita['Fecha']); ?></p>
                        <p><strong>Hora:</strong> <?php echo htmlspecialchars($cita['Hora']); ?></p>
                        <p><strong>Estado:</strong> <span class="estado-cita"><?php echo htmlspecialchars($cita['Estado']); ?></span></p>
                        <p><strong>Notas:</strong> <?php echo htmlspecialchars($cita['Notas']); ?></p>
                        <p><strong>DNI Paciente:</strong> <?php echo htmlspecialchars($cita['PacienteDNI']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
