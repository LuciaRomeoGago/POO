<?php
class Cliente {
    private $nombre;
    private $dni;
    private $id;
    private $referenciaAnimal = [];
    private $inventario = [];

    public function __construct($nombre, $dni, $id = null)
    {
        $this->nombre = $nombre;
        $this->dni = $dni;
        $this->id = $id ?? uniqid('Cliente_', true); // unico de c/cliente, si no se proporciona, se genera automaticamente
        $this->referenciaAnimal = []; //array, almacena las mascotas asociadas al cliente
    }

    // Getters, permiten acceder a las propiedades privadas de la clase sin que sus valores se modifiquen directamente

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getDni()
    {
        return $this->dni;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getReferenciaAnimal()
    {
        return $this->referenciaAnimal;
    }

    public function getInventario()
    {
        return $this->inventario;
    }

    // Setters, permiten modificar las propiedades, algunas retornan $this para permitir encadenar llamadas

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setDni($dni)
    {
        $this->dni = $dni;
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setMascotas($mascotas)
    { //establece las mascotas asociadas al cliente si es necesario
        $this->referenciaAnimal = $mascotas;
    }

    public function setInventario($inventario)
    {
        $this->inventario = $inventario;
    }

    // Agregar una mascota, establece el id de cliente, y permite agregar una instancia de mascota a este
    public function agregarMascota(Mascota $mascota)
    {
        $mascota->setClienteId($this->getId());
        $this->referenciaAnimal[] = $mascota;
    }

    // Mostrar todas las mascotas del cliente, verifica si hay mascotas y las imprime con el metodo mostrar() de c/mascota
    public function mostrarMascotas()
    {
        if (empty($this->referenciaAnimal)) {
            echo "Este cliente no tiene mascotas." . PHP_EOL;
            return;
        }

        echo "Mascotas del cliente " . htmlspecialchars($this->getNombre()) . ":" . PHP_EOL;
        foreach ($this->referenciaAnimal as $mascota) {
            echo "- ";
            $mascota->mostrar();
        }
    }

    //Muestra por pantalla un cliente y llama para mostrar sus mascotas
    public function mostrar()
    {
        echo "Id: " . $this->getId()
            . ", Nombre: " . $this->getNombre()
            . ", Dni: " . $this->getDni()
            . PHP_EOL;
        $this->mostrarMascotas();
    }

    // Guarda en la base de datos
    public function guardar()
    {
        try {
            //prepara y ejecuta consulta para insertar el cliente
            $sql = "INSERT INTO Cliente (nombre, dni, id)
                    VALUES (:nombre, :dni, :id)";

            $stmt = Conexion::prepare($sql);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':dni', $this->dni);
            $stmt->bindParam(':id', $this->id);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al guardar cliente: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }


    // Borra el cliente de la base de datos
    public function borrar()
    {
        try {
            //Primero elimino las mascotas asociadas al cliente
            foreach ($this->referenciaAnimal as $mascota) {
                $mascota->borrar(); // Llama al método borrar() de cada mascota
            }
            //prepara la consulta SQL
            $sql = "DELETE FROM Cliente WHERE id = :id";
            //prepara la declaracion
            $stmt = Conexion::prepare($sql);
            //asocia parametros
            $stmt->bindParam(':id', $this->id);

            if ($stmt->execute()) {
                echo "Cliente borrado exitosamente.";
                return true;
            } else {
                echo "No se pudo borrar el cliente.";
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al borrar cliente: " . htmlspecialchars($e->getMessage());
        }
    }

    public function modificar($campos)
    {
        try {
            // Imprimir los valores de las propiedades antes de ejecutar la consulta SQL
            echo "Nombre: " . htmlspecialchars($this->nombre) . PHP_EOL;
            echo "ID: " . htmlspecialchars($this->id) . PHP_EOL;

            // Construir dinámicamente la consulta SQL
            $setClause = [];
            foreach ($campos as $campo => $valor) {
                $setClause[] = "$campo = :$campo";
            }
            $setClause = implode(", ", $setClause);

            // Consulta SQL dinámica
            $sql = "UPDATE Cliente SET $setClause WHERE id = :id";

            // Imprimir la consulta SQL antes de ejecutarla
            echo "Consulta SQL: " . htmlspecialchars($sql) . PHP_EOL;

            // Prepara la declaración
            $stmt = Conexion::prepare($sql);

            // Asociar los parámetros
            foreach ($campos as $campo => &$valor) {
                $stmt->bindParam(":$campo", $valor);
            }
            $stmt->bindParam(':id', $this->id);

            if ($stmt->execute()) {
                // Imprimir el número de filas afectadas por la consulta SQL
                echo "Número de filas afectadas: " . htmlspecialchars($stmt->rowCount()) . PHP_EOL;

                echo "Cliente modificado exitosamente.";
                return true;
            } else {
                echo "No se pudo modificar el cliente.";
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al modificar cliente: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }

    // busca un cliente por su ID en la base de datos, esta harcodeado lo del a db, podria cambiarlo
    public static function buscarPorId($clienteId)
    {
        $host = 'sql10.freesqldatabase.com';
        $dbname = 'sql10768330';
        $usuario = 'sql10768330';
        $contrasena = '1bFDBkBLRb';

        try {
            // Crear la conexión PDO
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $contrasena);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Preparar la consulta
            $stmt = $pdo->prepare("SELECT id, nombre, dni FROM Cliente WHERE id = :id");
            $stmt->execute([':id' => $clienteId]);

            // Recuperar los datos del cliente
            $clienteData = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si existen datos del cliente, proceder a crear el objeto Cliente
            if ($clienteData) {
                $cliente = new Cliente($clienteData['nombre'], $clienteData['dni'], $clienteData['id']);

                // Asignar las mascotas del cliente utilizando el método estático de la clase Mascota
                $cliente->setMascotas(Mascota::getMascotasPorClienteId($cliente->getId()));

                return $cliente;
            } else {
                // Si no se encontró el cliente, devolver null
                return null;
            }
        } catch (PDOException $e) {
            // Manejar errores de conexión o ejecución de la consulta
            error_log("Error en buscarPorId: " . $e->getMessage()); // Registrar el error
            return null;
        }
    }
}
