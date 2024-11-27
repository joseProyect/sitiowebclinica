<?php
require_once '../conexion/conexion.php';
require_once 'LoginController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'];
    $contraseña = $_POST['contraseña'];

    $loginController = new LoginController();
    $usuario = $loginController->iniciarSesion($codigo, $contraseña);

    if ($usuario) {
        if ($usuario['Estado'] === 'Inactivo') {
            // Usuario inactivo
            $_SESSION['error'] = 'Tu cuenta está inactiva. Contacta al administrador.';
            header('Location: ../presentacion/login.php');
            exit();
        } else {
            // Usuario activo: almacenar datos en sesión
            $_SESSION['UsuarioId'] = $usuario['UsuarioId'];
            $_SESSION['TipoUsuario'] = $usuario['TipoUsuario'];
            $_SESSION['Nombre'] = $usuario['Nombre'];
            $_SESSION['Apellidos'] = $usuario['Ape_Paterno'] . ' ' . $usuario['Ape_Materno'];

            if ($usuario['TipoUsuario'] === 'Doctor') {
                // Obtener DoctorId si es doctor
                $queryDoctor = "SELECT DoctorId FROM Doctores WHERE UsuarioId = :usuarioId";
                $stmtDoctor = Conexion::getInstancia()->getConexion()->prepare($queryDoctor);
                $stmtDoctor->bindParam(':usuarioId', $usuario['UsuarioId'], PDO::PARAM_INT);
                $stmtDoctor->execute();
                $doctor = $stmtDoctor->fetch(PDO::FETCH_ASSOC);
                if ($doctor) {
                    $_SESSION['DoctorId'] = $doctor['DoctorId'];
                } else {
                    $_SESSION['error'] = 'No se pudo encontrar el ID del doctor.';
                    header('Location: ../presentacion/login.php');
                    exit();
                }
            }

            // Redirigir según el tipo de usuario
            switch ($usuario['TipoUsuario']) {
                case 'Administrador':
                    header('Location: ../presentacion/home-administrador.php');
                    break;
                case 'Asistente':
                    header('Location: ../presentacion/home-asistente.php');
                    break;
                case 'Doctor':
                    header('Location: ../presentacion/home-doctor.php');
                    break;
                default:
                    $_SESSION['error'] = 'No tienes permisos para acceder.';
                    header('Location: ../presentacion/login.php');
            }
            exit();
        }
    } else {
        // Credenciales incorrectas
        $_SESSION['error'] = 'Código o contraseña incorrectos.';
        header('Location: ../presentacion/login.php');
        exit();
    }
}
?>