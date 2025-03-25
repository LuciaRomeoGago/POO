<?php
require_once('clases' . DIRECTORY_SEPARATOR . 'Veterinario.php');
require_once('clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('clases' . DIRECTORY_SEPARATOR . 'Producto.php');

class VeterinarioModelo{
    
     public function guardar(Veterinario $veterinario)
     {
         try {
             $sql = "INSERT INTO Veterinario (nombre, especialidad) VALUES (:nombre, :especialidad)";
             $stmt = Conexion::prepare($sql);

             $nombre = $veterinario->getNombre();
             $especialidad=$veterinario->getEspecialidad();

             $stmt->bindParam(':nombre', $nombre); // vincula los parametros a valores correspondientes
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

     public static function obtenerTodos(): array
     {
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
 
     // Borrar veterinario de la base de datos
     public function borrar(Veterinario $veterinario)
     {
         try {
             $sql = "DELETE FROM Veterinario WHERE id = :id";
             $stmt = Conexion::prepare($sql);
             
             $id=$veterinario->getId();
 
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
 
 
     // Método para modificar el veterinario en la base de datos
     public function modificar(Veterinario $veterinario, $campos)
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
 
     // busca un veterinario por su ID en la base de datos, esta harcodeado lo del a db, podria cambiarlo
     public static function buscarPorId(Veterinario $veterinarioId)
     {try {
        // Preparar la consulta
        $stmt = Conexion::prepare("SELECT id, nombre, especialidad FROM Veterinario WHERE id = :id");
        $stmt->execute([':id' => $veterinarioId]);

        // Recuperar los datos del veterinario
        $veterinarioData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si existen datos del veterinario, proceder a crear el objeto Cliente
        if ($veterinarioData) {
            $veterinario = new Veterinario($veterinarioData['nombre'], $veterinarioData['especialidad'], $veterinarioData['id']);

            return $veterinario;
        } else {
            // Si no se encontró el veterinario, devolver null
            return null;
        }
    } catch (PDOException $e) {
        // Manejar errores de conexión o ejecución de la consulta
        error_log("Error en buscarPorId: " . $e->getMessage()); // Registrar el error
        return null;
    }
}
}