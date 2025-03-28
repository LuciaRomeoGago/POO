<?php
require_once('clases' . DIRECTORY_SEPARATOR . 'Veterinario.php');
require_once('clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('clases' . DIRECTORY_SEPARATOR . 'Producto.php');

class VeterinarioModelo
{

    // Crea un Veterinario
    public function guardar(Veterinario $veterinario) {
        try {
            $sql = "INSERT INTO Veterinario (nombre, especialidad) VALUES (:nombre, :especialidad)";
            $stmt = Conexion::prepare($sql);

            $nombre = $veterinario->getNombre();
            $especialidad = $veterinario->getEspecialidad();

            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':especialidad', $especialidad);

            if ($stmt->execute()) {
                $id = Conexion::getLastId();
                $veterinario->setId($id);
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al guardar veterinario: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }

    // Muestra todos los Veterinarios
    public static function obtenerTodos() {
        try {
            $sql = "SELECT * FROM Veterinario";
            $stmt = Conexion::prepare($sql);
            $stmt->execute();

            $veterinariosData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $veterinarios = [];
            foreach ($veterinariosData as $veterinarioData) {
                $veterinarios[] = new Veterinario(
                    $veterinarioData['nombre'],
                    $veterinarioData['especialidad'],
                    $veterinarioData['id']
                );
            }
            return $veterinarios;
        } catch (PDOException $e) {
            error_log("Error al obtener todos los veterinarios: " . $e->getMessage());
            return [];
        }
    }

    // Borra un Veterinario 
    public function borrar(Veterinario $veterinario) {
        try {
            $sql = "DELETE FROM Veterinario WHERE id = :id";
            $stmt = Conexion::prepare($sql);
            $id = $veterinario->getId();
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


    // Modifica un Veterinario
    public function modificar(Veterinario $veterinario, $campos) {
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
            $id = $veterinario->getId();
            $stmt->bindValue(':id', $id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al modificar veterinario: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }

    // Busca un Veterinario por su Id
    public static function buscarPorId(Veterinario $veterinarioId)
    {
        try {

            $stmt = Conexion::prepare("SELECT id, nombre, especialidad FROM Veterinario WHERE id = :id");
            $stmt->execute([':id' => $veterinarioId]);
            $veterinarioData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($veterinarioData) {
                $veterinario = new Veterinario($veterinarioData['nombre'], $veterinarioData['especialidad'], $veterinarioData['id']);
                return $veterinario;
            } else {

                return null;
            }
        } catch (PDOException $e) {
            error_log("Error en buscarPorId: " . $e->getMessage());
            return null;
        }
    }
}
