<?php
require_once ('Menu' . DIRECTORY_SEPARATOR . 'Menu.php');
require_once ('Conexion' . DIRECTORY_SEPARATOR . 'Conexion.php');

// Main
$menu = new Menu();

$menu->cls();
$menu->pantallaBienvenida('Veterinaria Patitas');

$db= Conexion::getConexion();

$menu->operacionesAdmin();

$menu->pantallaDespedida();

$db=Conexion::closeConexion();
