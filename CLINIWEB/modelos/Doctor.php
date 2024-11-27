<?php

class Doctor {
    private $doctorId;
    private $usuarioId;
    private $nombre;
    private $apePaterno;
    private $apeMaterno;
    private $telefono;
    private $direccion;
    private $especialidadId;
    private $especialidadNombre;

    // Constructor
    public function __construct($doctorId, $usuarioId, $nombre, $apePaterno, $apeMaterno, $telefono, $direccion, $especialidadId, $especialidadNombre) {
        $this->doctorId = $doctorId;
        $this->usuarioId = $usuarioId;
        $this->nombre = $nombre;
        $this->apePaterno = $apePaterno;
        $this->apeMaterno = $apeMaterno;
        $this->telefono = $telefono;
        $this->direccion = $direccion;
        $this->especialidadId = $especialidadId;
        $this->especialidadNombre = $especialidadNombre;
    }

    // Métodos getters
    public function getDoctorId() {
        return $this->doctorId;
    }

    public function getUsuarioId() {
        return $this->usuarioId;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApePaterno() {
        return $this->apePaterno;
    }

    public function getApeMaterno() {
        return $this->apeMaterno;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function getEspecialidadId() {
        return $this->especialidadId;
    }

    public function getEspecialidadNombre() {
        return $this->especialidadNombre;
    }

    // Métodos setters
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApePaterno($apePaterno) {
        $this->apePaterno = $apePaterno;
    }

    public function setApeMaterno($apeMaterno) {
        $this->apeMaterno = $apeMaterno;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    public function setEspecialidadId($especialidadId) {
        $this->especialidadId = $especialidadId;
    }

    public function setEspecialidadNombre($especialidadNombre) {
        $this->especialidadNombre = $especialidadNombre;
    }
}
?>
