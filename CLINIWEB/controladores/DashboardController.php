<?php
// controladores/DashboardController.php

require_once '../conexion/conexion.php';

class DashboardController {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::getInstancia()->getConexion();
    }

    public function obtenerDatosDashboard() {
        // Consultas para obtener los totales
        $query_usuarios = "SELECT COUNT(*) AS total FROM usuarios";
        $query_doctores = "SELECT COUNT(*) AS total FROM doctores";
        $query_asistentes = "SELECT COUNT(*) AS total FROM asistentes";
        $query_administradores = "SELECT COUNT(*) AS total FROM administradores";
        $query_pacientes = "SELECT COUNT(*) AS total FROM pacientes";
        $query_citas = "SELECT COUNT(*) AS total FROM citas";

        // Ejecutar las consultas
        $result_usuarios = $this->conexion->query($query_usuarios);
        $result_doctores = $this->conexion->query($query_doctores);
        $result_asistentes = $this->conexion->query($query_asistentes);
        $result_administradores = $this->conexion->query($query_administradores);
        $result_pacientes = $this->conexion->query($query_pacientes);
        $result_citas = $this->conexion->query($query_citas);

        // Obtener los resultados
        $usuarios = $result_usuarios->fetch(PDO::FETCH_ASSOC)['total'];
        $doctores = $result_doctores->fetch(PDO::FETCH_ASSOC)['total'];
        $asistentes = $result_asistentes->fetch(PDO::FETCH_ASSOC)['total'];
        $administradores = $result_administradores->fetch(PDO::FETCH_ASSOC)['total'];
        $pacientes = $result_pacientes->fetch(PDO::FETCH_ASSOC)['total'];
        $citas = $result_citas->fetch(PDO::FETCH_ASSOC)['total'];

        // Devolver los datos en formato JSON
        return json_encode([
            'usuarios' => $usuarios,
            'doctores' => $doctores,
            'asistentes' => $asistentes,
            'administradores' => $administradores,
            'pacientes' => $pacientes,
            'citas' => $citas
        ]);
    }
}

// Instanciar el controlador y devolver los datos
$dashboardController = new DashboardController();
echo $dashboardController->obtenerDatosDashboard();
?>
