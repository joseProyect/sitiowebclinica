<?php
session_start();
if (!isset($_SESSION['UsuarioId']) || $_SESSION['TipoUsuario'] !== 'Administrador') {
    $_SESSION['error'] = "No tienes permisos para acceder a esta página.";
    header('Location: login.php');
    exit();
}

require_once '../conexion/conexion.php';

$conexion = Conexion::getInstancia()->getConexion();

// Mensaje de error o notificación (ejemplo: mensaje desde la URL)
if (isset($_GET['error'])) {
    $mensaje = htmlspecialchars($_GET['error']);
} elseif (isset($_GET['success'])) {
    $mensaje = htmlspecialchars($_GET['success']);
} else {
    $mensaje = null;
}

// Obtener todos los usuarios
$query = "SELECT * FROM Usuarios";
$stmt = $conexion->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/adminUsuarios.css">
</head>
<body>
    <div class="container">
        <!-- Cuadro de alerta retro -->
        <div class="alert-container" id="alertContainer" style="display: <?php echo $mensaje ? 'block' : 'none'; ?>;">
            <div class="alert-box">
                <span class="alert-message"><?php echo $mensaje; ?></span>
                <button class="alert-close" onclick="closeAlert()">×</button>
            </div>
        </div>

        <!-- Cuadro de confirmación retro -->
        <div class="confirm-container" id="confirmContainer" style="display: none;">
            <div class="confirm-box">
                <p class="confirm-message">¿Deseas guardar los cambios antes de salir?</p>
                <div class="confirm-buttons">
                    <button class="confirm-button" id="confirmYes" onclick="guardarCambios()">Aceptar</button>
                    <button class="confirm-button cancel" id="confirmNo" onclick="cancelar()">Cancelar</button>
                </div>
            </div>
        </div>

        <!-- Cuadro de mensaje personalizado -->
        <div class="message-container" id="messageContainer" style="display: none;">
            <div class="message-box">
                <p class="message-content" id="messageContent">Mensaje predeterminado</p>
                <button class="message-button" onclick="closeMessage()">Aceptar</button>
            </div>
        </div>

        <!-- Botón de cierre -->
        <div class="close-button" onclick="showConfirmDialog()">×</div>
        
        <!-- Título y botón Agregar Usuario -->
        <div class="header">
            <h1>Administrar Usuarios</h1>
            <button class="add-user-button" onclick="location.href='agregarUsuario.php'">
                <i class="fa fa-user-plus"></i> Agregar Usuario
            </button>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Tipo de Usuario</th>
                    <th>Estado</th>
                    <th>Editar</th>
                    <th>Cambiar Estado</th>
                    <th>Ver Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td data-label="Código"><?php echo htmlspecialchars($usuario['Codigo']); ?></td>
                    <td data-label="Nombre"><?php echo htmlspecialchars($usuario['Nombre']) . ' ' . htmlspecialchars($usuario['Ape_Paterno']) . ' ' . htmlspecialchars($usuario['Ape_Materno']); ?></td>
                    <td data-label="Tipo de Usuario"><?php echo htmlspecialchars($usuario['TipoUsuario']); ?></td>
                    <td data-label="Estado">
                        <span class="<?php echo $usuario['Estado'] === 'Activo' ? 'active' : 'inactive'; ?>">
                            <?php echo htmlspecialchars($usuario['Estado']); ?>
                        </span>
                    </td>
                    <!-- Columna Editar -->
                    <td data-label="Editar">
                        <a href="editarUsuario.php?id=<?php echo $usuario['UsuarioId']; ?>" title="Editar">
                            <i class="fa fa-edit" style="color: #40E0D0;"></i>
                        </a>
                    </td>
                    <!-- Columna Cambiar Estado -->
                    <td data-label="Cambiar Estado">
                        <a href="../controladores/cambiarEstadoUsuario.php?id=<?php echo $usuario['UsuarioId']; ?>&estado=<?php echo $usuario['Estado'] === 'Activo' ? 'Inactivo' : 'Activo'; ?>" title="Cambiar Estado">
                            <i class="fa fa-exchange-alt" style="color: #40E0D0;"></i>
                        </a>
                    </td>
                    <!-- Columna Ver Detalles -->
                    <td data-label="Ver Detalles">
                        <a href="detalleUsuario.php?id=<?php echo $usuario['UsuarioId']; ?>" title="Ver Detalles">
                            <i class="fa fa-eye" style="color: #40E0D0;"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Función para mostrar el cuadro de confirmación
        function showConfirmDialog() {
            document.getElementById('confirmContainer').style.display = 'flex';
        }

        // Función para cerrar el cuadro de confirmación
        function closeConfirmDialog() {
            document.getElementById('confirmContainer').style.display = 'none';
        }

        // Función para mostrar un mensaje personalizado
        function showMessage(content) {
            document.getElementById('messageContent').textContent = content;
            document.getElementById('messageContainer').style.display = 'flex';
        }

        // Función para cerrar el cuadro de mensaje
        function closeMessage() {
            document.getElementById('messageContainer').style.display = 'none';
        }

        // Función para guardar cambios (redirecciona a otra página)
        function guardarCambios() {
            closeConfirmDialog(); // Cierra el cuadro de confirmación
            showMessage("Guardando los cambios...");
            setTimeout(() => {
                window.location.href = "paginaDestino.php"; // Cambia "paginaDestino.php" por la URL deseada
            }, 2000);
        }

        // Función para cancelar la acción
        function cancelar() {
            closeConfirmDialog(); // Cierra el cuadro de confirmación
            showMessage("No se guardaron los cambios.");
        }

        // Función para cerrar el cuadro de alerta
        function closeAlert() {
            document.getElementById('alertContainer').style.display = 'none';
        }
    </script>
</body>
</html>
