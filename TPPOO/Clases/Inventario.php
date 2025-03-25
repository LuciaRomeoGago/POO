<?php
class Inventario{ //no le hago modelo porque no lo utilizo para otra cosa
    
    // Agregar producto al inventario
    public static function agregarProducto($clienteId, $productoId, $cantidad)
    {
        try {
            $sql = "INSERT INTO Inventario (clienteId, productoId, cantidad) 
                    VALUES (:clienteId, :productoId, :cantidad)
                    ON DUPLICATE KEY UPDATE cantidad = cantidad + :cantidad";

            $stmt = Conexion::prepare($sql);
            $stmt->bindParam(':clienteId', $clienteId);
            $stmt->bindParam(':productoId', $productoId);
            $stmt->bindParam(':cantidad', $cantidad);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al agregar producto al inventario: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar producto del inventario
    public static function eliminarProducto($clienteId, $productoId)
    {
        $sql = "DELETE FROM Inventario 
                WHERE clienteId = :clienteId AND productoId = :productoId";
        $stmt = Conexion::prepare($sql);
        $stmt->bindParam(':clienteId', $clienteId);
        $stmt->bindParam(':productoId', $productoId);
        return $stmt->execute();
    }

    // Obtener inventario del cliente
    public static function obtenerInventario($clienteId)
    {
        try {
            $sql = "SELECT productoId, cantidad 
                FROM Inventario 
                WHERE clienteId = :clienteId";
            $stmt = Conexion::prepare($sql);
            $stmt->bindParam(':clienteId', $clienteId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "Inventario para cliente " . htmlspecialchars($clienteId) . ": ";
            var_dump($inventario);
            echo PHP_EOL;

            return $inventario;
        } catch (PDOException $e) {
            error_log("Error al obtener inventario: " . $e->getMessage());
            return [];
        }
    }
}
