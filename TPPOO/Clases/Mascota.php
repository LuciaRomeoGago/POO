<?php
class Mascota
{
    private $nombre;
    private $edad;
    private $raza;
    private $id;
    private $historialMedico;
    private $clienteId;

    public function __construct($nombre, $edad, $raza, $historialMedico)
    {
        $this->nombre = $nombre;
        $this->edad = $edad;
        $this->raza = $raza;
        $this->id = uniqid('Mascota_', true); //genera id unico
        $this->historialMedico = $historialMedico;
        $this->clienteId = null; //al crear mascota no se sabe a que cliente pertenece aun
    }

    // Getters, para obtener el valor de la propiedad

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getEdad()
    {
        return $this->edad;
    }

    public function getRaza()
    {
        return $this->raza;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getClienteId()
    {
        return $this->clienteId;
    }

    public function getHistorialMedico()
    {
        return $this->historialMedico;
    }

    //Para obtener las mascotas asociadas a un cliente especifico
    public static function getMascotasPorClienteId($clienteId)
    {
        try {
            // Obtener la conexión reutilizable
            $pdo = Conexion::getConexion();

            // Preparar la consulta
            $stmt = $pdo->prepare("SELECT id, nombre, edad, raza, historialMedico 
                                           FROM Mascota 
                                           WHERE clienteId = :clienteId");
            // Ejecutar la consulta
            $stmt->execute([':clienteId' => $clienteId]);

            // Obtener todas las mascotas
            $mascotasData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $mascotas = [];
            // Crear objetos de tipo Mascota a partir de los resultados
            foreach ($mascotasData as $mascotaData) {
                $mascota = new Mascota(
                    $mascotaData['nombre'],
                    $mascotaData['edad'],
                    $mascotaData['raza'],
                    $mascotaData['historialMedico'],
                    $mascotaData['id']
                );
                $mascotas[] = $mascota;
            }

            return $mascotas;
        } catch (PDOException $e) {
            // Manejar errores de conexión o consulta
            error_log("Error en getMascotasByClienteId: " . $e->getMessage());
            return []; // Retorna un arreglo vacío en caso de error
        }
    }


    //Setters, establece el valor de la propiedad


    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setEdad($edad)
    {
        $this->edad = $edad;
    }

    public function setRaza($raza)
    {
        $this->raza = $raza;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setHistorialMedico($historialMedico)
    {
        $this->historialMedico = $historialMedico;
    }

    //establece el id del cliente al que pertenece la mascota
    public function setClienteId($clienteId)
    {
        $this->clienteId = $clienteId;
    }


    // Método para mostrar información de la mascota, previene que se interprete como otro tipo de codigo/lenguaje
    public function mostrar()
    {
        echo "Nombre: " . htmlspecialchars($this->getNombre())
            . ", Edad: " . htmlspecialchars($this->getEdad())
            . ", Raza: " . htmlspecialchars($this->getRaza())
            . ", Id: " . htmlspecialchars($this->getId())
            . ", Historial Médico: " . htmlspecialchars($this->getHistorialMedico())
            . PHP_EOL;
    }

    //guarda o actualiza la info de la masctoa en la db
    public function guardar()
    {
        try {
            // Consulta SQL para insertar una nueva mascota
            $sql = "INSERT INTO Mascota (nombre, edad, raza, historialMedico, clienteId) 
                    VALUES (:nombre, :edad, :raza, :historialMedico, :clienteId)";
            $stmt = Conexion::prepare($sql);  // Utiliza tu clase de conexión

            // Vincular los parámetros
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':edad', $this->edad);
            $stmt->bindParam(':raza', $this->raza);
            $stmt->bindParam(':historialMedico', $this->historialMedico);
            $stmt->bindParam(':clienteId', $this->clienteId);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Si es una inserción, obtener el ID generado
                if ($this->id == null) {
                    $this->setId(Conexion::getLastId());
                }
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al guardar/actualizar mascota: " . htmlspecialchars($e->getMessage());
            return false;
        }
    }

    // Borra el mascota de la base de datos
    public function borrar()
    {
        try {
            //prepara la consulta SQL
            $sql = "DELETE FROM Mascota WHERE id = :id";
            //prepara la declaracion
            $stmt = Conexion::prepare($sql);
            //asocia parametros
            $stmt->bindParam(':id', $this->id);

            if ($stmt->execute()) {
                echo "Mascota borrada exitosamente.";
                return true;
            } else {
                echo "No se pudo borrar la mascota.";
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al borrar la mascota: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }

    public function modificar(Mascota $mascota)
    {
        try {
            $sql = "UPDATE Mascota SET ";
            $params = [];
            $updates = [];

            if (!empty($mascota->getNombre())) {
                $updates[] = "nombre = :nombre";
                $params[':nombre'] = $mascota->getNombre();
            }

            if (!empty($mascota->getEdad())) {
                $updates[] = "edad = :edad";
                $params[':edad'] = $mascota->getEdad();
            }

            if (!empty($mascota->getRaza())) {
                $updates[] = "raza = :raza";
                $params[':raza'] = $mascota->getRaza();
            }

            if (!empty($mascota->getHistorialMedico())) {
                $updates[] = "historialMedico = :historialMedico";
                $params[':historialMedico'] = $mascota->getHistorialMedico();
            }

            if (empty($updates)) {
                echo "No hay cambios para guardar.";
                return false;
            }

            $sql .= implode(', ', $updates) . " WHERE id = :id";
            $params[':id'] = $mascota->getId();

            $stmt = Conexion::prepare($sql);
            if ($stmt->execute($params)) {
                echo "Mascota modificada exitosamente.";
                return true;
            } else {
                echo "No se pudo modificar la mascota.";
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al modificar la mascota: " . htmlspecialchars($e->getMessage());
            return false;
        }
    }
}
