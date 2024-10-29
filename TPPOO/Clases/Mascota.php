<?php
     class Mascota { //deberia estar asociado a su dueno = cliente
        private $nombre;
        private $edad; // podria poner fecha de nacimiento, pero en termino de animales parece innecesario
        private $raza;
        private $id;
        private $historialMedico;
        private $clienteId;

        public function __construct($nombre,$edad, $raza, $id, $historialMedico, $clienteId){
            $this->nombre=$nombre;
            $this->edad=$edad;
            $this->raza=$raza;
            $this->id=$id;
            $this->historialMedico=$historialMedico;
            $this->clienteId=$clienteId;
        }

        // Getters

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

        //Setters


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

        public function setClienteId($clienteId) {
            $this->clienteId = $clienteId; // Para almacenar el ID del dueño
        }


        // Método para mostrar información de la mascota
        public function mostrar() {
        echo "Nombre: " . htmlspecialchars($this->getNombre()) 
            . ", Edad: " . htmlspecialchars($this->getEdad()) 
            . ", Raza: " . htmlspecialchars($this->getRaza()) 
            . ", Id: " . htmlspecialchars($this->getId()) 
            . ", Historial Médico: " . htmlspecialchars($this->getHistorialMedico())
            . PHP_EOL;
        }

        // Guarda en la base de datos
        public function guardar() {
            try {
              $sql = "INSERT INTO Mascota (nombre, edad, raza, id, historial_medico, clienteId)
                      VALUES (:nombre, :edad, :raza, :id, :historial_medico, :clienteId)";
                      
              $stmt = Conexion::prepare($sql);
              $stmt->bindParam(':nombre', $this->nombre);
              $stmt->bindParam(':edad', $this->edad);
              $stmt->bindParam(':raza', $this->raza);
              $stmt->bindParam(':id', $this->id);
              $stmt->bindParam(':historial_medico', $this->historialMedico);
              $stmt->bindParam(':cliente_id', $this->clienteId); // Vincula el ID del dueño
            
              if ($stmt->execute()) {
                // Asignar id si es necesario
                  $this->setId(Conexion::getId());
                  return true;
              }
            } catch (PDOException $e) {
              echo "Error al guardar mascota: " . htmlspecialchars($e->getMessage());
            }
            return false;
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
              return false; // Indica fallo
          }
  
  
           // Modifica la mascota en la base de datos
          public function modificar() {
            try {  
              //prepara la consulta SQL
              $sql = "UPDATE Mascota SET nombre = :nombre,
                      edad = :edad,
                      raza = :raza,
                      historial_medico = :historial_medico
                      WHERE id = :id";
  
              //prepara la declaracion
              $stmt = Conexion::prepare($sql);
              
              // Asocia parámetros
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':edad', $this->edad);
            $stmt->bindParam(':raza', $this->raza);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':historial_medico', $this->historialMedico);

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

    }