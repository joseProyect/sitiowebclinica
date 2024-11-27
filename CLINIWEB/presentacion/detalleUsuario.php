<?php
session_start();
if (!isset($_SESSION['UsuarioId']) || $_SESSION['TipoUsuario'] !== 'Administrador') {
    header('Location: login.php');
    exit();
}

require_once '../conexion/conexion.php';

$conexion = Conexion::getInstancia()->getConexion();

if (!isset($_GET['id'])) {
    header('Location: adminUsuarios.php?error=No se especificó un usuario.');
    exit();
}

$id = $_GET['id'];

try {
    // Obtener detalles del usuario
    $sql = "SELECT * FROM Usuarios WHERE UsuarioId = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header('Location: adminUsuarios.php?error=Usuario no encontrado.');
        exit();
    }
} catch (PDOException $e) {
    header('Location: adminUsuarios.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Usuario</title>
    <link rel="stylesheet" href="./css/detalleUsuario.css">
</head>
<body>
    <div class="container">
        <h1>Detalle de Usuario</h1>
        <form>
            <fieldset>
                <legend>Información Personal</legend>
                <label for="codigo">Código:</label>
                <input type="text" id="codigo" value="<?php echo htmlspecialchars($usuario['Codigo']); ?>" readonly>

                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" value="<?php echo htmlspecialchars($usuario['Nombre']) . ' ' . htmlspecialchars($usuario['Ape_Paterno']) . ' ' . htmlspecialchars($usuario['Ape_Materno']); ?>" readonly>
            </fieldset>

            <fieldset>
                <legend>Contacto</legend>
                <label for="dni">DNI:</label>
                <input type="text" id="dni" value="<?php echo htmlspecialchars($usuario['DNI']); ?>" readonly>

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" value="<?php echo htmlspecialchars($usuario['Telefono']); ?>" readonly>
            </fieldset>

            <fieldset>
                <legend>Información Adicional</legend>
                <label for="tipoUsuario">Tipo de Usuario:</label>
                <input type="text" id="tipoUsuario" value="<?php echo htmlspecialchars($usuario['TipoUsuario']); ?>" readonly>

                <label for="estado">Estado:</label>
                <input type="text" id="estado" value="<?php echo htmlspecialchars($usuario['Estado']); ?>" readonly>
                
                <label for="direccion">Dirección:</label>
                <textarea id="direccion" readonly><?php echo htmlspecialchars($usuario['Direccion']); ?></textarea>
            </fieldset>

            <div class="buttons">
                <button type="button" class="btn-back" onclick="window.location.href='adminUsuarios.php'">Volver</button>
                <button type="button" class="btn-edit" onclick="window.location.href='editarUsuario.php?id=<?php echo $usuario['UsuarioId']; ?>'">Editar</button>
            </div>
        </form>
    </div>
</body>
</html>
