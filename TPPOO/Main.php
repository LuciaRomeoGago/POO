<?php
require_once('Menu' . DIRECTORY_SEPARATOR . 'Menu.php');
require_once('Menu' . DIRECTORY_SEPARATOR . 'MenuAdministrador.php');
require_once('Conexion' . DIRECTORY_SEPARATOR . 'Conexion.php');

$db = Conexion::getConexion();

//Crea una instancia para acceder a sus metodos
$menuAdmin = new MenuAdmin();

$menuAdmin->menuPrincipal();
$menuAdmin->pantallaDespedida();

$db = Conexion::closeConexion();