<?php
class Veterinario
{
    private $nombre;
    private $especialidad;
    private $id; // Se usa  después de guardar

    public function __construct($nombre, $especialidad, $id = null)
    {
        $this->nombre = $nombre;
        $this->especialidad = $especialidad;
        $this->id = $id; // O se asigna si se proporciona
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


    // Método para mostrar información del veterinario
    public function mostrar()
    {
        echo "ID: " . htmlspecialchars($this->getId())
            . ", Nombre: " . htmlspecialchars($this->getNombre())
            . ", Especialidad: " . htmlspecialchars($this->getEspecialidad())
            . PHP_EOL;
    }
}
