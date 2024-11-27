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

// Consultar los horarios del doctor
$sqlHorarios = "SELECT Dia, Hora_Inicio, Hora_Fin 
                FROM Horarios 
                WHERE DoctorId = :doctorId";
$stmt = $conexion->prepare($sqlHorarios);
$stmt->bindParam(':doctorId', $doctorId, PDO::PARAM_INT);
$stmt->execute();
$horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear una estructura para organizar los horarios
$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$horas = [
    '08:00', '09:00', '10:00', '11:00', '12:00',
    '14:00', '15:00', '16:00', '17:00', '18:00'
];

// Inicializar la tabla de horarios
$tablaHorarios = [];
foreach ($dias as $dia) {
    foreach ($horas as $hora) {
        $tablaHorarios[$dia][$hora] = false; // Por defecto, no está ocupado
    }
}

// Rellenar los horarios ocupados según la base de datos
foreach ($horarios as $horario) {
    $dia = $horario['Dia'];
    $horaInicio = strtotime($horario['Hora_Inicio']);
    $horaFin = strtotime($horario['Hora_Fin']);

    // Marcar las horas ocupadas en la tabla, incluyendo la última hora
    foreach ($horas as $hora) {
        $horaActual = strtotime($hora);
        if ($horaActual >= $horaInicio && $horaActual <= $horaFin) {
            $tablaHorarios[$dia][$hora] = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario del Doctor</title>
    <link rel="stylesheet" href="./css/horario.css">
</head>
<body>
    <div class="container">
        <h1>Horario del Doctor</h1>
        <table>
            <thead>
                <tr>
                    <th>Hora/Día</th>
                    <?php foreach ($dias as $dia): ?>
                        <th><?php echo $dia; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <!-- Bloque de la mañana -->
                <?php foreach (['08:00', '09:00', '10:00', '11:00', '12:00'] as $hora): ?>
                    <tr>
                        <td><?php echo $hora; ?></td>
                        <?php foreach ($dias as $dia): ?>
                            <td class="<?php echo $tablaHorarios[$dia][$hora] ? 'disponible' : 'vacio'; ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>

                <!-- Bloque de descanso -->
                <tr>
                    <td colspan="<?php echo count($dias) + 1; ?>" class="descanso">Hora de descanso</td>
                </tr>

                <!-- Bloque de la tarde -->
                <?php foreach (['14:00', '15:00', '16:00', '17:00', '18:00'] as $hora): ?>
                    <tr>
                        <td><?php echo $hora; ?></td>
                        <?php foreach ($dias as $dia): ?>
                            <td class="<?php echo $tablaHorarios[$dia][$hora] ? 'disponible' : 'vacio'; ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
