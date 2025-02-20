<?php
require_once ('Menu' . DIRECTORY_SEPARATOR . 'Menu.php');
require_once ('Conexion' . DIRECTORY_SEPARATOR . 'Conexion.php');
require_once ('Menu'. DIRECTORY_SEPARATOR . 'MenuAdministrador.php');
// QUIERO QUE FUNCIONE SOLO CON ESTO ASOCIADO A MENUADMIN
// Main
$db= Conexion::getConexion();


//Crea una instancia para acceder a sus metodos
$menuAdmin = new MenuAdmin();

//Menu principal para seleccionar el rol (Cliente o Veterinario)
$titulo= 'Seleccione su perfil de ingreso';

$opciones = [
    [0, "Salir del sistema",[$menuAdmin, "exit"]],
    [1, "Entrar como Cliente", [$menuAdmin, "menuCliente"]],
    [2, "Entrar como Veterinario", [$menuAdmin], "menuVeterinario"]
];

//Mostrar el menu de seleccion
$menuAdmin->mostrarMenu($titulo,$opciones);

$menu->pantallaDespedida();

$db=Conexion::closeConexion();
/*
$menu = new Menu();
$clienteManager = new ClienteManager();

while (true) {
    $menu->displayMainMenu();
    
    $opcion = Menu::readln("Seleccione una opción: ");
    
    switch ($opcion) {
        case '1':
            // Menu del veterinario
            while (true) {
                $menu->displayVeterinarianMenu();
                $opcionVeterinario = Menu::readln("Seleccione una opción: ");
                
                switch ($opcionVeterinario) {
                    case '1':
                        $VeterinarioManager->altaMascota(); // crea la mascota
                        break;
                    case '2':
                        $MascotaManager->modificar();// modifica la mascota, podria mandarlo al veterinariomanager, no?
                        break;
                    case '3':
                        $MascotaManager->baja();// Logic to delete a pet (implement this as needed)
                        break;
                    case '4':
                        $VeterinarioManager->mostrarMascotas;// Logic to show pets (implement this as needed)
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
*/