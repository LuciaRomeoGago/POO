<?php
class Cliente
{
    private $nombre;
    private $dni;
    private $id;
    private $referenciaAnimal = [];
    private $inventario = [];

    public function __construct($nombre, $dni, $id = null)
    {
        $this->nombre = $nombre;
        $this->dni = $dni;
        $this->id = $id;
        $this->referenciaAnimal = []; //array, almacena las mascotas asociadas al cliente
    }

    // Getters, permiten acceder a las propiedades privadas de la clase sin que sus valores se modifiquen directamente
    public function getNombre()
    {
        return $this->nombre;
    }

    public function getDni()
    {
        return $this->dni;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getReferenciaAnimal()
    {
        return $this->referenciaAnimal;
    }

    public function getInventario()
    {
        return $this->inventario;
    }

    // Setters, permiten modificar las propiedades, algunas retornan $this para permitir encadenar llamadas
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setDni($dni)
    {
        $this->dni = $dni;
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setMascotas($mascotas)
    { //establece las mascotas asociadas al cliente si es necesario
        $this->referenciaAnimal = $mascotas;
    }

    public function setInventario($inventario)
    {
        $this->inventario = $inventario;
    }

   ///////////////// // Agregar una mascota, establece el id de cliente, y permite agregar una instancia de mascota a este
    public function agregarMascota(Mascota $mascota)
    {
        $mascota->setClienteId($this->getId());
        $this->referenciaAnimal[] = $mascota;
    }

    // uso internoMostrar todas las mascotas del cliente, verifica si hay mascotas y las imprime con el metodo mostrar() de c/mascota
    public function mostrarMascotas()
    {
        if (empty($this->referenciaAnimal)) {
            echo "Este cliente no tiene mascotas." . PHP_EOL;
            return;
        }

        echo "Mascotas del cliente " . htmlspecialchars($this->getNombre()) . ":" . PHP_EOL;
        foreach ($this->referenciaAnimal as $mascota) {
            echo "- ";
            $mascota->mostrar();
        }
    }

    //Muestra por pantalla un cliente y llama para mostrar sus mascotas
    public function mostrar()
    {
        echo "Id: " . $this->getId()
            . ", Nombre: " . $this->getNombre()
            . ", Dni: " . $this->getDni()
            . PHP_EOL;
        $this->mostrarMascotas();
    }
}
