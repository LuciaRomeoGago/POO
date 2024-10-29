<?php
interface ABMinterface {
    //Levanta los datos de la base de datos para volcarlos en una estructura
    public function levantar();
    //Da de alta un elemento en la base de datos y en la estructura
    public function alta();
    //Da de baja un elemento en la base de datos y en la estructura
    public function baja();
    //Modifica un elemento en la base de datos y en la estructura
    public function modificacion();
}