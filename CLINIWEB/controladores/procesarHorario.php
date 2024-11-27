<?php
header('Content-Type: application/json'); // Respuesta en formato JSON
include_once "../dao/horarioDAO.php";

$horarioDAO = new HorarioDAO();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
        $accion = $_POST['accion'];

        if ($accion === 'agregar') {
            // Agregar horarios
            $doctorId = $_POST['doctorId'];
            $dias = isset($_POST['dias']) ? $_POST['dias'] : [];
            $horaInicio = $_POST['horaInicio'];
            $horaFin = $_POST['horaFin'];

            if (!empty($dias) && !empty($horaInicio) && !empty($horaFin)) {
                foreach ($dias as $dia) {
                    $horarioDAO->agregarHorario($doctorId, $dia, $horaInicio, $horaFin);
                }
                echo json_encode(['success' => true, 'message' => 'Horarios agregados correctamente']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Datos incompletos para agregar horarios']);
                exit;
            }
        } elseif ($accion === 'eliminar') {
            // Eliminar horario
            $horarioId = $_POST['horarioId'];
            if (!empty($horarioId)) {
                $resultado = $horarioDAO->eliminarHorario($horarioId);
                if ($resultado) {
                    echo json_encode(['success' => true, 'message' => 'Horario eliminado correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al eliminar el horario']);
                }
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'ID de horario no especificado']);
                exit;
            }
        }
    } else {
        // Respuesta para solicitudes inválidas
        echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
        exit;
    }
} catch (Exception $e) {
    // Manejo de excepciones
    echo json_encode(['success' => false, 'message' => 'Ocurrió un error: ' . $e->getMessage()]);
    exit;
}
