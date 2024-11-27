<?php
session_start();
if (!isset($_SESSION['UsuarioId']) || $_SESSION['TipoUsuario'] !== 'Administrador') {
    header('Location: ../presentacion/login.php');
    exit();
}

require_once '../conexion/conexion.php';

$conexion = Conexion::getInstancia()->getConexion();

if (isset($_GET['id']) && isset($_GET['estado'])) {
    $id = $_GET['id'];
    $nuevoEstado = $_GET['estado'];

    $query = "UPDATE Usuarios SET Estado = :estado WHERE UsuarioId = :id";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':estado', $nuevoEstado);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        header('Location: ../presentacion/adminUsuarios.php');
        exit();
    }
}
?>
