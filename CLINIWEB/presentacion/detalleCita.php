<?php
require_once '../conexion/conexion.php';

$conexion = Conexion::getInstancia()->getConexion();

if (isset($_GET['citaId'])) {
    $citaId = $_GET['citaId'];

    try {
        $sql = "SELECT 
                    C.Fecha, 
                    C.Hora, 
                    C.Notas, 
                    P.Nombre AS PacienteNombre, 
                    P.Ape_Paterno AS PacienteApellidoPaterno, 
                    P.Ape_Materno AS PacienteApellidoMaterno, 
                    P.DNI AS PacienteDNI, 
                    P.Fecha_Nacimiento AS PacienteFechaNacimiento, 
                    P.Telefono AS PacienteTelefono, 
                    P.Direccion AS PacienteDireccion, 
                    P.Sexo AS PacienteSexo
                FROM Citas C
                JOIN Pacientes P ON C.PacienteId = P.PacienteId
                WHERE C.CitaId = :citaId";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':citaId', $citaId, PDO::PARAM_INT);
        $stmt->execute();
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cita) {
            echo json_encode($cita);
        } else {
            echo json_encode(['error' => 'Cita no encontrada']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'ID de cita no proporcionado']);
}
?>
