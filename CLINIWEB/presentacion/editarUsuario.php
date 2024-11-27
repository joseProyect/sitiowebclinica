<?php
require_once '../conexion/conexion.php';

$error = "";
$success = "";

// Obtener el usuario desde la base de datos
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $conexion = Conexion::getInstancia()->getConexion();
        $sql = "SELECT * FROM Usuarios WHERE UsuarioId = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $error = "Usuario no encontrado.";
        }
    } catch (PDOException $e) {
        $error = "Error al obtener los datos del usuario: " . $e->getMessage();
    }
}

// Actualizar el usuario si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
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

    if (empty($codigo) || empty($tipoUsuario) || empty($nombre) || empty($ape_paterno) || empty($ape_materno)) {
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
            if (!empty($contraseña)) {
                $sql = "UPDATE Usuarios SET 
                        Codigo = :codigo, Contraseña = :contraseña, TipoUsuario = :tipoUsuario, 
                        Nombre = :nombre, Ape_Paterno = :ape_paterno, Ape_Materno = :ape_materno, 
                        DNI = :dni, Telefono = :telefono, Direccion = :direccion, Estado = :estado 
                        WHERE UsuarioId = :id";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':contraseña', $contraseña, PDO::PARAM_STR);
            } else {
                $sql = "UPDATE Usuarios SET 
                        Codigo = :codigo, TipoUsuario = :tipoUsuario, 
                        Nombre = :nombre, Ape_Paterno = :ape_paterno, Ape_Materno = :ape_materno, 
                        DNI = :dni, Telefono = :telefono, Direccion = :direccion, Estado = :estado 
                        WHERE UsuarioId = :id";
                $stmt = $conexion->prepare($sql);
            }

            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_INT);
            $stmt->bindParam(':tipoUsuario', $tipoUsuario, PDO::PARAM_STR);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':ape_paterno', $ape_paterno, PDO::PARAM_STR);
            $stmt->bindParam(':ape_materno', $ape_materno, PDO::PARAM_STR);
            $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $success = "Usuario actualizado exitosamente.";
            header("Location: adminUsuarios.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error al actualizar el usuario: " . $e->getMessage();
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
    <title>Editar Usuario</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-header">
            <button class="close-btn" onclick="confirmExit()">&times;</button>
        </div>
        <h1>Editar Usuario</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['UsuarioId']); ?>">
            <label for="codigo">Código *</label>
            <input type="number" name="codigo" id="codigo" value="<?php echo htmlspecialchars($usuario['Codigo']); ?>" required>

            <label for="contraseña">Contraseña (opcional)</label>
            <input type="password" name="contraseña" id="contraseña" placeholder="Ingrese una nueva contraseña (opcional)">

            <label for="tipo_usuario">Tipo de Usuario *</label>
            <select name="tipo_usuario" id="tipo_usuario" required>
                <option value="Administrador" <?php echo $usuario['TipoUsuario'] === 'Administrador' ? 'selected' : ''; ?>>Administrador</option>
                <option value="Asistente" <?php echo $usuario['TipoUsuario'] === 'Asistente' ? 'selected' : ''; ?>>Asistente</option>
                <option value="Doctor" <?php echo $usuario['TipoUsuario'] === 'Doctor' ? 'selected' : ''; ?>>Doctor</option>
            </select>

            <label for="nombre">Nombre *</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($usuario['Nombre']); ?>" required>

            <label for="ape_paterno">Apellido Paterno *</label>
            <input type="text" name="ape_paterno" id="ape_paterno" value="<?php echo htmlspecialchars($usuario['Ape_Paterno']); ?>" required>

            <label for="ape_materno">Apellido Materno *</label>
            <input type="text" name="ape_materno" id="ape_materno" value="<?php echo htmlspecialchars($usuario['Ape_Materno']); ?>" required>

            <label for="dni">DNI *</label>
            <input type="text" name="dni" id="dni" maxlength="8" value="<?php echo htmlspecialchars($usuario['DNI']); ?>" required>

            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" maxlength="9" value="<?php echo htmlspecialchars($usuario['Telefono']); ?>">

            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="<?php echo htmlspecialchars($usuario['Direccion']); ?>">

            <label for="estado">Estado *</label>
            <select name="estado" id="estado" required>
                <option value="Activo" <?php echo $usuario['Estado'] === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                <option value="Inactivo" <?php echo $usuario['Estado'] === 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
            </select>

            <button type="submit">Guardar Cambios</button>
        </form>
    </div>

    <script>
        function confirmExit() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No se guardarán los cambios realizados.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'adminUsuarios.php';
                }
            });
        }
    </script>
</body>
</html>
