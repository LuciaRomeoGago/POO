<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Producto.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'interface.php');

class ProductoManager extends arrayIdManager{

    public function __construct() {
        $this->levantar();
    }

    // Levanta(para obtener) los productos desde la base de datos
    public function levantar() {
        try {
            $sql = "SELECT * FROM Producto";
            $stmt = Conexion::prepare($sql);
            $stmt->execute();

            $productos = $stmt->fetchAll(PDO::FETCH_OBJ);

            foreach ($productos as $producto) {
                $nuevoProducto = new Producto(
                    $producto->id,
                    $producto->nombre,
                    $producto->precio,
                    $producto->stock
                );
                // Agregar al arreglo
                $this->agregar($nuevoProducto);
            }
        } catch (PDOException $e) {
            error_log("Error al levantar productos: ". $e->getMessage());
        }
    }

    // Método para mostrar los productos
    public function mostrar() {

        //levanto productos de la db
        $this->levantar();

        // Obtener los productos
        $productos = $this->getArreglo(); // debe devolver los productos
        if (empty($productos)) {
            echo "No hay productos disponibles." . PHP_EOL;
            return; // Salir si no hay productos
        }

        Menu::cls(); // Limpiar la pantalla si es necesario
        Menu::subtitulo('Lista de productos disponibles:');

        foreach ($productos as $producto) {
            $producto->mostrar(); // Llama al método mostrar() de cada producto
        }

        Menu::waitForEnter(); // Esperar a que el usuario presione Enter antes de continuar
    }

    public function alta (){
        $id = Menu::readln("Ingrese el id del producto: ");
        $nombre = Menu::readln("Ingrese el nombre del producto: ");
        $precio = Menu::readln("Ingrese el precio del producto: ");
        $stock = Menu::readln("Ingrese el stock del producto: ");

        if (empty ($id) || empty($nombre) || empty($precio) || empty($stock)) {
            echo "Error: los campos id, nombre, precio y stock son obligatorios" . PHP_EOL;
            return false;
        }

        $producto = new Producto ($id,$nombre,$precio,$stock);
        
        if ($producto ->guardar()) {
            echo "El producto se ha creado con exito.". PHP_EOL;
            return true;
        } else {
            echo "Hubo un error al crear el producto.". PHP_EOL;
            return false;
        }
    }

    public function baja(){
            // Mostrar todos los productos
            $this->mostrar();
            
            // Solicitar el ID del producto a eliminar
            $id = Menu::readln("Ingrese el ID del producto a eliminar: ");
            
            // Verificar si existe el producto
            if ($this->existeId($id)) {
                $producto = $this->getPorId($id);
                
                // Mostrar información del producto a eliminar
                Menu::writeln('Está por eliminar el siguiente producto: '. PHP_EOL);
                $producto->mostrar();
                
                // Confirmar si el usuario desea continuar
                $rta = Menu::readln(PHP_EOL . '¿Está seguro que desea eliminar este producto? (S/N): ');
                
                if (strtolower($rta) === 's') {
                    // Llamar al método borrar() del producto para eliminarlo de la base de datos
                    if ($producto->borrar()) {
                        // Eliminar el producto del arreglo gestionado por ProductoManager
                        $this->eliminarPorId($id);
                        echo "El producto ha sido eliminado con éxito." . PHP_EOL;
                    } else {
                        echo "Hubo un error al intentar eliminar el producto." . PHP_EOL;
                    }
                } else {
                    echo "Eliminación cancelada." . PHP_EOL;
                }
            } else {
                echo "El ID ingresado no se encuentra entre nuestros productos." . PHP_EOL;
            }
        }

        public function modificarProducto() {
            // Mostrar todos los productos
            $this->mostrar();
            
            // Solicitar el ID del producto a modificar
            $id = Menu::readln("Ingrese el ID del producto a modificar: ");
            
            // Verificar si existe el producto
            if ($this->existeId($id)) {
                $producto = $this->getPorId($id);
                
                // Mostrar información del producto a modificar
                Menu::writeln('Está por modificar el siguiente producto: '. PHP_EOL);
                $producto->mostrar();
                
                // Confirmar si el usuario desea continuar
                $rta = Menu::readln(PHP_EOL . '¿Está seguro que desea modificar este producto? (S/N): ');
                
                if (strtolower($rta) === 's') {
                    // Solicitar los nuevos datos del producto
                    Menu::writeln("A continuación ingrese los nuevos datos, ENTER para dejarlos sin modificar");
                    $nombre = Menu::readln("Ingrese el nuevo nombre del producto: ");
                    if ($nombre != "") {
                        $producto->setNombre($nombre);
                    }
                    
                    $precio = Menu::readln("Ingrese el nuevo precio del producto: ");
                    if ($precio != "") {
                        $producto->setPrecio($precio);
                    }
                    
                    $stock = Menu::readln("Ingrese el nuevo stock del producto: ");
                    if ($stock != "") {
                        $producto->setStock($stock);
                    }
                    
                    // Modificar el producto en la base de datos
                    if ($producto->modificar()) {
                        echo "El producto ha sido modificado con éxito." . PHP_EOL;
                    } else {
                        echo "Hubo un error al intentar modificar el producto." . PHP_EOL;
                    }
                } else {
                    echo "Modificación cancelada." . PHP_EOL;
                }
            } else {
                echo "El ID ingresado no se encuentra entre nuestros productos." . PHP_EOL;
            }
        }
        

    // Metodo para comprar un producto
    public function comprar($idProducto, $cantidadCompra) {
        try {
            // Buscar el producto por código
            $producto = Producto::buscarPorId($idProducto);

            if ($producto) {
                if ($producto->hayStockDisponible() && $producto->getStock() >= $cantidadCompra) {
                    // Restar stock y actualizar en la base de datos
                    if ($producto->restarStock($cantidadCompra)) {

                        // Actualizar el producto en la base de datos
                        if ($producto->modificar()) {
                            echo PHP_EOL . "Compra realizada con éxito. Stock actualizado: {$producto->getStock()}" . PHP_EOL;
                            return true;
                        } else {
                            echo PHP_EOL . "Error al actualizar el stock en la base de datos." . PHP_EOL;
                            return false;
                        }
                    } else {
                        echo PHP_EOL . "Error al restar el stock del producto." . PHP_EOL;
                        return false;
                    }
                } else {
                    echo PHP_EOL . "Stock insuficiente o cantidad no válida." . PHP_EOL;
                    return false;
                }
            } else {
                echo PHP_EOL . "Producto no encontrado." . PHP_EOL;
                return false;
            }
        } catch (Exception $e) {
            echo PHP_EOL . "Error al procesar la compra: {$e->getMessage()}" . PHP_EOL;
            return false;
        }
        return false;
    }

    public function comprarProducto() {
        //muestro lista de productos disponibles antes de pedir info
        $this->mostrar();
        echo PHP_EOL . "Ingrese el ID del producto que desea comprar: ";
        $idProducto = trim(fgets(STDIN));

        echo "Ingrese la cantidad que desea comprar: ";
        $cantidadCompra = (int) trim(fgets(STDIN));

        if ($this->comprar($idProducto, $cantidadCompra)) {
            echo PHP_EOL . "La compra fue exitosa.". PHP_EOL;
        } else {
             echo PHP_EOL . "Error al procesar la compra." . PHP_EOL;
            return false;
        }
    }
}
