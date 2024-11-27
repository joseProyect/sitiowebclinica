<?php
require_once '../conexion/conexion.php';

$conexion = Conexion::getInstancia()->getConexion();

// Obtener el parámetro de búsqueda
$query = $_GET['query'] ?? '';

// Si hay un texto de búsqueda, realizar la consulta
if (!empty($query)) {
    // Escapar los caracteres para evitar inyección SQL
    $query = "%" . $query . "%";
    $sql = "SELECT PacienteId, CONCAT(Nombre, ' ', Ape_Paterno, ' ', Ape_Materno) AS NombreCompleto 
            FROM Pacientes 
            WHERE CONCAT(Nombre, ' ', Ape_Paterno, ' ', Ape_Materno) LIKE :query";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':query', $query, PDO::PARAM_STR);
    $stmt->execute();

    // Devolver los pacientes en formato JSON
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($pacientes);
} else {
    // Si no hay búsqueda, devolver un array vacío
    echo json_encode([]);
}
?>
