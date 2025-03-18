<?php
    class Veterinaria{
        private $nombre;
        private $direccion;
        private $telefono;


        public function __construct($nombre,$direccion,$telefono){
            $this->nombre=$nombre;
            $this->direccion=$direccion;
            $this->telefono=$telefono;
        }
    }