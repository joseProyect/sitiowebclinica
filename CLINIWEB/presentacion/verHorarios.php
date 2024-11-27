<?php
include_once "../dao/horarioDAO.php";

// Obtener el ID del doctor
$doctorId = isset($_GET['doctorId']) ? $_GET['doctorId'] : null;

if (!$doctorId) {
    header("Location: adminHorarios.php");
    exit;
}

$horarioDAO = new HorarioDAO();
$horarios = $horarioDAO->obtenerHorariosPorDoctor($doctorId);
$doctor = $horarioDAO->obtenerNombreDoctorPorId($doctorId); // Obtén el nombre del doctor

if (!$doctor) {
    header("Location: adminHorarios.php?msg=doctor_not_found");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios del Doctor <?= htmlspecialchars($doctor['NombreCompleto']) ?></title>
    <link rel="stylesheet" href="./css/verHorarios.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <h1>Horarios del Doctor: <?= htmlspecialchars($doctor['NombreCompleto']) ?></h1>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="horarios-lista">
                <?php if (!empty($horarios)): ?>
                    <?php foreach ($horarios as $horario): ?>
                        <tr id="horario-<?= $horario['HorarioId'] ?>">
                            <td><?= htmlspecialchars($horario['Dia']) ?></td>
                            <td><?= htmlspecialchars($horario['Hora_Inicio']) ?></td>
                            <td><?= htmlspecialchars($horario['Hora_Fin']) ?></td>
                            <td class="acciones">
                                <!-- Botón para Editar -->
                                <a href="editarHorario.php?horarioId=<?= $horario['HorarioId'] ?>" class="accion editar" title="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <!-- Botón para Eliminar -->
                                <button class="accion eliminar" data-id="<?= $horario['HorarioId'] ?>" title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No hay horarios disponibles para este doctor.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="adminHorarios.php" class="volver">Volver</a>
    </div>

    <div id="modal-confirmacion" class="modal">
        <div class="modal-contenido">
            <h3>¿Estás seguro de eliminar este horario?</h3>
            <div class="modal-acciones">
                <button id="btn-cancelar" class="btn-cancelar">Cancelar</button>
                <button id="btn-confirmar" class="btn-confirmar">Aceptar</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let horarioIdAEliminar = null;

            // Manejo de la acción de eliminar
            document.querySelectorAll('.eliminar').forEach(button => {
                button.addEventListener('click', function () {
                    horarioIdAEliminar = this.getAttribute('data-id');
                    document.getElementById('modal-confirmacion').style.display = 'flex';
                });
            });

            document.getElementById('btn-cancelar').addEventListener('click', function () {
                document.getElementById('modal-confirmacion').style.display = 'none';
                horarioIdAEliminar = null;
            });

            document.getElementById('btn-confirmar').addEventListener('click', function () {
                if (horarioIdAEliminar) {
                    fetch('../controladores/procesarHorario.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            accion: 'eliminar',
                            horarioId: horarioIdAEliminar
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                document.querySelector(`#horario-${horarioIdAEliminar}`).remove();
                            } else {
                                alert("Error al eliminar horario: " + data.message);
                            }
                        })
                        .catch(error => {
                            alert("Error en la solicitud: " + error.message);
                        })
                        .finally(() => {
                            document.getElementById('modal-confirmacion').style.display = 'none';
                            horarioIdAEliminar = null;
                        });
                }
            });
        });
    </script>
</body>
</html>
