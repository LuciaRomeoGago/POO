<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Producto.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ProductoModelo.php');

class InventarioManager
{
    // Agregar producto al inventario
    public static function agregarAlInventario(Cliente $cliente, $productoId, $cantidad)
    {
        if (Inventario::agregarProducto($cliente->getId(), $productoId, $cantidad)) {
            $cliente->setInventario(Inventario::obtenerInventario($cliente->getId()));
            return true;
        }
        return false;
    }

    // Eliminar producto del inventario
    public static function eliminarDelInventario(Cliente $cliente, $productoId)
    {
        if (Inventario::eliminarProducto($cliente->getId(), $productoId)) {
            $cliente->setInventario(Inventario::obtenerInventario($cliente->getId()));
            return true;
        }
        return false;
    }

    // Mostrar Inventario
    public static function mostrarInventario(Cliente $cliente)
    {
        $inventario = $cliente->getInventario();

        if (empty($inventario)) {
            echo "El cliente no tiene productos en su inventario." . PHP_EOL;
            return;
        }

        echo "Inventario de " . $cliente->getNombre() . ":" . PHP_EOL;
        foreach ($inventario as $item) {
            $producto = ProductoModelo::buscarPorId($item['productoId']);
            if ($producto) {
                echo "- " . $producto->getNombre()
                    . " (Cantidad: " . $item['cantidad'] . ")"
                    . PHP_EOL;
            }
        }
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

        // Solicitar cantidad a comprar
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

        // Restar stock al producto y actualizar en la base de datos
        if ($productoManager->comprar($idProducto, $cantidadCompra)) {
            echo PHP_EOL . "Compra realizada con éxito. " . PHP_EOL;

            // Recargar el producto desde la base de datos para obtener el stock actualizado
            $producto = ProductoModelo::buscarPorId($idProducto);

            // Agregar el producto al inventario del cliente
            if (InventarioManager::agregarAlInventario($cliente, $idProducto, $cantidadCompra)) {

                // Mostrar el inventario actualizado
                InventarioManager::mostrarInventario($cliente);
            } else {
                echo PHP_EOL . "Error al agregar el producto al inventario." . PHP_EOL;
            }
        } else {
            echo PHP_EOL . "Error al procesar la compra." . PHP_EOL;
        }
    }
}
