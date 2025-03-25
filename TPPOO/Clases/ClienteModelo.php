<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Inventario.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Mascota.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaModelo.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Producto.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'interface.php');


class ClienteModelo
{
    //  Guarda en la base de datos
    public function guardar(Cliente $cliente)
    {
        try {
            //prepara y ejecuta consulta para insertar el cliente
            $sql = "INSERT INTO Cliente (nombre, dni)
                    VALUES (:nombre, :dni)";

            $stmt = Conexion::prepare($sql);
            $nombre = $cliente->getNombre();
            $dni =  $cliente->getDni();


            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':dni', $dni);


            if ($stmt->execute()) {
                // Obtener el ID generado
                $id = Conexion::getLastId();
                $cliente->setId($id); // Asignar el ID al objeto Cliente
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al guardar cliente: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }

    public static function obtenerTodos() {
        try {
            $sql = "SELECT * FROM Cliente";
            $stmt = Conexion::prepare($sql);
            $stmt->execute();

            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $clientes;
        } catch (PDOException $e) {
            error_log("Error al obtener todos los clientes: " . $e->getMessage());
            return [];
        }
    }

    // leer o busca un cliente por su ID en la base de datos
    public static function buscarPorId($id)
    {
        try {
            // Preparar la consulta
            $stmt = Conexion::prepare("SELECT id, nombre, dni FROM Cliente WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Recuperar los datos del cliente
            $clienteData = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si existen datos del cliente, proceder a crear el objeto Cliente
            if ($clienteData) {
                $cliente = new Cliente($clienteData['nombre'], $clienteData['dni'], $clienteData['id']);

                // Asignar las mascotas del cliente utilizando el método estático de la clase Mascota
                $cliente->setMascotas(MascotaModelo::getMascotasPorClienteId($cliente->getId()));

                return $cliente;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error en buscarPorId: " . $e->getMessage()); 
            return null;
        }
    }

    public function existeDni($dni)
    {
        try {
            $sql = "SELECT * FROM Cliente WHERE dni = :dni";
            $stmt = Conexion::prepare($sql);
            $stmt->bindParam(':dni', $dni);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true; // El DNI ya existe
            } else {
                return false; // El DNI no existe
            }
        } catch (PDOException $e) {
            echo "Error al verificar DNI: " . htmlspecialchars($e->getMessage());
        }
    }

    // Actualizar
    public function modificar(Cliente $cliente, $campos)
    {
        try {
            // Construyo dinámicamente la consulta SQL
            $setClause = [];
            foreach ($campos as $campo => $valor) {
                $setClause[] = "$campo = :$campo";
            }
            $setClause = implode(", ", $setClause);


            $sql = "UPDATE Cliente SET $setClause WHERE id = :id";
            $stmt = Conexion::prepare($sql);

            // Asociar los parámetros
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

    // Borra el cliente de la base de datos
    public function borrar(Cliente $cliente)
    {
        try {
            //Primero elimino las mascotas asociadas al cliente
            foreach ($cliente->getReferenciaAnimal() as $mascota) {
                $mascotaModelo= new MascotaModelo;
                $mascotaModelo->borrar($mascota); // Llama al método borrar() de cada mascota
            }
            //prepara la consulta SQL
            $sql = "DELETE FROM Cliente WHERE id = :id";
            //prepara la declaracion
            $stmt = Conexion::prepare($sql);
            //asocia parametros

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
