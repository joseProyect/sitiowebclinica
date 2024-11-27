<?php
require_once '../conexion/conexion.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No se especificó un paciente.");
}

$id = $_GET['id'];

try {
    $conexion = Conexion::getInstancia()->getConexion();

    // Obtener detalles del paciente
    $sqlPaciente = "SELECT * FROM Pacientes WHERE PacienteId = :id";
    $stmtPaciente = $conexion->prepare($sqlPaciente);
    $stmtPaciente->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtPaciente->execute();
    $paciente = $stmtPaciente->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        die("Paciente no encontrado.");
    }

    // Obtener citas del paciente
    $sqlCitas = "SELECT * FROM Citas WHERE PacienteId = :id ORDER BY Fecha, Hora ASC";
    $stmtCitas = $conexion->prepare($sqlCitas);
    $stmtCitas->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtCitas->execute();
    $citas = $stmtCitas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener los datos: " . $e->getMessage());
}

// Crear el archivo Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=historial_paciente_" . $id . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Escribir el contenido del archivo
echo "<table border='1'>";
echo "<tr><th colspan='2'>Información del Paciente</th></tr>";
echo "<tr><td>Nombre:</td><td>{$paciente['Nombre']}</td></tr>";
echo "<tr><td>Apellido Paterno:</td><td>{$paciente['Ape_Paterno']}</td></tr>";
echo "<tr><td>Apellido Materno:</td><td>{$paciente['Ape_Materno']}</td></tr>";
echo "<tr><td>DNI:</td><td>{$paciente['DNI']}</td></tr>";
echo "<tr><td>Fecha de Nacimiento:</td><td>{$paciente['Fecha_Nacimiento']}</td></tr>";
echo "<tr><td>Teléfono:</td><td>{$paciente['Telefono']}</td></tr>";
echo "<tr><td>Dirección:</td><td>{$paciente['Direccion']}</td></tr>";
echo "<tr><td>Sexo:</td><td>{$paciente['Sexo']}</td></tr>";
echo "<tr><td>Estado:</td><td>{$paciente['Estado']}</td></tr>";

if (!empty($citas)) {
    echo "<tr><th colspan='4'>Historial de Citas</th></tr>";
    echo "<tr><th>Fecha</th><th>Hora</th><th>Estado</th><th>Notas</th></tr>";
    foreach ($citas as $cita) {
        echo "<tr>";
        echo "<td>{$cita['Fecha']}</td>";
        echo "<td>{$cita['Hora']}</td>";
        echo "<td>{$cita['Estado']}</td>";
        echo "<td>{$cita['Notas']}</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No hay citas registradas para este paciente.</td></tr>";
}
echo "</table>";
?>
