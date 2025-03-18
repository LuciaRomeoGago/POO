<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Inventario.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Mascota.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Producto.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'interface.php');


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
            $sql = "select * 
                from Cliente";
            $clientes = Conexion::query($sql); //utiliza la consulta usando metodo query de conexion y almacena resultados en $clientes

            // Verificar si se obtuvieron resultados
            if ($clientes === false || empty($clientes)) {
                echo "No se encontraron clientes en la base de datos." . PHP_EOL;
                return; // Salir del método si no hay clientes
            }

            foreach ($clientes as $cliente) {
                if (!array_key_exists('id', $cliente)) {
                    echo "Advertencia: No se encontró la clave 'id' para un cliente." . PHP_EOL;
                    continue;
                }
                // Acceder a los valores por su clave (nombre de la columna en la base de datos)
                $nombre = $cliente['nombre'];
                $dni = $cliente['dni'];
                $id = $cliente['id'];

                // Crear el objeto Cliente y agregarlo al arreglo
                $nuevoCliente = new Cliente(
                    $nombre,
                    $dni,
                    $id  // Asignar ID desde la base de datos
                );

                // Cargar las mascotas del cliente
                $mascotas = Mascota::getMascotasPorClienteId($id);
                foreach ($mascotas as $mascota) {
                    $nuevoCliente->agregarMascota($mascota);
                }

                // Cargar el inventario del cliente
                $inventario = Inventario::obtenerInventario($id);
                $nuevoCliente->setInventario($inventario);

                // Agregar al arreglo gestionado por ArrayIdManager
                $this->agregar($nuevoCliente);
            }
        } catch (PDOException $e) {
            echo "Error al levantar clientes: " . htmlspecialchars($e->getMessage());
        }
    }

    //  (crea nuevo cliente en el sistema) Guarda el cliente en la base de datos y le setea el id generado por la base de datos al insertarlo
    public function alta()
    {

        $nombre = Menu::readln("Ingrese el nombre y apellido del cliente: ");
        $dni = Menu::readln("Ingrese el dni del cliente: ");
        $id = Menu::readln("Ingrese el id del cliente: ");

        //Crea el nuevo objeto cliente
        $cliente = new Cliente($nombre, $dni, $id);

        //Lo inserta en la base de datos
        if ($cliente->guardar()) {

            //Lo agrega al arreglo
            $this->agregar($cliente);

            // Preguntar si desea agregar una mascota
            $agregarMascota = Menu::readln("¿Desea agregar una mascota? (si/no): ");
            if (strtolower($agregarMascota) === 'si') {
                $nombreMascota = Menu::readln("Ingrese el nombre de la mascota: ");
                $edad = Menu::readln("Ingrese la edad de la mascota: ");
                $raza = Menu::readln("Ingrese la raza de la mascota: ");
                $historialMedico = Menu::readln("Ingrese el historial médico de la mascota: ");

                // Crea el nuevo objeto Mascota
                $mascota = new Mascota($nombreMascota, $edad, $raza, $historialMedico);
                $mascota->setClienteId($cliente->getId()); // Asigna el ID del cliente a la mascota
                $mascota->guardar();
                // Agrega la mascota al cliente
                $cliente->agregarMascota($mascota);

                // Mensaje de confirmación
                echo "La mascota se ha agregado exitosamente." . PHP_EOL;
            }

            echo "El cliente se ha creado con éxito." . PHP_EOL;
        } else {
            echo "Error al crear el cliente." . PHP_EOL;
        }
    }

    //Dar de baja(elimina) un cliente, se pide el id del cliente a eliminar. Se elimina de la base de datos y del arreglo
    public function baja()
    {

        $this->mostrar();
        $id = Menu::readln("Ingrese el id cliente a eliminar:");
        if ($this->existeId($id)) {
            $cliente = $this->getPorId($id);
            Menu::writeln('Está por eliminar al siguiente cliente del sistema: ' . PHP_EOL);
            $cliente->mostrar();
            $rta = Menu::readln(PHP_EOL . '¿Está seguro? S/N: ');
            if (strtolower($rta) === 's') {
                // Lo elimina de la base de datos
                $cliente->borrar();
                // Lo elimina del arreglo
                $this->eliminarPorId($id);
                echo "El cliente fue eliminado con éxito." . PHP_EOL;
            }
        } else {
            echo "No existe el ID a eliminar." . PHP_EOL;
        }
    }

    // Actualizar los datos de un cliente existente por su ID
    public function modificarCliente()
    {

        $this->mostrar();
        $idOriginal = Menu::readln("Ingrese Id de cliente a modificar: ");

        if ($this->existeId($idOriginal)) {
            $clienteModificado = $this->getPorId($idOriginal);
            Menu::writeln('Está por modificar al siguiente cliente del sistema: ' . PHP_EOL);
            $clienteModificado->mostrar();

            $rta = Menu::readln(PHP_EOL . '¿Está seguro? S/N: ');
            if ($rta == 'S' or $rta == 's') {
                $campos = [];
                Menu::writeln("A continuación ingrese los nuevos datos, ENTER para dejarlos sin modificar");

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
                } else {

                    // Pasar $campos al método modificar()
                    $clienteModificado->modificar($campos);
                    Menu::readln("El cliente fue modificado con éxito");
                }
            }
        } else {
            Menu::writeln("El id ingresado no se encuentra entre nuestros clientes");
        }
    }

    // Mostrar por pantalla todos los clientes
    public function mostrar()
    {
        $clientes = $this->getArreglo();
        Menu::cls();
        Menu::subtitulo('Lista de los clientes existentes en nuestro sistema');
        $lineas = 0; // para controlar la cantidad de clientes mostrados por pantalla y hace pausa c/cierta cant de lineas

        foreach ($clientes as $cliente) {
            $cliente->mostrar();
            Menu::writeln("");
            $lineas += 1;
            if ((($lineas) % (Menu::lineasPorPagina())) === 0) {
                Menu::waitForEnter();
                Menu::cls(); // Limpiar la pantalla antes de imprimir las siguientes líneas
            }
        }
        Menu::waitForEnter();
    }

    // funciones para inventario
    public static function agregarAlInventario(Cliente $cliente, $productoId, $cantidad)
    {
        if (Inventario::agregarProducto($cliente->getId(), $productoId, $cantidad)) {
            $cliente->setInventario(Inventario::obtenerInventario($cliente->getId()));
            return true;
        }
        return false;
    }

    public static function eliminarDelInventario(Cliente $cliente, $productoId)
    {
        if (Inventario::eliminarProducto($cliente->getId(), $productoId)) {
            $cliente->setInventario(Inventario::obtenerInventario($cliente->getId()));
            return true;
        }
        return false;
    }

    public static function mostrarInventario(Cliente $cliente)
    {
        if (empty($cliente->getInventario())) {
            echo "El cliente no tiene productos en su inventario." . PHP_EOL;
            return;
        }

        echo "Inventario de " . $cliente->getNombre() . ":" . PHP_EOL;
        foreach ($cliente->getInventario() as $item) {
            $producto = Producto::buscarPorId($item['productoId']);
            if ($producto) {
                echo "- " . $producto->getNombre()
                    . " (Cantidad: " . $item['cantidad'] . ")"
                    . PHP_EOL;
            }
        }
    }

    public function comprarProducto(Cliente $cliente, ProductoManager $productoManager)
    {
        // Mostrar lista de productos disponibles
        $productoManager->mostrar();

        // Solicitar ID del producto
        echo PHP_EOL . "Ingrese el ID del producto que desea comprar: ";
        $idProducto = trim(fgets(STDIN));

        // Validar que el producto exista
        $producto = Producto::buscarPorId($idProducto);
        if (!$producto) {
            echo PHP_EOL . "El producto no existe. Intente nuevamente." . PHP_EOL;
            return;
        }

        // Solicitar cantidad a comprar
        echo "Ingrese la cantidad que desea comprar: ";
        $cantidadCompra = (int) trim(fgets(STDIN));

        if ($cantidadCompra <= 0) {
            echo PHP_EOL . "La cantidad debe ser mayor a 0. Intente nuevamente." . PHP_EOL;
            return;
        }

        // Verificar si hay suficiente stock
        if (!$producto->hayStockDisponible() || $producto->getStock() < $cantidadCompra) {
            echo PHP_EOL . "No hay suficiente stock disponible para este producto." . PHP_EOL;
            return;
        }

        // Restar stock al producto y actualizar en la base de datos
        if ($productoManager->comprar($idProducto, $cantidadCompra)) {
            echo PHP_EOL . "Compra realizada con éxito. Stock actualizado: {$producto->getStock()}" . PHP_EOL;

            // Recargar el producto desde la base de datos para obtener el stock actualizado
            $producto = Producto::buscarPorId($idProducto);

            // Agregar el producto al inventario del cliente
            if (ClienteManager::agregarAlInventario($cliente, $idProducto, $cantidadCompra)) {
                echo PHP_EOL . "El producto se ha agregado al inventario del cliente." . PHP_EOL;

                // Mostrar el inventario actualizado
                ClienteManager::mostrarInventario($cliente);
            } else {
                echo PHP_EOL . "Error al agregar el producto al inventario." . PHP_EOL;
            }
        } else {
            echo PHP_EOL . "Error al procesar la compra." . PHP_EOL;
        }
    }
}
