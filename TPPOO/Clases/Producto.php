<?php
     class Producto {
        private $codigo;
        private $nombre;
        private $precio;
        private $stock;

        public function __construct($codigo,$nombre,$precio, $stock)
        {
            $this->codigo=$codigo;
            $this->nombre=$nombre;
            $this->precio=$precio;
            $this->stock=$stock;
        }
     }

     