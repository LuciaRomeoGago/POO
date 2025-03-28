<?php
class Veterinario
{
    private $nombre;
    private $especialidad;
    private $id;

    public function __construct($nombre, $especialidad, $id = null)
    {
        $this->nombre = $nombre;
        $this->especialidad = $especialidad;
        $this->id = $id;
    }

    // Getters
    public function getNombre()
    {
        return $this->nombre;
    }

    public function getEspecialidad()
    {
        return $this->especialidad;
    }

    public function getId()
    {
        return $this->id;
    }

    // Setters
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setEspecialidad($especialidad)
    {
        $this->especialidad = $especialidad;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    // Muestra un Veterinario
    public function mostrar()
    {
        echo "ID: " . htmlspecialchars($this->getId())
            . ", Nombre: " . htmlspecialchars($this->getNombre())
            . ", Especialidad: " . htmlspecialchars($this->getEspecialidad())
            . PHP_EOL;
    }
}
