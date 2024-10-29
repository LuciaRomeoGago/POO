<?php
    class Turno {
        private $fecha;
        private $hora;
        private $estado; //disponible u ocupado
// aca no volvemos a poner a mascota y cliente, porque usariamos los datos de los otros objetos al reservar un turno?
        public function __construct($fecha,$hora,$estado){
            $this->fecha=$fecha;
            $this->hora=$hora;
            $this->estado=$estado;
        }
    }