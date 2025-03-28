<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ClienteModelo.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Mascota.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaModelo.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Inventario.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'InventarioModelo.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'InventarioManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Producto.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ProductoModelo.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'ABMinterface.php');
require_once('Menu' . DIRECTORY_SEPARATOR . 'Menu.php');


class ClienteManager extends arrayIdManager implements ABMinterface
{
    public function __construct()
    {
        $this->levantar(); //llama al metodo levantar para cargar clientes desde db al crear una isntancia de clientemanager
    }

    // Levanta los clientes y los agrega al arreglo para manipularlos
    public function levantar()
    {
        try {
            $clientesData = ClienteModelo::obtenerTodos();
            if (empty($clientesData)) {
                echo "No se encontraron clientes en la base de datos." . PHP_EOL;
                return;
            }

            foreach ($clientesData as $clienteData) {
                if (!array_key_exists('id', $clienteData)) {
                    echo "Advertencia: No se encontró la clave 'id' para un cliente." . PHP_EOL;
                    continue;
                }

                $cliente = new Cliente(
                    $clienteData['nombre'],
                    $clienteData['dni'],
                    $clienteData['id']
                );

                $cliente->setMascotas(MascotaModelo::getPorClienteId($cliente->getId()));

                // Cargar el inventario del cliente
                $inventario = new InventarioManager($cliente);
                $cliente->setInventario($inventario->getArreglo());
                $this->agregar($cliente);
            }
        } catch (PDOException $e) {
            echo "Error al levantar clientes: " . htmlspecialchars($e->getMessage());
        }
    }

    // Crea un Cliente
    public function alta()
    {
        $nombre = Menu::readln("Ingrese el nombre y apellido del cliente: ");
        if (!preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
            echo "Error: El nombre debe contener solo letras." . PHP_EOL;
            return;
        }
        $dni = Menu::readln("Ingrese el dni del cliente: ");
        if (!preg_match('/^[0-9]+$/', $dni) || strlen($dni) != 8) {
            echo "Error: El DNI debe ser numérico y tener 8 dígitos." . PHP_EOL;
           return;
        }

        $modelo = new ClienteModelo();
        if ($modelo->existeDni($dni)) {
            echo "El cliente con el DNI $dni ya existe en el sistema." . PHP_EOL;
            return;
        }

        $cliente = new Cliente($nombre, $dni);
        if ($modelo->guardar($cliente)) {
            $this->agregar($cliente);

            if (strtolower(Menu::readln("¿Desea agregar una mascota? (si/no): ")) === 'si') {
                $mascotaManager = new MascotaManager($cliente);
                $mascotaManager->alta();
            }
            echo "El cliente se ha creado con éxito." . PHP_EOL;
        } else {
            echo "Error al crear el cliente." . PHP_EOL;
        }
    }

    // Elimina un Cliente
    public function baja() {
        $this->mostrar();
        $id = Menu::readln("Ingrese el id cliente a eliminar:");

        if ($this->existeId($id)) {
            $cliente = $this->getPorId($id);

            Menu::writeln('Está por eliminar al siguiente cliente del sistema: ' . PHP_EOL);
            $cliente->mostrar();

            if (strtolower(Menu::readln(PHP_EOL . '¿Está seguro? S/N: ')) === 's') {
                $modelo = new ClienteModelo();
                if ($modelo->borrar($cliente)) {
                    $this->eliminarPorId($id);
                    echo "El cliente fue eliminado con éxito." . PHP_EOL;
                }
            }
        } else {
            echo "No existe el ID a eliminar." . PHP_EOL;
        }
    }

// Mostra los Clientes 
    public function mostrar() {
        $this->levantar();

        $clientes = $this->getArreglo();
        Menu::cls();
        Menu::subtitulo('Lista de los clientes existentes en nuestro sistema');

        $lineas = 0; // Controla cant de Cliente que se muestran, hace pausa c/cierta cant de lineas
        foreach ($clientes as $cliente) {
            $cliente->mostrar();
            Menu::writeln("");
            $lineas += 1;

            if (($lineas % Menu::lineasPorPagina()) === 0) {
                Menu::waitForEnter();
                Menu::cls();
            }
        }
        Menu::waitForEnter();
    }

    // Modifica un Cliente
    public function modificar($elementoModificado = null) {
        $this->mostrar();
        $idOriginal = Menu::readln("Ingrese Id de cliente a modificar: ");

        if ($this->existeId($idOriginal)) {
            $clienteModificado = $this->getPorId($idOriginal);

            Menu::writeln('Está por modificar al siguiente cliente del sistema: ' . PHP_EOL);
            $clienteModificado->mostrar();

            if (strtolower(Menu::readln(PHP_EOL . '¿Está seguro? S/N: ')) == 's') {
                Menu::writeln("A continuación ingrese los nuevos datos (ENTER para dejarlos sin modificar): ");
                $campos = [];

                $nombre = Menu::readln("Ingrese el nombre y apellido: ");
                if ($nombre != "") {
                    if (!preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
                        echo "Error: El nombre debe contener solo letras." . PHP_EOL;
                        return;
                    }        
                    $clienteModificado->setNombre($nombre);
                    $campos['nombre'] = $nombre;
                }
                $dni = Menu::readln("Ingrese el dni: ");
                if ($dni != "") {
                    if (!preg_match('/^[0-9]+$/', $dni) || strlen($dni) != 8) {
                        echo "Error: El DNI debe ser numérico y tener 8 dígitos." . PHP_EOL;
                       return;
                    }
                    $clienteModificado->setDni($dni);
                    $campos['dni'] = $dni;
                }

                if (empty($campos)) {
                    Menu::writeln("No se ingresaron cambios.");
                    return;
                }

                $modelo = new ClienteModelo();
                if ($modelo->modificar($clienteModificado, $campos)) {
                    Menu::writeln("El cliente fue modificado con éxito");
                    parent::modificar($clienteModificado);
                } else {
                    Menu::writeln("No se pudo modificar el cliente.");
                }
            } else {
                Menu::writeln("Se ha cancelado la modificación.");
            }
        }
    }
}
