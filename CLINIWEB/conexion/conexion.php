<?php

class Conexion {
    private $host = "localhost";
    private $usuario = "root";
    private $password = "";
    private $db = "dbclinic";
    private static $instancia = null;
    private $conexion;

    private function __construct() {
        try {
            $this->conexion = new PDO("mysql:host={$this->host};dbname={$this->db}", $this->usuario, $this->password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Mejora la seguridad de las consultas
        } catch (PDOException $e) {
            die("Error en la conexión a la base de datos: " . $e->getMessage());
        }
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia;
    }

    public function getConexion() {
        return $this->conexion;
    }

    // Evitar la clonación de la instancia
    private function __clone() {}
}

?>
