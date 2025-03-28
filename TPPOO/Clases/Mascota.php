<?php
class Mascota
{
    private $nombre;
    private $edad;
    private $raza;
    private $id;
    private $historialMedico;
    private $clienteId;

    public function __construct($nombre, $edad, $raza, $historialMedico)
    {
        $this->nombre = $nombre;
        $this->edad = $edad;
        $this->raza = $raza;
        $this->id = null;
        $this->historialMedico = $historialMedico;
        $this->clienteId = null; 
    }

    // Getters, para obtener el valor de la propiedad
    public function getNombre()
    {
        return $this->nombre;
    }

    public function getEdad()
    {
        return $this->edad;
    }

    public function getRaza()
    {
        return $this->raza;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getClienteId()
    {
        return $this->clienteId;
    }

    public function getHistorialMedico()
    {
        return $this->historialMedico;
    }

    //Setters, establece el valor de la propiedad
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setEdad($edad)
    {
        $this->edad = $edad;
    }

    public function setRaza($raza)
    {
        $this->raza = $raza;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setHistorialMedico($historialMedico)
    {
        $this->historialMedico = $historialMedico;
    }

    public function setClienteId($clienteId)
    {
        $this->clienteId = $clienteId;
    }

    // Muestra la Mascota
    public function mostrar()
    {
        MascotaModelo::actualizarMascota($this);
        echo "Nombre: " . htmlspecialchars($this->getNombre())
            . ", Edad: " . htmlspecialchars($this->getEdad())
            . ", Raza: " . htmlspecialchars($this->getRaza())
            . ", Id: " . (empty($this->getId()) ? 'No asignado' : $this->getId())
            . ", Historial Médico: " . htmlspecialchars($this->getHistorialMedico())
            . PHP_EOL;
    }
}
