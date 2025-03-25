<?php
class Producto{
    private $id; 
    private $nombre;
    private $precio;
    private $stock;

    public function __construct($id, $nombre, $precio, $stock)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->stock = $stock;
    }

    // Getters, permiten acceder a las propiedades privadas de la clase sin que sus valores se modifiquen directamente
    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function getStock()
    {
        return $this->stock;
    }

    //Setters, permiten modificar las propiedades, algunas retornan $this para permitir encadenar llamadas
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    public function setStock($stock)
    {
        $this->stock = $stock;
    }

      //Muestra por pantalla producto
      public function mostrar()
      {
          echo "Id: " . $this->getId()
              . ", Nombre: " . $this->getNombre()
              . ", Precio: " . $this->getPrecio()
              . ", Stock: " . $this->getStock()
              . PHP_EOL;
      }
  
    }