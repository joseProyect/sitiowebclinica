<?php
include_once "../dao/doctorDAO.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctorId = $_POST['doctorId'];
    $nombre = $_POST['nombre'];
    $ape_paterno = $_POST['ape_paterno'];
    $ape_materno = $_POST['ape_materno'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $especialidadId = $_POST['especialidad'];

    $doctorDAO = new DoctorDAO();
    $doctorDAO->actualizarDoctor($doctorId, $nombre, $ape_paterno, $ape_materno, $telefono, $direccion, $especialidadId);

    header("Location: administrarDoctores.php");
    exit();
}
