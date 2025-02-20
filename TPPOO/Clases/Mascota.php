<?php
     class Mascota { 
        private $nombre;
        private $edad;
        private $raza;
        private $id;
        private $historialMedico;
        private $clienteId;

        public function __construct($nombre, $edad, $raza, $historialMedico){
            $this->nombre=$nombre;
            $this->edad=$edad;
            $this->raza=$raza;
            $this->id= uniqid('Mascota_', true); //genera id unico
            $this->historialMedico=$historialMedico;
            $this->clienteId=null; //al crear mascota no se sabe a que cliente pertenece aun
        }

        // Getters, para obtener el valor de la propiedad

        public function getNombre(){
            return $this->nombre;
        } 

        public function getEdad(){
            return $this->edad;
        }

        public function getRaza(){
            return $this->raza;
        }

        public function getId(){
            return $this->id;
        }

        public function getHistorialMedico(){
            return $this->historialMedico;
        }

        //Setters, establece el valor de la propiedad


        public function setNombre($nombre) {
            $this->nombre = $nombre;
        }

        public function setEdad($edad) {
            $this->edad = $edad;
        }

        public function setRaza($raza) {
            $this->raza = $raza;
        }

        public function setId($id) {
            $this->id = $id;
        }

        public function setHistorialMedico($historialMedico) {
            $this->historialMedico = $historialMedico;
        }

        //establece el id del cliente al que pertenece la mascota
        public function setClienteId($clienteId) {
            $this->clienteId = $clienteId; 
        }


        // Método para mostrar información de la mascota, previene que se interprete como otro tipo de codigo/lenguaje
        public function mostrar() {
         echo "Nombre: " . htmlspecialchars($this->getNombre())
            . ", Edad: " . htmlspecialchars($this->getEdad()) 
            . ", Raza: " . htmlspecialchars($this->getRaza()) 
            . ", Id: " . htmlspecialchars($this->getId()) 
            . ", Historial Médico: " . htmlspecialchars($this->getHistorialMedico())
            . PHP_EOL;
        }

        //guarda o actualiza la info de la masctoa en la db
        public function guardar() {
            $sql = "";
            try {
                // Determinar si es una inserción o actualización
                if ($this->id == null) {
                    // Inserción
                    $sql = "INSERT INTO Mascota (nombre, edad, raza, historialMedico, clienteId)
                            VALUES (:nombre, :edad, :raza, :historialMedico, :clienteId)";
                } else {
                    // Actualización
                    $sql = "UPDATE Mascota SET nombre = :nombre, edad = :edad, raza = :raza, 
                            historialMedico = :historialMedico, clienteId = :clienteId
                            WHERE id = :id";
                }
    
                $stmt = Conexion::prepare($sql);  // Utiliza tu clase de conexión
    
                // Vincular los parámetros
                $stmt->bindParam(':nombre', $this->nombre);
                $stmt->bindParam(':edad', $this->edad);
                $stmt->bindParam(':raza', $this->raza);
                $stmt->bindParam(':historialMedico', $this->historialMedico);
                $stmt->bindParam(':clienteId', $this->clienteId);
    
                // Si es una actualización, vincular el ID
                if ($this->id != null) {
                    $stmt->bindParam(':id', $this->id);
                }
    
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
        public function borrar() {
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
  
  
        // Modifica la mascota en la base de datos
        public function modificar() {
            try {  
                //prepara la consulta SQL
                $sql = "UPDATE Mascota SET nombre = :nombre,
                      edad = :edad,
                      raza = :raza,
                      historialMedico = :historialMedico
                      WHERE id = :id";
  
                //prepara la declaracion
                 $stmt = Conexion::prepare($sql);
              
                 // Asocia parámetros
                 $stmt->bindParam(':nombre', $this->nombre);
                 $stmt->bindParam(':edad', $this->edad);
                 $stmt->bindParam(':raza', $this->raza);
                 $stmt->bindParam(':id', $this->id);
                 $stmt->bindParam(':historialMedico', $this->historialMedico);

                 if ($stmt->execute()) { 
                     echo "Mascota modificada exitosamente."; 
                     return true; 
                    } else { 
                      echo "No se pudo modificar la mascota."; 
                     return false; 
                    }
  
                } catch (PDOException $e) {
                 echo "Error al modificar la mascota: " . $e->getMessage();
                }
        } 

        //Para obtener las mascotas asociadas a un cliente especifico, tmb esta hardcodeado la db
        public static function getMascotasByClienteId($clienteId) {
            $host = 'sql10.freesqldatabase.com';      
            $dbname = ' sql10763804';   
            $usuario = 'sql10763804';      
            $contrasena = 'YW49tuyKvg'; 
    
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $contrasena);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
                $stmt = $pdo->prepare("SELECT id, nombre, edad, raza, historialMedico 
                                       FROM Mascota 
                                       WHERE clienteId = :clienteId");
                $stmt->execute([$clienteId]);
                $mascotasData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                $mascotas = [];
                foreach ($mascotasData as $mascotaData) {
                    $mascota = new Mascota(
                        $mascotaData['nombre'],
                        $mascotaData['edad'],
                        $mascotaData['raza'],
                        $mascotaData['historialmedico'],
                        $mascotaData['id']
                    );
                    $mascotas[] = $mascota;
                }
    
                return $mascotas;
    
            } catch (PDOException $e) {
                // Manejar errores de conexión o consulta
                error_log("Error en getMascotasByClienteId: " . $e->getMessage());
                return []; 
            } finally {
                //Cerrar la conexion
                $pdo = null;
            }
        }

    }
