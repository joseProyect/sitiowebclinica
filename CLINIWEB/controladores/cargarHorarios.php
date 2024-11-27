<?php
include_once "../dao/horarioDAO.php";

// Verificar si el parámetro doctorId está presente
if (isset($_GET['doctorId']) && !empty($_GET['doctorId'])) {
    $horarioDAO = new HorarioDAO();
    $doctorId = intval($_GET['doctorId']); // Asegurarse de que doctorId sea un entero
    $horarios = $horarioDAO->obtenerHorariosPorDoctor($doctorId);

    // Verificar si hay horarios para mostrar
    if (!empty($horarios)) {
        foreach ($horarios as $horario) {
            // Escapar valores para prevenir ataques XSS
            $horarioId = htmlspecialchars($horario['HorarioId'], ENT_QUOTES, 'UTF-8');
            $dia = htmlspecialchars($horario['Dia'], ENT_QUOTES, 'UTF-8');
            $horaInicio = htmlspecialchars($horario['Hora_Inicio'], ENT_QUOTES, 'UTF-8');
            $horaFin = htmlspecialchars($horario['Hora_Fin'], ENT_QUOTES, 'UTF-8');

            // Generar fila de la tabla con botones de acción
            echo "<tr>
                <td>{$dia}</td>
                <td>{$horaInicio}</td>
                <td>{$horaFin}</td>
                <td>
                    <button onclick=\"editarHorario({
                        HorarioId: '{$horarioId}',
                        DoctorId: '{$doctorId}',
                        Dia: '{$dia}',
                        HoraInicio: '{$horaInicio}',
                        HoraFin: '{$horaFin}'
                    })\">Editar</button>
                    <button onclick=\"mostrarModal('{$horarioId}')\">Eliminar</button>
                </td>
            </tr>";
        }
    } else {
        // Mostrar mensaje si no hay horarios
        echo "<tr>
            <td colspan='4'>No se encontraron horarios para este doctor.</td>
        </tr>";
    }
} else {
    // Mensaje si no se proporcionó doctorId
    echo "<tr>
        <td colspan='4'>Error: No se ha seleccionado un doctor válido.</td>
    </tr>";
}
?>
