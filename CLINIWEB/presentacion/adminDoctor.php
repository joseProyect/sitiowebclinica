<?php
include_once "../dao/doctorDAO.php";
$doctorDAO = new DoctorDAO();
$doctores = $doctorDAO->obtenerDoctores();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Doctores</title>
    <link rel="stylesheet" href="./css/adminDoctores.css">
    <script>
        // Función para redirigir a la página de edición
        function editarDoctor(doctorId) {
            window.location.href = "editarDoctor.php?doctorId=" + doctorId;
        }

        // Función para redirigir a la página de citas
        function verCitas(doctorId) {
            window.location.href = "verCitasDoctor.php?doctorId=" + doctorId;
        }

        // Función para redirigir a la página de horarios
        function verHorarios(doctorId) {
            window.location.href = "adminHorarios.php?doctorId=" + doctorId;
        }

        // Función para agregar un nuevo doctor
        function mostrarFormularioAgregar() {
            window.location.href = "agregarUsuario.php";
        }
    </script>
</head>
<body>
    <h1>Administrar Doctores</h1>
    <button onclick="mostrarFormularioAgregar()">Agregar Doctor</button>

    <!-- Tabla de Doctores -->
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Especialidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($doctores as $doctor): ?>
                <tr>
                    <td data-label="Nombre"><?= $doctor['Nombre'] . " " . $doctor['Ape_Paterno'] . " " . $doctor['Ape_Materno'] ?></td>
                    <td data-label="Teléfono"><?= $doctor['Telefono'] ?></td>
                    <td data-label="Dirección"><?= $doctor['Direccion'] ?></td>
                    <td data-label="Especialidad"><?= $doctor['Especialidad'] ?></td>
                    <td data-label="Acciones">
                        <button onclick="editarDoctor(<?= $doctor['DoctorId'] ?>)">Editar</button>
                        <button onclick="verCitas(<?= $doctor['DoctorId'] ?>)">Ver Citas</button>
                        <button onclick="verHorarios(<?= $doctor['DoctorId'] ?>)">Ver Horarios</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
