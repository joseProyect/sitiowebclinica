<?php
require_once '../conexion/conexion.php';

$conexion = Conexion::getInstancia()->getConexion();
$search = $_GET['search'] ?? ''; // Capturar el término de búsqueda (DNI del paciente)
$citas = [];

// Eliminar cita si se solicita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    try {
        $sql = "DELETE FROM Citas WHERE CitaId = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        $error = "Error al eliminar la cita: " . $e->getMessage();
    }
}

// Cambiar estado de cita si se solicita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado'])) {
    $id = $_POST['id'];
    $estado = $_POST['estado'];

    try {
        $sql = "UPDATE Citas SET Estado = :estado WHERE CitaId = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        $error = "Error al actualizar el estado: " . $e->getMessage();
    }
}

// Obtener citas filtradas por DNI del paciente
try {
    $query = "SELECT 
                Citas.CitaId, 
                Citas.Fecha, 
                Citas.Hora, 
                Pacientes.Nombre AS PacienteNombre, 
                Pacientes.Ape_Paterno AS PacienteApellido, 
                Doctores.DoctorId, 
                Usuarios.Nombre AS DoctorNombre, 
                Usuarios.Ape_Paterno AS DoctorApellido, 
                Citas.Estado, 
                Citas.Notas 
              FROM 
                Citas
              JOIN 
                Pacientes ON Citas.PacienteId = Pacientes.PacienteId
              JOIN 
                Doctores ON Citas.DoctorId = Doctores.DoctorId
              JOIN 
                Usuarios ON Doctores.UsuarioId = Usuarios.UsuarioId";

    // Agregar filtro por DNI si se proporciona
    if ($search) {
        $query .= " WHERE Pacientes.DNI = :search";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':search', $search, PDO::PARAM_INT);
    } else {
        $stmt = $conexion->prepare($query);
    }

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
    <title>Administrar Citas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/adminCita.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Administrar Citas</h1>
            <div class="search-bar">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Buscar por DNI del paciente" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <button class="add-appointment-button" onclick="location.href='Citas.php'">
                <i class="fa fa-calendar-plus"></i> Agregar Cita
            </button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Doctor</th>
                    <th>Estado</th>
                    <th>Notas</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($citas)): ?>
                    <tr>
                        <td colspan="7">No se encontraron citas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($citas as $cita): ?>
                    <tr>
                        <td data-label="Fecha"><?php echo htmlspecialchars($cita['Fecha']); ?></td>
                        <td data-label="Hora"><?php echo htmlspecialchars($cita['Hora']); ?></td>
                        <td data-label="Paciente"><?php echo htmlspecialchars($cita['PacienteNombre'] . " " . $cita['PacienteApellido']); ?></td>
                        <td data-label="Doctor"><?php echo htmlspecialchars($cita['DoctorNombre'] . " " . $cita['DoctorApellido']); ?></td>
                        <td data-label="Estado">
                            <?php echo htmlspecialchars($cita['Estado']); ?>
                            <!-- Botón para cambiar estado con icono -->
                            <button onclick="mostrarFormularioEstado(<?php echo $cita['CitaId']; ?>)" title="Cambiar Estado">
                                <i class="fa fa-pencil-alt"></i> Cambiar Estado
                            </button>
                        </td>
                        <td data-label="Notas"><?php echo htmlspecialchars($cita['Notas']); ?></td>
                        <td data-label="Opciones">
                            <!-- Botón Ver Detalle -->
                            <button onclick="editarCita(<?php echo $cita['CitaId']; ?>)" title="Ver Detalle">
                                <i class="fa fa-eye" style="color: #40E0D0;"></i>
                            </button>
                            <!-- Botón Eliminar -->
                            <button onclick="confirmarEliminacion(<?php echo $cita['CitaId']; ?>)" title="Eliminar">
                                <i class="fa fa-trash" style="color: #FF6F61;"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Formulario flotante para cambiar estado -->
        <div id="formularioEstado" class="formularioEstado" style="display: none;">
            <form method="POST" action="">
                <label for="estado">Estado:</label>
                <select name="estado" required>
                    <option value="Agendada">Agendada</option>
                    <option value="Cancelada">Cancelada</option>
                    <option value="Completada">Completada</option>
                </select>
                <input type="hidden" name="id" id="citaId">
                <button type="submit" name="cambiar_estado">Cambiar Estado</button>
                <button type="button" onclick="cerrarFormularioEstado()">Cerrar</button>
            </form>
        </div>
    </div>

   <script>
    function editarCita(id) {
        window.location.href = `editCita.php?id=${id}`;
    }

    function mostrarFormularioEstado(citaId) {
        document.getElementById('citaId').value = citaId;
        document.getElementById('formularioEstado').style.display = 'block'; // Mostrar formulario
    }

    function cerrarFormularioEstado() {
        document.getElementById('formularioEstado').style.display = 'none'; // Ocultar formulario
    }

    function confirmarEliminacion(citaId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'eliminar';
                input.value = true;
                form.appendChild(input);
                var inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = citaId;
                form.appendChild(inputId);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

</body>
</html>
