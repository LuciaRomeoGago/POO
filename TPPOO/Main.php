<?php
require_once ('Menu' . DIRECTORY_SEPARATOR . 'Menu.php');
require_once ('Conexion' . DIRECTORY_SEPARATOR . 'Conexion.php');
require_once ('Menu'. DIRECTORY_SEPARATOR . 'MenuAdministrador.php');
// Main
$menu = new Menu();

$menu->cls();
$menu->pantallaBienvenida('Veterinaria Patitas');

$db= Conexion::getConexion();

//Crea isntancia para acceder a sus metodos
$menuAdmin = new MenuAdmin();

//Menu principal para seleccionar el rol (Cliente o Veterinario)
$titulo= 'Seleccione su perfil de ingreso';
$opciones = [
    [0, "Salir del sistema",[$menu, "exit"]],
    [1, "Entrar como Cliente", [$menu, "menuCliente"]],
    [2, "Entrar como Veterinario", [$menu], "menuVeterinario"]
];

//Mostrar el menu de seleccion
$menuAdmin->mostrarMenu($titulo,$opciones);

$menu->pantallaDespedida();

$db=Conexion::closeConexion();
