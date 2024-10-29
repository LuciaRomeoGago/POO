<?php
    class Veterinaria{
        private $nombre;
        private $direccion;
        private $telefono;
        //existe un nro que identifica el local especifico, se llama RUC, lo ponemos?

        public function __construct($nombre,$direccion,$telefono){
            $this->nombre=$nombre;
            $this->direccion=$direccion;
            $this->telefono=$telefono;
        }
    }