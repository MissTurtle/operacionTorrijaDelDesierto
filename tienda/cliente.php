<?php
class Cliente {
    private $dni;
    private $nombre;
    private $direccion;
    private $localidad;
    private $provincia;
    private $telefono;
    private $email;
    private $contrasenya;
    private $rol;
    private $activo;

    public function __construct($dni, $nombre, $direccion, $localidad, $provincia, $telefono, $email, $contrasenya, $rol, $activo) {
        $this->dni = $dni;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->localidad = $localidad;
        $this->provincia = $provincia;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->contrasenya = $contrasenya;
        $this->rol = $rol;
        $this->activo = $activo;
    }

    //Accedo a los atributos
    public function getDNI() {
        return $this->dni;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function getLocalidad() {
        return $this->localidad;
    }

    public function getProvincia() {
        return $this->provincia;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getContrasenya() {
        return $this->contrasenya;
    }

    public function getRol() {
        return $this->rol;
    }
    public function getActivo() {
        return $this->activo;
    }
}
?>