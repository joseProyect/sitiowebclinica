<?php
include_once "../conexion/conexion.php";

class HorarioDAO {
    private $pdo;

    public function __construct() {
        // Obtener la conexión a través del Singleton
        $this->pdo = Conexion::getInstancia()->getConexion();
    }

    /**
     * Obtener la lista de doctores
     * @return array - Lista de doctores con sus nombres completos
     */
    public function obtenerDoctores() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT d.DoctorId, u.Nombre, u.Ape_Paterno, u.Ape_Materno
                FROM Doctores d
                INNER JOIN Usuarios u ON d.UsuarioId = u.UsuarioId
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve un array asociativo con los datos
        } catch (PDOException $e) {
            error_log("Error al obtener doctores: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener horarios por doctor
     * @param int $doctorId - ID del doctor
     * @return array - Lista de horarios del doctor
     */
    public function obtenerHorariosPorDoctor($doctorId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT HorarioId, Dia, Hora_Inicio, Hora_Fin
                FROM Horarios
                WHERE DoctorId = ?
                ORDER BY FIELD(Dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'), Hora_Inicio
            ");
            $stmt->execute([$doctorId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener horarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener el nombre completo de un doctor por su ID
     * @param int $doctorId - ID del doctor
     * @return array|null - Datos del doctor o null si no existe
     */
    public function obtenerNombreDoctorPorId($doctorId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT CONCAT(u.Nombre, ' ', u.Ape_Paterno, ' ', u.Ape_Materno) AS NombreCompleto
                FROM Doctores d
                INNER JOIN Usuarios u ON d.UsuarioId = u.UsuarioId
                WHERE d.DoctorId = ?
            ");
            $stmt->execute([$doctorId]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna un único registro o null
        } catch (PDOException $e) {
            error_log("Error al obtener nombre del doctor: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener un horario por su ID
     * @param int $horarioId - ID del horario
     * @return array|null - Datos del horario o null si no existe
     */
    public function obtenerHorarioPorId($horarioId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT HorarioId, DoctorId, Dia, Hora_Inicio, Hora_Fin
                FROM Horarios
                WHERE HorarioId = ?
            ");
            $stmt->execute([$horarioId]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna un único registro o null
        } catch (PDOException $e) {
            error_log("Error al obtener horario por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Agregar un nuevo horario
     * @param int $doctorId - ID del doctor
     * @param string $dia - Día del horario
     * @param string $horaInicio - Hora de inicio del horario
     * @param string $horaFin - Hora de fin del horario
     * @throws Exception - Si hay un conflicto de horario
     */
    public function agregarHorario($doctorId, $dia, $horaInicio, $horaFin) {
        try {
            if ($this->verificarConflictoHorario($doctorId, $dia, $horaInicio, $horaFin)) {
                throw new Exception("El horario se solapa con otro existente.");
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO Horarios (DoctorId, Dia, Hora_Inicio, Hora_Fin)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$doctorId, $dia, $horaInicio, $horaFin]);
        } catch (PDOException $e) {
            error_log("Error al agregar horario: " . $e->getMessage());
            throw new Exception("No se pudo agregar el horario.");
        }
    }

    /**
     * Actualizar un horario existente
     * @param int $horarioId - ID del horario
     * @param int $doctorId - ID del doctor
     * @param string $dia - Día del horario
     * @param string $horaInicio - Hora de inicio del horario
     * @param string $horaFin - Hora de fin del horario
     * @throws Exception - Si hay un conflicto de horario
     */
    public function actualizarHorario($horarioId, $doctorId, $dia, $horaInicio, $horaFin) {
        try {
            if ($this->verificarConflictoHorario($doctorId, $dia, $horaInicio, $horaFin, true, $horarioId)) {
                throw new Exception("El horario se solapa con otro existente.");
            }

            $stmt = $this->pdo->prepare("
                UPDATE Horarios
                SET Dia = ?, Hora_Inicio = ?, Hora_Fin = ?
                WHERE HorarioId = ?
            ");
            $stmt->execute([$dia, $horaInicio, $horaFin, $horarioId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar horario: " . $e->getMessage());
            throw new Exception("No se pudo actualizar el horario.");
        }
    }

    /**
     * Eliminar un horario
     * @param int $horarioId - ID del horario
     * @return bool - True si la eliminación fue exitosa
     */
    public function eliminarHorario($horarioId) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM Horarios
                WHERE HorarioId = ?
            ");
            return $stmt->execute([$horarioId]);
        } catch (PDOException $e) {
            error_log("Error al eliminar horario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si hay conflicto de horarios
     * @param int $doctorId - ID del doctor
     * @param string $dia - Día del horario
     * @param string $horaInicio - Hora de inicio del horario
     * @param string $horaFin - Hora de fin del horario
     * @param bool $excluirActual - Excluir horario actual al verificar (para actualizaciones)
     * @param int|null $horarioId - ID del horario a excluir
     * @return bool - True si hay conflicto, False si no
     */
    private function verificarConflictoHorario($doctorId, $dia, $horaInicio, $horaFin, $excluirActual = false, $horarioId = null) {
        $query = "
            SELECT COUNT(*) AS conflicto
            FROM Horarios
            WHERE DoctorId = ? AND Dia = ? AND (
                (Hora_Inicio < ? AND Hora_Fin > ?) OR
                (Hora_Inicio < ? AND Hora_Fin > ?) OR
                (Hora_Inicio = ? AND Hora_Fin = ?)
            )
        ";

        if ($excluirActual && $horarioId !== null) {
            $query .= " AND HorarioId != ?";
        }

        $stmt = $this->pdo->prepare($query);

        if ($excluirActual && $horarioId !== null) {
            $stmt->execute([$doctorId, $dia, $horaFin, $horaInicio, $horaInicio, $horaFin, $horaInicio, $horaFin, $horarioId]);
        } else {
            $stmt->execute([$doctorId, $dia, $horaFin, $horaInicio, $horaInicio, $horaFin, $horaInicio, $horaFin]);
        }

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['conflicto'] > 0;
    }
}
