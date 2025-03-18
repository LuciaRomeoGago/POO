<?php
class Producto{
    private $id; // viene a ser el ID 
    private $nombre;
    private $precio;
    private $stock;

    public function __construct($id, $nombre, $precio, $stock)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->stock = $stock;
    }

    // Getters, permiten acceder a las propiedades privadas de la clase sin que sus valores se modifiquen directamente
    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function getStock()
    {
        return $this->stock;
    }

    //Setters, permiten modificar las propiedades, algunas retornan $this para permitir encadenar llamadas

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    public function setStock($stock)
    {
        $this->stock = $stock;
    }


    // Guarda en la base de datos
    public function guardar()
    {
        try {
            //prepara y ejecuta consulta para insertar el cliente
            $sql = "INSERT INTO Producto (id, nombre, precio, stock)
                        VALUES (:id, :nombre, :precio, :stock)";

            $stmt = Conexion::prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':precio', $this->precio);
            $stmt->bindParam(':stock', $this->stock);

            if ($stmt->execute()) {
                echo "Se ha guardado exitosamente el producto.";
                return true;
            }
        } catch (PDOException $e) {
            echo "Error al guardar el producto: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }


    // Borra el producto de la base de datos
    public function borrar()
    {
        try {
            //prepara la consulta SQL
            $sql = "DELETE FROM Producto WHERE id = :id";
            //prepara la declaracion
            $stmt = Conexion::prepare($sql);
            //asocia parametros
            $stmt->bindParam(':id', $this->id);

            if ($stmt->execute()) {
                echo "Producto borrado exitosamente.";
                return true;
            } else {
                echo "No se pudo borrar el producto.";
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al borrar el producto: " . htmlspecialchars($e->getMessage());
        }
    }


    // Modifica al producto en la base de datos
    public function modificar()
    {
        try {
            //prepara la consulta SQL
            $sql = "UPDATE Producto SET nombre = :nombre, precio = :precio, stock = :stock
                        WHERE id = :id";

            // Prepara la declaración
            $stmt = Conexion::prepare($sql);

            // Asocia los parámetros
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':precio', $this->precio);
            $stmt->bindParam(':stock', $this->stock);

            if ($stmt->execute()) {
                echo "El producto se ha modificado exitosamente.";
                return true;
            } else {
                echo "No se pudo modificar el producto.";
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al modificar el producto: " . $e->getMessage();
        }
        return false;
    }

    //Funciones equis

    public function hayStockDisponible()
    {
        return $this->stock > 0;
    }

    public function restarStock($cantidad)
    {
        if ($this->hayStockDisponible() && $cantidad <= $this->stock) {
            $this->stock -= $cantidad;
            return true;
        }
        return false;
    }

    //Muestra por pantalla producto
    public function mostrar()
    {
        echo "Id: " . $this->getId()
            . ", Nombre: " . $this->getNombre()
            . ", Precio: " . $this->getPrecio()
            . ", Stock: " . $this->getStock()
            . PHP_EOL;
    }

    // Muestro todos los productos
    public static function mostrarTodosProductos()
    {
        $sql = "SELECT * FROM Producto";
        $stmt = Conexion::prepare($sql);
        $stmt->execute();
        $filas = $stmt->fetchAll();
        $productos = [];
        foreach ($filas as $fila) {
            $productos[] = new self($fila['id'], $fila['nombre'], $fila['precio'], $fila['stock']);
        }
        return $productos;
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
                return new self($fila['id'], $fila['nombre'], $fila['precio'], $fila['stock']);
            }
            return null; // Si no se encuentra el producto
        } catch (PDOException $e) {
            error_log("Error al buscar producto: " . $e->getMessage());
            return null;
        }
    }
}