<?php
require_once '../conexion/conexion.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo'];
    $contraseña = $_POST['contraseña'];
    $tipoUsuario = $_POST['tipo_usuario'];
    $nombre = $_POST['nombre'];
    $ape_paterno = $_POST['ape_paterno'];
    $ape_materno = $_POST['ape_materno'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $estado = $_POST['estado'];

    if (empty($codigo) || empty($contraseña) || empty($tipoUsuario) || empty($nombre) || empty($ape_paterno) || empty($ape_materno) || empty($dni)) {
        $error = "Todos los campos marcados con * son obligatorios.";
    } elseif (!is_numeric($codigo)) {
        $error = "El campo 'Código' debe ser un número válido.";
    } elseif (!is_numeric($dni) || strlen($dni) != 8) {
        $error = "El campo 'DNI' debe contener exactamente 8 dígitos.";
    } elseif (!empty($telefono) && (!is_numeric($telefono) || strlen($telefono) != 9)) {
        $error = "El campo 'Teléfono' debe contener exactamente 9 dígitos numéricos.";
    } else {
        try {
            $conexion = Conexion::getInstancia()->getConexion();
            $sql = "INSERT INTO Usuarios (Codigo, Contraseña, TipoUsuario, Nombre, Ape_Paterno, Ape_Materno, DNI, Telefono, Direccion, Estado) 
                    VALUES ('$codigo', '$contraseña', '$tipoUsuario', '$nombre', '$ape_paterno', '$ape_materno', '$dni', '$telefono', '$direccion', '$estado')";
            $conexion->exec($sql);
            $success = "Usuario agregado exitosamente.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "El código de usuario '$codigo' ya está registrado. Por favor, elija otro.";
            } else {
                $error = "Error al agregar el usuario: " . $e->getMessage();
            }
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
    <title>Agregar Usuario</title>
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
                    window.location.href = "adminUsuarios.php";
                }
            });
        }

        function validateInput(event, maxLength) {
            if (event.target.value.length >= maxLength) {
                event.preventDefault();
            }
        }

        function allowNumbersOnly(event) {
            const charCode = event.which ? event.which : event.keyCode;
            if (charCode < 48 || charCode > 57) {
                event.preventDefault();
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <div class="form-header">
            <button class="close-btn" onclick="confirmExit()">&times;</button>
        </div>
        <h1>Agregar Usuario</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="codigo">Código *</label>
            <input type="number" name="codigo" id="codigo" required>

            <label for="contraseña">Contraseña *</label>
            <input type="password" name="contraseña" id="contraseña" required>

            <label for="tipo_usuario">Tipo de Usuario *</label>
            <select name="tipo_usuario" id="tipo_usuario" required>
                <option value="Administrador">Administrador</option>
                <option value="Asistente">Asistente</option>
                <option value="Doctor">Doctor</option>
            </select>

            <label for="nombre">Nombre *</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="ape_paterno">Apellido Paterno *</label>
            <input type="text" name="ape_paterno" id="ape_paterno" required>

            <label for="ape_materno">Apellido Materno *</label>
            <input type="text" name="ape_materno" id="ape_materno" required>

            <label for="dni">DNI *</label>
            <input type="text" name="dni" id="dni" maxlength="8" required onkeypress="allowNumbersOnly(event)" oninput="validateInput(event, 8)">

            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" maxlength="9" onkeypress="allowNumbersOnly(event)" oninput="validateInput(event, 9)">

            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion">

            <label for="estado">Estado</label>
            <select name="estado" id="estado">
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
            </select>

            <button type="submit">Agregar Usuario</button>
        </form>
    </div>
</body>
</html>
