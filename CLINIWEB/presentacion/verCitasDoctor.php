<?php
session_start();
if (!isset($_SESSION['UsuarioId']) || $_SESSION['TipoUsuario'] !== 'Administrador') {
    header('Location: login.php');
    exit();
}

require_once '../conexion/conexion.php';

if (!isset($_GET['doctorId'])) {
    header('Location: adminDoctores.php?error=No se especificó un doctor.');
    exit();
}

$doctorId = intval($_GET['doctorId']);
$citas = [];
$doctor = [];
$error = "";

// Obtener el nombre del doctor
try {
    $conexion = Conexion::getInstancia()->getConexion();
    $sqlDoctor = "SELECT Usuarios.Nombre, Usuarios.Ape_Paterno, Usuarios.Ape_Materno 
                  FROM Doctores 
                  JOIN Usuarios ON Doctores.UsuarioId = Usuarios.UsuarioId 
                  WHERE Doctores.DoctorId = :doctorId";
    $stmtDoctor = $conexion->prepare($sqlDoctor);
    $stmtDoctor->bindParam(':doctorId', $doctorId, PDO::PARAM_INT);
    $stmtDoctor->execute();
    $doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);

    if (!$doctor) {
        $error = "No se encontró información del doctor.";
    }
} catch (PDOException $e) {
    $error = "Error al obtener la información del doctor: " . $e->getMessage();
}

// Obtener las citas del doctor
if (!$error) {
    try {
        $sqlCitas = "SELECT 
                        Citas.Fecha, 
                        Citas.Hora, 
                        Pacientes.Nombre AS PacienteNombre, 
                        Pacientes.Ape_Paterno AS PacienteApellido, 
                        Citas.Estado, 
                        Citas.Notas 
                    FROM 
                        Citas
                    JOIN 
                        Pacientes ON Citas.PacienteId = Pacientes.PacienteId
                    WHERE 
                        Citas.DoctorId = :doctorId
                    ORDER BY Citas.Fecha ASC, Citas.Hora ASC";
        $stmtCitas = $conexion->prepare($sqlCitas);
        $stmtCitas->bindParam(':doctorId', $doctorId, PDO::PARAM_INT);
        $stmtCitas->execute();
        $citas = $stmtCitas->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error al obtener las citas: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Citas del Doctor</title>
    <link rel="stylesheet" href="./css/verCitasDoctor.css">
</head>
<body>
    <div class="container">
        <h1>Citas del Doctor</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php else: ?>
            <h2><?php echo htmlspecialchars($doctor['Nombre'] . " " . $doctor['Ape_Paterno'] . " " . $doctor['Ape_Materno']); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Estado</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($citas)): ?>
                        <tr>
                            <td colspan="5">No se encontraron citas para este doctor.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($citas as $cita): ?>
                            <tr>
                                <td data-label="Fecha"><?php echo htmlspecialchars($cita['Fecha']); ?></td>
                                <td data-label="Hora"><?php echo htmlspecialchars($cita['Hora']); ?></td>
                                <td data-label="Paciente"><?php echo htmlspecialchars($cita['PacienteNombre'] . " " . $cita['PacienteApellido']); ?></td>
                                <td data-label="Estado"><?php echo htmlspecialchars($cita['Estado']); ?></td>
                                <td data-label="Notas"><?php echo htmlspecialchars($cita['Notas']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <button class="btn-back" onclick="window.location.href='adminDoctor.php'">Volver</button>
    </div>
</body>
</html>
