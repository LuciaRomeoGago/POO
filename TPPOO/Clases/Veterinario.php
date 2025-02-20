<?php
class Veterinario {
    private $nombre;
    private $especialidad;
    private $id; // Se usa  después de guardar

    public function __construct($nombre, $especialidad, $id = null) {
        $this->nombre = $nombre;
        $this->especialidad = $especialidad;
        $this->id = $id; // O se asigna si se proporciona
    }

    // Getters
    public function getNombre() {
        return $this->nombre;
    }

    public function getEspecialidad() {
        return $this->especialidad;
    }

    public function getId() {
        return $this->id;
    }

    // Setters
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setEspecialidad($especialidad) {
        $this->especialidad = $especialidad;
    }

    public function setId($id) {
        $this->id = $id;
    }


    // Método para mostrar información del veterinario
    public function mostrar() {
        echo "ID: " . htmlspecialchars($this->getId()) 
            . ", Nombre: " . htmlspecialchars($this->getNombre()) 
            . ", Especialidad: " . htmlspecialchars($this->getEspecialidad()) 
            . PHP_EOL;
    }

   /////////////////////// // Guardar en la base de datos
    public function guardar() {
        try {
            $sql = "INSERT INTO Veterinario (nombre, especialidad) VALUES (:nombre, :especialidad)";
            $stmt = Conexion::prepare($sql);
            $stmt->bindParam(':nombre', $this->nombre); // vincula los parametros a valores correspondientes
            $stmt->bindParam(':especialidad', $this->especialidad);

            if ($stmt->execute()) {
                // Asignar ID si es necesario
                $this->setId(Conexion::getLastId());
                return true; 
            }
        } catch (PDOException $e) {
            echo "Error al guardar veterinario: " . htmlspecialchars($e->getMessage());
        }
        return false; 
    }

    // Borrar veterinario de la base de datos
    public function borrar() {
        try {
            $sql = "DELETE FROM Veterinario WHERE id = :id";
            $stmt = Conexion::prepare($sql);
            $stmt->bindParam(':id', $this->id);

            if ($stmt->execute()) {
                echo "Veterinario borrado exitosamente.";
                return true; 
            } else {
                echo "No se pudo borrar el veterinario.";
                return false; 
            }
        } catch (PDOException $e) {
            echo "Error al borrar veterinario: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }

    // Método para modificar el veterinario en la base de datos
    public function modificar() {
        try {  
            $sql = "UPDATE Veterinario SET nombre = :nombre, especialidad = :especialidad WHERE id = :id";
            $stmt = Conexion::prepare($sql);
            
            // Asocia parámetros
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':especialidad', $this->especialidad);
            $stmt->bindParam(':id', $this->id);

            if ($stmt->execute()) { 
                echo "Veterinario modificado exitosamente."; 
                return true; 
            } else { 
                echo "No se pudo modificar el veterinario."; 
                return false; 
            }
        } catch (PDOException $e) {
            echo "Error al modificar veterinario: " . htmlspecialchars($e->getMessage());
        }
        return false; 
    }
}