<?php
include_once "../dao/doctorDAO.php";

$doctorDAO = new DoctorDAO();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'agregar':
                $doctorDAO->agregarDoctor($_POST['codigo'], $_POST['contraseña'], $_POST['nombre'], $_POST['ape_paterno'], $_POST['ape_materno'], $_POST['dni'], $_POST['telefono'], $_POST['direccion'], $_POST['especialidadId']);
                break;
            case 'editar':
                $doctorDAO->editarDoctor($_POST['doctorId'], $_POST['nombre'], $_POST['ape_paterno'], $_POST['ape_materno'], $_POST['telefono'], $_POST['direccion'], $_POST['especialidadId']);
                break;
        }
    }
}
?>
