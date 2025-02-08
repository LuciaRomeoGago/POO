<?php
require_once ('Menu' . DIRECTORY_SEPARATOR . 'Menu.php');
require_once ('Conexion' . DIRECTORY_SEPARATOR . 'Conexion.php');
require_once ('Menu'. DIRECTORY_SEPARATOR . 'MenuAdministrador.php');
/*// Main
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
*/
$menu = new Menu();
$clienteManager = new ClienteManager();

while (true) {
    $menu->displayMainMenu();
    
    $opcion = Menu::readln("Seleccione una opción: ");
    
    switch ($opcion) {
        case '1':
            // Veterinarian Menu
            while (true) {
                $menu->displayVeterinarianMenu();
                $veterinarianOption = Menu::readln("Seleccione una opción: ");
                
                switch ($veterinarianOption) {
                    case '1':
                        // Logic to add a pet (implement this as needed)
                        break;
                    case '2':
                        // Logic to modify a pet (implement this as needed)
                        break;
                    case '3':
                        // Logic to delete a pet (implement this as needed)
                        break;
                    case '4':
                        // Logic to show pets (implement this as needed)
                        break;
                    case '5':
                        break 2; // Exit to main menu
                    default:
                        Menu::writeln("Opción no válida. Intente de nuevo.");
                }
            }
            break;

        case '2':
            // Client Menu
            while (true) {
                $menu->displayClientMenu();
                $clientOption = Menu::readln("Seleccione una opción: ");
                
                switch ($clientOption) {
                    case '1':
                        $clienteManager->alta(); // Create client
                        break;
                    case '2':
                        $clienteManager->modificarCliente(); // Modify client
                        break;
                    case '3':
                        $clienteManager->baja(); // Delete client
                        break;
                    case '4':
                        $clienteManager->mostrar(); // Show clients
                        break;
                    case '5':
                        break 2; // Exit to main menu
                    default:
                        Menu::writeln("Opción no válida. Intente de nuevo.");
                }
            }
            break;

        case '3':
            exit("Saliendo del sistema.\n");

        default:
            Menu::writeln("Opción no válida. Intente de nuevo.");
    }
}