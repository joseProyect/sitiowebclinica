<?php
require_once '../conexion/conexion.php';

$conexion = Conexion::getInstancia()->getConexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha']; // Fecha seleccionada
    $intervaloId = $_POST['intervaloId']; // Intervalo seleccionado
    $pacienteId = $_POST['pacienteId']; // Paciente seleccionado
    $doctorId = $_POST['doctorId']; // Doctor seleccionado
    $notas = $_POST['notas'] ?? ''; // Notas adicionales, opcionales

    try {
        // Verificar que el intervalo está disponible
        $stmtCheck = $conexion->prepare("
            SELECT Disponible 
            FROM IntervalosHorario 
            WHERE IntervaloId = :intervaloId
        ");
        $stmtCheck->bindParam(':intervaloId', $intervaloId, PDO::PARAM_INT);
        $stmtCheck->execute();
        $disponible = $stmtCheck->fetchColumn();

        if ($disponible != 1) {
            throw new Exception("El intervalo seleccionado no está disponible.");
        }

        // Obtener la hora del intervalo
        $stmtHora = $conexion->prepare("
            SELECT Hora 
            FROM IntervalosHorario 
            WHERE IntervaloId = :intervaloId
        ");
        $stmtHora->bindParam(':intervaloId', $intervaloId, PDO::PARAM_INT);
        $stmtHora->execute();
        $hora = $stmtHora->fetchColumn();

        // Insertar la cita
        $stmt = $conexion->prepare("
            INSERT INTO Citas (Fecha, Hora, IntervaloId, PacienteId, DoctorId, Estado, Notas)
            VALUES (:fecha, :hora, :intervaloId, :pacienteId, :doctorId, 'Agendada', :notas)
        ");
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':intervaloId', $intervaloId);
        $stmt->bindParam(':pacienteId', $pacienteId);
        $stmt->bindParam(':doctorId', $doctorId);
        $stmt->bindParam(':notas', $notas);
        $stmt->execute();

        // Actualizar el estado del intervalo a no disponible
        $stmtUpdate = $conexion->prepare("
            UPDATE IntervalosHorario SET Disponible = 0 WHERE IntervaloId = :intervaloId
        ");
        $stmtUpdate->bindParam(':intervaloId', $intervaloId, PDO::PARAM_INT);
        $stmtUpdate->execute();

        echo "<script>alert('Cita agregada correctamente'); window.location.href = '../presentacion/adminCitas.php';</script>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
