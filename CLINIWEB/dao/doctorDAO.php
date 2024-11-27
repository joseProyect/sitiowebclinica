<?php
include_once "../conexion/Conexion.php";

class DoctorDAO {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::getInstancia()->getConexion();
    }

    // Obtener todos los doctores con su información asociada
    public function obtenerDoctores() {
        try {
            $query = $this->conexion->prepare("
                SELECT 
                    d.DoctorId,
                    u.Nombre,
                    u.Ape_Paterno,
                    u.Ape_Materno,
                    u.Telefono,
                    u.Direccion,
                    e.Nombre AS Especialidad
                FROM Doctores d
                JOIN Usuarios u ON d.UsuarioId = u.UsuarioId
                JOIN Especialidades e ON d.EspecialidadId = e.EspecialidadId
            ");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener doctores: " . $e->getMessage());
        }
    }

    // Obtener un doctor específico por ID
    public function obtenerDoctorPorId($doctorId) {
        try {
            $query = $this->conexion->prepare("
                SELECT 
                    d.DoctorId,
                    u.Nombre,
                    u.Ape_Paterno,
                    u.Ape_Materno,
                    u.Telefono,
                    u.Direccion,
                    e.Nombre AS Especialidad,
                    e.EspecialidadId
                FROM Doctores d
                JOIN Usuarios u ON d.UsuarioId = u.UsuarioId
                JOIN Especialidades e ON d.EspecialidadId = e.EspecialidadId
                WHERE d.DoctorId = :doctorId
            ");
            $query->bindParam(':doctorId', $doctorId, PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener el doctor: " . $e->getMessage());
        }
    }

    // Actualizar la información de un doctor
    public function actualizarDoctor($doctorId, $nombre, $ape_paterno, $ape_materno, $telefono, $direccion, $especialidadId) {
        try {
            $query = $this->conexion->prepare("
                UPDATE Usuarios u
                JOIN Doctores d ON u.UsuarioId = d.UsuarioId
                SET 
                    u.Nombre = :nombre,
                    u.Ape_Paterno = :ape_paterno,
                    u.Ape_Materno = :ape_materno,
                    u.Telefono = :telefono,
                    u.Direccion = :direccion,
                    d.EspecialidadId = :especialidadId
                WHERE d.DoctorId = :doctorId
            ");
            $query->bindParam(':nombre', $nombre);
            $query->bindParam(':ape_paterno', $ape_paterno);
            $query->bindParam(':ape_materno', $ape_materno);
            $query->bindParam(':telefono', $telefono);
            $query->bindParam(':direccion', $direccion);
            $query->bindParam(':especialidadId', $especialidadId, PDO::PARAM_INT);
            $query->bindParam(':doctorId', $doctorId, PDO::PARAM_INT);
            $query->execute();
        } catch (PDOException $e) {
            die("Error al actualizar el doctor: " . $e->getMessage());
        }
    }

    // Obtener todas las especialidades para el formulario
    public function obtenerEspecialidades() {
        try {
            $query = $this->conexion->prepare("SELECT EspecialidadId, Nombre FROM Especialidades");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener especialidades: " . $e->getMessage());
        }
    }

    // Agregar un nuevo doctor
    public function agregarDoctor($usuarioId, $especialidadId) {
        try {
            $query = $this->conexion->prepare("
                INSERT INTO Doctores (UsuarioId, EspecialidadId)
                VALUES (:usuarioId, :especialidadId)
            ");
            $query->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $query->bindParam(':especialidadId', $especialidadId, PDO::PARAM_INT);
            $query->execute();
        } catch (PDOException $e) {
            die("Error al agregar doctor: " . $e->getMessage());
        }
    }

    // Eliminar un doctor
    public function eliminarDoctor($doctorId) {
        try {
            $query = $this->conexion->prepare("DELETE FROM Doctores WHERE DoctorId = :doctorId");
            $query->bindParam(':doctorId', $doctorId, PDO::PARAM_INT);
            $query->execute();
        } catch (PDOException $e) {
            die("Error al eliminar doctor: " . $e->getMessage());
        }
    }
}
