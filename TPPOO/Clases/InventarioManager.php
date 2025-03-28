<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Producto.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ProductoModelo.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'ABMinterface.php');

class InventarioManager extends ArrayIdManager implements ABMinterface {
    private $cliente;

    public function __construct(Cliente $cliente) {
        $this->cliente = $cliente;
        $this->levantar();
    }

    public function levantar() {
        $inventarioDatos = InventarioModelo::getPorClienteId($this->cliente->getId());

        foreach ($inventarioDatos as $item) {
            $producto = ProductoModelo::buscarPorId($item['productoId']);
            if ($producto) {
                $inventarioItem = new Inventario($producto, $item['cantidad']);
                parent::agregar($inventarioItem);
            }
        }
    }

    // Agrega Producto al inventario
    public function alta()
    {
        echo "Ingrese el ID del producto a agregar al inventario: ";
        $productoId = trim(fgets(STDIN));

        echo "Ingrese la cantidad a agregar: ";
        $cantidad = (int)trim(fgets(STDIN));

        if (InventarioModelo::guardar($this->cliente->getId(), $productoId, $cantidad)) {
            $producto = ProductoModelo::buscarPorId($productoId);
            if ($producto) {
                $inventarioItem = new Inventario($producto, $cantidad);
                parent::agregar($inventarioItem);
            }
            echo "El producto se ha agregado al inventario con éxito." . PHP_EOL;
        } else {
            echo "Error al agregar el producto al inventario." . PHP_EOL;
        }
    }

    // Eliminar producto del inventario
    public function baja()
    {
        $this->mostrar();

        echo "Ingrese el ID del producto a eliminar del inventario: ";
        $productoId = trim(fgets(STDIN));

        if (InventarioModelo::borrar($this->cliente->getId(), $productoId)) {

            parent::eliminarPorId($productoId);
            echo "El producto se ha eliminado del inventario con éxito." . PHP_EOL;
        } else {
            echo "Error al eliminar el producto del inventario." . PHP_EOL;
        }
    }

    // Mostrar Inventario
    public function mostrar()
    {
        echo "Inventario de " . $this->cliente->getNombre() . ":" . PHP_EOL;

        if (empty($this->getArreglo())) {
            echo "El cliente no tiene productos en su inventario." . PHP_EOL;
            return;
        }
        foreach ($this->getArreglo() as $inventarioItem) {
            echo "- " . $inventarioItem->getProducto()->getNombre()
                . " (Cantidad: " . $inventarioItem->getCantidad() . ")"
                . PHP_EOL;
        }
    }

    public static function mostrarInventario(Cliente $cliente)
    {
        $inventarioManager = new self($cliente);
        $inventarioManager->mostrar();
    }

    // Comprar Producto
    public static function comprarProducto(Cliente $cliente, ProductoManager $productoManager)
    {
        $productoManager->mostrar();

        echo PHP_EOL . "Ingrese el ID del producto que desea comprar: ";
        $idProducto = trim(fgets(STDIN));

        if (!$producto = ProductoModelo::buscarPorId($idProducto)) {
            echo PHP_EOL . "El producto no existe. Intente nuevamente." . PHP_EOL;
            return;
        }

        echo "Ingrese la cantidad que desea comprar: ";
        $cantidadCompra = (int)trim(fgets(STDIN));

        if ($cantidadCompra <= 0) {
            echo PHP_EOL . "La cantidad debe ser mayor a 0. Intente nuevamente." . PHP_EOL;
            return;
        }

        // Verificar si hay suficiente stock
        $productoModelo = new ProductoModelo();
        if (!$productoModelo->hayStockDisponible($producto, $cantidadCompra)) {
            echo PHP_EOL . "No hay suficiente stock disponible para este producto." . PHP_EOL;
            return;
        }

        if ($productoManager->comprar($idProducto, $cantidadCompra)) {
            echo PHP_EOL . "Compra realizada con éxito. " . PHP_EOL;

            $producto = ProductoModelo::buscarPorId($idProducto);

            if (InventarioModelo::guardar($cliente->getId(), $idProducto, $cantidadCompra)) {
                InventarioManager::mostrarInventario($cliente);
            } else {
                echo PHP_EOL . "Error al agregar el producto al inventario." . PHP_EOL;
            }
        } else {
            echo PHP_EOL . "Error al procesar la compra." . PHP_EOL;
        }
    }
}
