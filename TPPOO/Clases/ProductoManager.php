<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Producto.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ProductoModelo.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'ABMinterface.php');

class ProductoManager extends arrayIdManager implements ABMinterface {

    public function __construct(){
        $this->levantar();
    }

    // Levanta los Productos
    public function levantar(){
        $productos = ProductoModelo::obtenerTodos();

        foreach ($productos as $producto) {
            $nuevoProducto = new Producto(
                $producto->id,
                $producto->nombre,
                $producto->precio,
                $producto->stock
            );
            $this->agregar($nuevoProducto);
        }
    }

    // Muestra los Productos
    public function mostrar() {
        $this->levantar();

        $productos = $this->getArreglo();
        if (empty($productos)) {
            echo "No hay productos disponibles." . PHP_EOL;
            return;
        }

        Menu::cls();
        Menu::subtitulo('Lista de productos disponibles:');

        foreach ($productos as $producto) {
            $producto->mostrar();
        }
        Menu::waitForEnter();
    }

    // Crea un Producto
    public function alta()
    {
        $id = Menu::readln("Ingrese el id del producto: ");
        $nombre = Menu::readln("Ingrese el nombre del producto: ");
        $precio = Menu::readln("Ingrese el precio del producto: ");
        $stock = Menu::readln("Ingrese el stock del producto: ");

        if (!ctype_digit($id)) {
            echo "Error: El id debe contener solo un número entero." . PHP_EOL;
            return false;
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', ($nombre))) {
            echo "Error: El nombre debe contener solo letras y espacios." . PHP_EOL;
            return false;
        }

        if (!ctype_digit($precio)) {
            echo "Error: el precio debe ser un número entero." . PHP_EOL;
            return false;
        }

        if (!ctype_digit($stock)) {
            echo "Error: el stock debe ser un número entero." . PHP_EOL;
            return false;
        }

        if (empty($id) || empty($nombre) || empty($precio) || empty($stock)) {
            echo "Error: los campos id, nombre, precio y stock son obligatorios" . PHP_EOL;
            return false;
        }
        $productoModelo = new ProductoModelo();
        if ($productoModelo->existeId($id)) {
            echo "Error: Ya existe un producto con el ID ingresado. Por favor, ingrese otro ID." . PHP_EOL;
            return false;
        }

        $producto = new Producto($id, $nombre, $precio, $stock);


        if ($productoModelo->guardar($producto)) {
            echo "Se ha creado el producto exitosamente." . PHP_EOL;
            return true;
        } else {
            echo "Hubo un error al crear el producto." . PHP_EOL;
            return false;
        }
    }

    // Elimina un Producto
    public function baja()
    {
        $this->mostrar();

        $id = Menu::readln("Ingrese el ID del producto a eliminar: ");
        if ($this->existeId($id)) {
            $producto = $this->getPorId($id);

            Menu::writeln('Está por eliminar el siguiente producto: ' . PHP_EOL);
            $producto->mostrar();

            if (strtolower(Menu::readln(PHP_EOL . '¿Está seguro que desea eliminar este producto? (S/N): ')) === 's') {
                $productoModelo = new ProductoModelo();
                if ($productoModelo->borrar($producto)) {
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

    // Modifica un Producto
    public function modificar($elementoModificado = null)
    {
        $this->mostrar();

        $id = Menu::readln("Ingrese el ID del producto a modificar: ");
        if ($this->existeId($id)) {
            $producto = $this->getPorId($id);

            Menu::writeln('Está por modificar el siguiente producto: ' . PHP_EOL);
            $producto->mostrar();

            if (strtolower(Menu::readln(PHP_EOL . '¿Está seguro que desea modificar este producto? (S/N): ')) === 's') {

                Menu::writeln("A continuación ingrese los nuevos datos, ENTER para dejarlos sin modificar");
                $nombre = Menu::readln("Ingrese el nuevo nombre del producto: ");
                while ($nombre != "" && !preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
                    echo "Error: El nombre debe contener solo letras y espacios. " . PHP_EOL;
                    $nombre = Menu::readln("Ingrese el nuevo nombre del producto: ");
                }
                if ($nombre != "") {
                    $producto->setNombre($nombre);
                }

                $precio = Menu::readln("Ingrese el nuevo precio del producto: ");
                while ($precio != "" && (!is_numeric($precio) || $precio <= 0)) {
                    echo "Error: El precio debe contener un número positivo. " . PHP_EOL;
                    $precio = Menu::readln("Ingrese el nuevo precio del producto: ");
                }
                if ($precio != "") {
                    $producto->setPrecio($precio);
                }

                $stock = Menu::readln("Ingrese el nuevo stock del producto: ");
                while ($stock != "" && (!is_numeric($stock) || $stock < 0)) {
                    echo "Error: El stock debe contener un número positivo." . PHP_EOL;
                    $stock = Menu::readln("Ingrese el nuevo stock del producto: ");
                }
                if ($stock != "") {
                    $producto->setStock($stock);
                }

                $productoModelo = new ProductoModelo();
                if ($productoModelo->modificar($producto)) {
                    echo "El producto ha sido modificado con éxito." . PHP_EOL;
                    parent::modificar($producto);
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

    public function comprar($idProducto, $cantidadCompra)
    {
        try {
            $productoModelo = new ProductoModelo();
            $producto = ProductoModelo::buscarPorId($idProducto);

            if ($producto) {
                if ($productoModelo->hayStockDisponible($producto, $cantidadCompra)) {
                    // Restar stock y actualizar en la base de datos
                    if ($productoModelo->restarStock($producto, $cantidadCompra)) {
                        return true;
                    } else {
                        echo PHP_EOL . "Stock insuficiente o cantidad no válida." . PHP_EOL;
                        return false;
                    }
                } else {
                    echo PHP_EOL . "Producto no encontrado." . PHP_EOL;
                    return false;
                }
            }
        } catch (Exception $e) {
            echo PHP_EOL . "Error al procesar la compra: {$e->getMessage()}" . PHP_EOL;
            return false;
        }
        return false;
    }
}
