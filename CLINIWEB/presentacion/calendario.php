<?php
require_once '../conexion/conexion.php';

$conexion = Conexion::getInstancia()->getConexion();

session_start();
if (!isset($_SESSION['DoctorId'])) {
    header('Location: login.php');
    exit();
}

$doctorId = $_SESSION['DoctorId'];

$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

try {
    $sql = "SELECT CitaId, Fecha, Hora, Notas FROM Citas WHERE DoctorId = :doctorId AND MONTH(Fecha) = :mes AND YEAR(Fecha) = :anio";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':doctorId', $doctorId);
    $stmt->bindParam(':mes', $mes, PDO::PARAM_INT);
    $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
    $stmt->execute();
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar las citas: " . $e->getMessage());
}

$citasPorDia = [];
foreach ($citas as $cita) {
    $dia = date('j', strtotime($cita['Fecha']));
    $citasPorDia[$dia][] = $cita;
}

function generarCalendario($mes, $anio, $citasPorDia)
{
    $diasDelMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
    $primerDia = date('N', strtotime("$anio-$mes-01"));
    $nombreMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $nombreDias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

    echo "<div class='calendar-container'>";
    echo "<h1>{$nombreMeses[$mes - 1]} $anio</h1>";
    echo "<div class='calendar-grid'>";
    
    foreach ($nombreDias as $dia) {
        echo "<div class='day-header'>$dia</div>";
    }

    for ($i = 1; $i < $primerDia; $i++) {
        echo "<div class='day empty'></div>";
    }

    for ($dia = 1; $dia <= $diasDelMes; $dia++) {
        echo "<div class='day'>";
        echo "<div class='date-number'>$dia</div>";

        if (isset($citasPorDia[$dia])) {
            foreach ($citasPorDia[$dia] as $cita) {
                echo "<div class='event' data-cita-id='{$cita['CitaId']}'>";
                echo "<span>" . date('H:i', strtotime($cita['Hora'])) . "</span>";
                echo "<p>" . htmlspecialchars($cita['Notas']) . "</p>";
                echo "</div>";
            }
        }

        echo "</div>";
    }

    echo "</div>";
    echo "</div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Citas</title>
    <link rel="stylesheet" href="./css/calendario.css">
    <link rel="stylesheet" href="./css/detalleCita.css">
</head>
<body>
    <div class="calendar-wrapper">
        <div class="calendar-controls">
            <button onclick="cambiarMes(-1)">Anterior</button>
            <button onclick="cambiarMes(1)">Siguiente</button>
        </div>
        <?php generarCalendario($mes, $anio, $citasPorDia); ?>
    </div>

    <div id="detalle-cita" class="detalle-cita-container"></div>

    <script src="./js/calendario.js"></script>
    <script>
        let mes = <?php echo $mes; ?>;
        let anio = <?php echo $anio; ?>;

        function cambiarMes(direccion) {
            mes += direccion;
            if (mes < 1) {
                mes = 12;
                anio--;
            } else if (mes > 12) {
                mes = 1;
                anio++;
            }
            window.location.href = `calendario.php?mes=${mes}&anio=${anio}`;
        }
    </script>
</body>
</html>
