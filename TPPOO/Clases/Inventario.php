<?php
class Inventario{
    private $producto;
    private $cantidad;

    public function __construct(Producto $producto, $cantidad) {
        $this->producto = $producto;
        $this->cantidad = $cantidad;
    }

    public function getProducto(){
        return $this->producto;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    public function getId(){
        return $this->producto->getId();
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }
}