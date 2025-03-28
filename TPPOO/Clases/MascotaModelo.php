<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Mascota.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaModelo.php');

class MascotaModelo {

    // Agrega una Mascota
    public function guardar(Mascota $mascota) {
        try {
            $sql = "INSERT INTO Mascota (nombre, edad, raza, historialMedico, clienteId) 
                    VALUES (:nombre, :edad, :raza, :historialMedico, :clienteId)";
            $stmt = Conexion::prepare($sql);

            $nombre = $mascota->getNombre();
            $edad = $mascota->getEdad();
            $raza = $mascota->getRaza();
            $historialMedico = $mascota->getHistorialMedico();
            $clienteId = $mascota->getClienteId();

            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':edad', $edad);
            $stmt->bindParam(':raza', $raza);
            $stmt->bindParam(':historialMedico', $historialMedico);
            $stmt->bindParam(':clienteId', $clienteId);

            if ($stmt->execute()) {
                $id = Conexion::getLastId();
                $mascota->setId($id);
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al guardar/actualizar mascota: " . htmlspecialchars($e->getMessage());
            return false;
        }
    }

    // Modifica una Mascota
    public function modificar(Mascota $mascota) {
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
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al modificar la mascota: " . htmlspecialchars($e->getMessage());
            return false;
        }
    }

    // Borra una Mascota
    public function borrar(Mascota $mascota){
        try {
            $sql = "DELETE FROM Mascota WHERE id = :id";
            $stmt = Conexion::prepare($sql);
            $id = $mascota->getId();
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error al borrar la mascota: " . htmlspecialchars($e->getMessage());
        }
        return false;
    }

    // Obtiene Mascota especifica de un Cliente
    public static function getPorClienteId($clienteId)
    {
        try {   $pdo = Conexion::getConexion();

            $stmt = $pdo->prepare("SELECT id, nombre, edad, raza, historialMedico 
                                        FROM Mascota 
                                        WHERE clienteId = :clienteId");
            $stmt->execute([':clienteId' => $clienteId]);

            $mascotasData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $mascotas = [];
            foreach ($mascotasData as $mascotaData) {
                $mascota = new Mascota(
                    $mascotaData['nombre'],
                    $mascotaData['edad'],
                    $mascotaData['raza'],
                    $mascotaData['historialMedico'],
                    $mascotaData['id']
                );
                $mascota->setId($mascotaData['id']);
                $mascotas[] = $mascota;
            }
            return $mascotas;
        } catch (PDOException $e) {
            error_log("Error en getMascotasByClienteId: " . $e->getMessage());
            return [];
        }
    }

    // Actualiza una mascota específica
    public static function actualizarMascota(Mascota $mascota)
    {
        try {
            $pdo = Conexion::getConexion();
            $stmt = $pdo->prepare("SELECT nombre, edad, raza, historialMedico 
                                   FROM Mascota 
                                   WHERE id = :id");
            $stmt->execute([':id' => $mascota->getId()]);
            $mascotaData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($mascotaData) {
                $mascota->setNombre($mascotaData['nombre']);
                $mascota->setEdad($mascotaData['edad']);
                $mascota->setRaza($mascotaData['raza']);
                $mascota->setHistorialMedico($mascotaData['historialMedico']);
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar mascota desde BD: " . $e->getMessage());
        }
    }
}
