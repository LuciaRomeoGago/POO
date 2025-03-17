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

        public function getClienteId(){
            return $this->clienteId;
        }

        public function getHistorialMedico(){
            return $this->historialMedico;
        }

            //Para obtener las mascotas asociadas a un cliente especifico
            public static function getMascotasPorClienteId($clienteId) {
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
            /*$sql = "";
            */try {/*
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
    */
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
    
                /*// Si es una actualización, vincular el ID
                if ($this->id != null) {
                    $stmt->bindParam(':id', $this->id);
                }
    */
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
       /* public function modificar() {
            try {  
                //prepara la consulta SQL
                $sql = "UPDATE Mascota SET 
                      nombre = :nombre,
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
        
        public function modificar(Mascota $mascota) {
            try {  
                // Prepara la consulta SQL
                $sql = "UPDATE Mascota SET 
                            nombre = :nombre,
                            edad = :edad,
                            raza = :raza,
                            historialMedico = :historialMedico
                        WHERE id = :id";
        
                // Prepara la declaración
                $stmt = Conexion::prepare($sql);
                
                // Asocia parámetros
                $stmt->bindParam(':nombre', $mascota->getNombre());
                $stmt->bindParam(':edad', $mascota->getEdad());
                $stmt->bindParam(':raza', $mascota->getRaza());
                $stmt->bindParam(':historialMedico', $mascota->getHistorialMedico());
                $stmt->bindParam(':id', $mascota->getId());
        
                // Ejecuta la consulta
                if ($stmt->execute()) { 
                    echo "Mascota modificada exitosamente."; 
                    return true; 
                } else { 
                    echo "No se pudo modificar la mascota."; 
                    return false; 
                }
        
            } catch (PDOException $e) {
                echo "Error al modificar la mascota: " . $e->getMessage();
                return false; // Retorna false en caso de error
            }
    }

        public function modificar(Mascota $mascota) {
            try {
                // Inicializa la consulta SQL y un array para los parámetros
                $sql = "UPDATE Mascota SET ";
                $params = [];
                $updates = []; // Para rastrear qué campos se están actualizando
        
                // Verifica y agrega cada campo si ha sido modificado
                if (!empty($mascota->getNombre())) {
                    $updates[] = "nombre = :nombre";
                    $params[':nombre'] = htmlspecialchars($mascota->getNombre()); // Sanitización básica
                }
        
                if (!empty($mascota->getEdad())) {
                    $updates[] = "edad = :edad";
                    $params[':edad'] = $mascota->getEdad();
                }
        
                if (!empty($mascota->getRaza())) {
                    $updates[] = "raza = :raza";
                    $params[':raza'] = htmlspecialchars($mascota->getRaza()); // Sanitización básica
                }
        
                if (!empty($mascota->getHistorialMedico())) {
                    $updates[] = "historialMedico = :historialMedico";
                    $params[':historialMedico'] = htmlspecialchars($mascota->getHistorialMedico()); // Sanitización básica
                }
        
                // Verifica si hay campos para actualizar
                if (empty($updates)) {
                    return ['success' => false, 'message' => "No hay cambios para guardar."];
                }
        
                // Une los campos a actualizar
                $sql .= implode(', ', $updates);
        
                // Agrega la cláusula WHERE
                $sql .= " WHERE id = :id";
                $params[':id'] = $mascota->getId();
        
                // Prepara la declaración
                $stmt = Conexion::prepare($sql);
        
                // Ejecuta la consulta
                if ($stmt->execute($params)) {
                    return ['success' => true, 'message' => "Mascota modificada exitosamente."];
                } else {
                    return ['success' => false, 'message' => "No se pudo modificar la mascota.", 'errorInfo' => $stmt->errorInfo()];
                }
        
            } catch (PDOException $e) {
                return ['success' => false, 'message' => "Error al modificar la mascota: " . $e->getMessage()];
            }
        }
     */
       
     public function modificar(Mascota $mascota) {
        try {
            // Inicializa la consulta SQL y un array para los parámetros
            $sql = "UPDATE Mascota SET nombre = :nombre, edad = :edad, raza = :raza, historialMedico = :historialMedico
                    WHERE id = :id";
            $stmt = Conexion::prepare($sql);
    
            // Asocia los parámetros
            $stmt->bindParam(':id', $mascota->getId());
            $stmt->bindParam(':nombre', $mascota->getNombre());
            $stmt->bindParam(':edad', $mascota->getEdad());
            $stmt->bindParam(':raza', $mascota->getRaza());
            $stmt->bindParam(':historialMedico', $mascota->getHistorialMedico());
    
            if ($stmt->execute()) { 
                echo "La mascota se ha modificado exitosamente."; 
                return true; 
            } else { 
                echo "No se pudo modificar la mascota."; 
                return false; 
            }
        } catch (PDOException $e) {
            echo "Error al modificar la mascota: " . $e->getMessage();
        }
        return false;
    }
    
    
        
        

      

    }
