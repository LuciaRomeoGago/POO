<?php
include_once('Clases' . DIRECTORY_SEPARATOR . 'Producto.php');
include_once('Clases' . DIRECTORY_SEPARATOR . 'ProductoManager.php');

class ProductoModelo
{
    // CRUD

    // Guarda en la base de datos
    public function guardar(Producto $producto)
    {
        try {
            //prepara y ejecuta consulta para insertar el cliente
            $sql = "INSERT INTO Producto (id, nombre, precio, stock)
                        VALUES (:id, :nombre, :precio, :stock)";

            $stmt = Conexion::prepare($sql);

            $id = $producto->getId();
            $nombre = $producto->getNombre();
            $precio = $producto->getPrecio();
            $stock = $producto->getStock();

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':precio', $precio);
            $stmt->bindParam(':stock', $stock);

            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            echo "Error al guardar el producto: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }

    public static function obtenerTodos()
    {
        try {
            $sql = "SELECT * FROM Producto";
            $stmt = Conexion::prepare($sql);
            $stmt->execute();

            $productos = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $productos;
        } catch (PDOException $e) {
            error_log("Error al obtener todos los productos: " . $e->getMessage());
            return;
        }
    }

    // Borra el producto de la base de datos
    public function borrar(Producto $producto)
    {
        try {
            //prepara la consulta SQL
            $sql = "DELETE FROM Producto WHERE id = :id";
            //prepara la declaracion
            $stmt = Conexion::prepare($sql);
            $id= $producto->getId();
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al borrar el producto: " . htmlspecialchars($e->getMessage());
        }
    }


    // Modifica al producto en la base de datos
    public function modificar(Producto $producto)
    {
        try {
            //prepara la consulta SQL
            $sql = "UPDATE Producto SET nombre = :nombre, precio = :precio, stock = :stock
                        WHERE id = :id";

            // Prepara la declaración
            $stmt = Conexion::prepare($sql);

            $id = $producto->getId();
            $nombre = $producto->getNombre();
            $precio = $producto->getPrecio();
            $stock = $producto->getStock();
    
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':precio', $precio);
            $stmt->bindParam(':stock', $stock);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al modificar el producto: " . $e->getMessage();
        }
        return false;
    }

    public static function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM Producto WHERE id = :id";
            $stmt = Conexion::prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fila) {

                return new Producto(
                    $fila['id'],
                    $fila['nombre'],
                    $fila['precio'],
                    $fila['stock']
                );
            }
            return null; // Si no se encuentra el producto
        } catch (PDOException $e) {
            error_log("Error al buscar producto: " . $e->getMessage());
            return null;
        }
    }


    public function hayStockDisponible(Producto $producto, $cantidadCompra)
    {
        return $producto->getStock() >= $cantidadCompra;
    }

    public function restarStock(Producto $producto, $cantidadCompra)
    {
        if ($this->hayStockDisponible($producto, $cantidadCompra)) {
            $nuevoStock = $producto->getStock() - $cantidadCompra;
            $producto->setStock($nuevoStock);
            return $this->modificar($producto);
        }
        return false;
    }
}
