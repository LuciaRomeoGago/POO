<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Inventario.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Mascota.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaModelo.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Producto.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'ABMinterface.php');

class ClienteModelo
{
    //  Guarda Cliente
    public function guardar(Cliente $cliente) {
        try {
            $sql = "INSERT INTO Cliente (nombre, dni)
                    VALUES (:nombre, :dni)";

            $stmt = Conexion::prepare($sql);
            $nombre = $cliente->getNombre();
            $dni =  $cliente->getDni();

            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':dni', $dni);

            if ($stmt->execute()) {
                $id = Conexion::getLastId();
                $cliente->setId($id); 
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al guardar cliente: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }

    // Mostrar todos los Clientes
    public static function obtenerTodos() {
        try {
            $sql = "SELECT * FROM Cliente";
            $stmt = Conexion::prepare($sql);
            $stmt->execute();

            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $clientes;
        } catch (PDOException $e) {
            echo "Error al obtener todos los clientes: " . $e->getMessage();  
        } return false;
    }

    // Leer o busca un Cliente por su ID
    public static function buscarPorId($id) {
        try {
            $stmt = Conexion::prepare("SELECT id, nombre, dni FROM Cliente WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();


            $clienteData = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($clienteData) {
                $cliente = new Cliente($clienteData['nombre'], $clienteData['dni'], $clienteData['id']);

                $cliente->setMascotas(MascotaModelo::getPorClienteId($cliente->getId()));

                return $cliente;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error en buscarPorId: " . $e->getMessage());
            return null;
        }
    }

    // Busca si existe o no un dni
    public function existeDni($dni) {
        try {
            $sql = "SELECT * FROM Cliente WHERE dni = :dni";
            $stmt = Conexion::prepare($sql);
            $stmt->bindParam(':dni', $dni);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true; 
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al verificar DNI: " . htmlspecialchars($e->getMessage());
        }
    }

    // Actualizo datos de Cliente
    public function modificar(Cliente $cliente, $campos) {
        try {
            $setClause = [];
            foreach ($campos as $campo => $valor) {
                $setClause[] = "$campo = :$campo";
            }
            $setClause = implode(", ", $setClause);


            $sql = "UPDATE Cliente SET $setClause WHERE id = :id";
            $stmt = Conexion::prepare($sql);

            foreach ($campos as $campo => $valor) {
                $stmt->bindValue(":$campo", $valor);
            }
            $id = $cliente->getId();
            $stmt->bindValue(':id', $id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al modificar cliente: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }

    // Borra el Cliente
    public function borrar(Cliente $cliente){
        try {

            foreach ($cliente->getReferenciaAnimal() as $mascota) {
                $mascotaModelo = new MascotaModelo;
                $mascotaModelo->borrar($mascota);
            }

            $sql = "DELETE FROM Cliente WHERE id = :id";
            $stmt = Conexion::prepare($sql);

            $id = $cliente->getId();
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al borrar cliente: " . htmlspecialchars($e->getMessage());
        }
    }
}
