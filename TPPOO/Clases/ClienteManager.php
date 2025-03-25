<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ClienteModelo.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaModelo.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Inventario.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Mascota.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaModelo.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Producto.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ProductoModelo.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'interface.php');
require_once('Menu' . DIRECTORY_SEPARATOR . 'Menu.php');


class ClienteManager extends arrayIdManager
{
    public function __construct()
    {
        $this->levantar(); //llama al metodo levantar para cargar clientes desde db al crear una isntancia de clientemanager
    }

    //De la base de datos levanta los clientes y los agrega al arreglo para manipularlos (lee datos de clientes desde db y crear objetos clientes con esos datos)
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

                $cliente->setMascotas(MascotaModelo::getMascotasPorClienteId($cliente->getId()));

                // Cargar el inventario del cliente
                $inventario = Inventario::obtenerInventario($cliente->getId());
                $cliente->setInventario($inventario);

                // Agregar al arreglo gestionado por ArrayIdManager
                $this->agregar($cliente);
            }
        } catch (PDOException $e) {
            echo "Error al levantar clientes: " . htmlspecialchars($e->getMessage());
        }
    }

    // Crear cliente
    public function alta()
    {
        $nombre = Menu::readln("Ingrese el nombre y apellido del cliente: ");
        $dni = Menu::readln("Ingrese el dni del cliente: ");

        $modelo = new ClienteModelo();
        if ($modelo->existeDni($dni)) {
            echo "El cliente con el DNI $dni ya existe en el sistema." . PHP_EOL;
            return;
        }

        $cliente = new Cliente($nombre, $dni);
        if ($modelo->guardar($cliente)) {
            //Lo agrega al arreglo
            $this->agregar($cliente);

            // Preguntar si desea agregar una mascota
            if (strtolower(Menu::readln("¿Desea agregar una mascota? (si/no): ")) === 'si') {
                $mascotaManager = new MascotaManager($cliente);
                $mascotaManager->alta();
            }
            echo "El cliente se ha creado con éxito." . PHP_EOL;
        } else {
            echo "Error al crear el cliente." . PHP_EOL;
        }
    }

    // Eliminar cliente
    public function baja()
    {
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

// Mostrar Clientes en pantalla
    public function mostrar()
    {
        $this->levantar();

        $clientes = $this->getArreglo();
        Menu::cls();
        Menu::subtitulo('Lista de los clientes existentes en nuestro sistema');

        $lineas = 0; // para controlar la cantidad de clientes mostrados por pantalla y hace pausa c/cierta cant de lineas
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

    // Modificar Cliente
    public function modificar($elementoModificado = null)
    {
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
                    $clienteModificado->setNombre($nombre);
                    $campos['nombre'] = $nombre;
                }
                $dni = Menu::readln("Ingrese el dni: ");
                if ($dni != "") {
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
                    // Actualizar el cliente en el arrayIdManager
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
