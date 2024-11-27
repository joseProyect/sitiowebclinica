<?php
require_once '../conexion/conexion.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $ape_paterno = $_POST['ape_paterno'];
    $ape_materno = $_POST['ape_materno'];
    $dni = $_POST['dni'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $sexo = $_POST['sexo'];
    $estado = $_POST['estado'];

    // Validaciones básicas
    if (empty($nombre) || empty($ape_paterno) || empty($ape_materno) || empty($dni) || empty($fecha_nacimiento) || empty($sexo)) {
        $error = "Todos los campos marcados con * son obligatorios.";
    } elseif (!is_numeric($dni) || strlen($dni) !== 8) {
        $error = "El DNI debe contener exactamente 8 dígitos numéricos.";
    } elseif (!empty($telefono) && (!is_numeric($telefono) || strlen($telefono) !== 9)) {
        $error = "El teléfono debe contener exactamente 9 dígitos numéricos.";
    } else {
        try {
            $conexion = Conexion::getInstancia()->getConexion();

            $sql = "INSERT INTO Pacientes (Nombre, Ape_Paterno, Ape_Materno, DNI, Fecha_Nacimiento, Telefono, Direccion, Sexo, Estado)
                    VALUES (:nombre, :ape_paterno, :ape_materno, :dni, :fecha_nacimiento, :telefono, :direccion, :sexo, :estado)";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':ape_paterno', $ape_paterno, PDO::PARAM_STR);
            $stmt->bindParam(':ape_materno', $ape_materno, PDO::PARAM_STR);
            $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento, PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $stmt->bindParam(':sexo', $sexo, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);

            $stmt->execute();
            $success = "Paciente agregado exitosamente.";
        } catch (PDOException $e) {
            $error = "Error al agregar el paciente: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/agregarUsuario.css">
    <title>Agregar Paciente</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <div class="form-header">
            <button class="close-btn" onclick="confirmExit()">&times;</button>
        </div>
        <h1>Agregar Paciente</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="nombre">Nombre *</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="ape_paterno">Apellido Paterno *</label>
            <input type="text" name="ape_paterno" id="ape_paterno" required>

            <label for="ape_materno">Apellido Materno *</label>
            <input type="text" name="ape_materno" id="ape_materno" required>

            <label for="dni">DNI *</label>
            <input type="text" name="dni" id="dni" maxlength="8" required onkeypress="allowNumbersOnly(event)">

            <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>

            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" maxlength="9" onkeypress="allowNumbersOnly(event)">

            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion">

            <label for="sexo">Sexo *</label>
            <select name="sexo" id="sexo" required>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
            </select>

            <label for="estado">Estado *</label>
            <select name="estado" id="estado" required>
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
            </select>

            <button type="submit">Agregar Paciente</button>
        </form>
    </div>
    <script>
        function confirmExit() {
            Swal.fire({
                title: "¿Estás seguro de que deseas salir?",
                text: "Todos los cambios no guardados se perderán.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, salir",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "adminPacientes.php";
                }
            });
        }

        function allowNumbersOnly(event) {
            const charCode = event.which ? event.which : event.keyCode;
            if (charCode < 48 || charCode > 57) {
                event.preventDefault();
            }
        }
    </script>
</body>
</html>
