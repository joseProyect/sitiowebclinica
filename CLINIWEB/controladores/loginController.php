<?php
require_once '../conexion/conexion.php';

class LoginController {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::getInstancia()->getConexion();
    }

    public function iniciarSesion($codigo, $contraseña) {
        $query = "SELECT * FROM Usuarios WHERE Codigo = :codigo AND Estado = 'Activo'";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':codigo', $codigo);

        if ($stmt->execute()) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Recupera al usuario como arreglo asociativo
            if ($usuario && hash('sha256', $contraseña) === $usuario['Contraseña']) {
                return $usuario; // Devuelve el arreglo completo
            }
        }
        return false; // Retorna false si no hay coincidencia
    }
}
?>
