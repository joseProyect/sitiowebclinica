<?php
include_once "../dao/doctorDAO.php";
$doctorDAO = new DoctorDAO();
$doctores = $doctorDAO->obtenerDoctores();

// Verificar si se seleccionó un doctor para editar
$doctorSeleccionado = null;
if (isset($_GET['doctorId'])) {
    $doctorSeleccionado = $doctorDAO->obtenerDoctorPorId($_GET['doctorId']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Administrar Doctores</title>
	<link rel="stylesheet" href="./css/editarDoctor.css">
    <script>
        function editarDoctor(id) {
            window.location.href = "?doctorId=" + id;
        }

        function cancelarEdicion() {
            window.location.href = "adminDoctor.php";
        }
    </script>
</head>
<body>
    <!-- Formulario de edición -->
    <?php if ($doctorSeleccionado): ?>
        <h2>Editar Doctor</h2>
        <form action="procesarEdicionDoctor.php" method="POST">
            <input type="hidden" name="doctorId" value="<?= $doctorSeleccionado['DoctorId'] ?>">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?= $doctorSeleccionado['Nombre'] ?>" required><br>
            
            <label for="ape_paterno">Apellido Paterno:</label>
            <input type="text" id="ape_paterno" name="ape_paterno" value="<?= $doctorSeleccionado['Ape_Paterno'] ?>" required><br>
            
            <label for="ape_materno">Apellido Materno:</label>
            <input type="text" id="ape_materno" name="ape_materno" value="<?= $doctorSeleccionado['Ape_Materno'] ?>" required><br>
            
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?= $doctorSeleccionado['Telefono'] ?>"><br>
            
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?= $doctorSeleccionado['Direccion'] ?>"><br>
            
            <label for="especialidad">Especialidad:</label>
            <select id="especialidad" name="especialidad">
                <?php
                $especialidades = $doctorDAO->obtenerEspecialidades();
                foreach ($especialidades as $especialidad):
                    $selected = $doctorSeleccionado['Especialidad'] == $especialidad['Nombre'] ? 'selected' : '';
                ?>
                    <option value="<?= $especialidad['EspecialidadId'] ?>" <?= $selected ?>><?= $especialidad['Nombre'] ?></option>
                <?php endforeach; ?>
            </select><br>
            
            <button type="submit">Guardar</button>
            <button type="button" onclick="cancelarEdicion()">Cancelar</button>
        </form>
    <?php endif; ?>
</body>
</html>
