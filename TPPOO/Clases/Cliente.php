<?php
    class Cliente {
        private $nombre;
        private $dni;
        private $id; 
        private $referenciaAnimal; 

        public function __construct($nombre,$dni, $id = null){
            $this->nombre=$nombre;
            $this->dni=$dni;
            $this->id=$id ?? uniqid('Cliente_', true);
            $this->referenciaAnimal=[];
        }

        // Getters 

        public function getNombre(){
            return $this->nombre;
        } 

        public function getDni(){
            return $this->dni;
        }
        public function getId(){
            return $this->id;
        }

        public function getReferenciaAnimal(){
            return $this->referenciaAnimal;
        }

        // Setters

        public function setNombre($nombre) {
            $this->nombre = $nombre;
        }
    
        public function setDni($dni) {
            $this->dni = $dni;
            return $this;
        }

        public function setId($id) {
            $this->id = $id;
        }

        //Metodos

        // Agregar una mascota
        public function agregarMascota(Mascota $mascota) {
            $mascota->setClienteId($this->getId()); //Establece el clienteId antes de agregar la mascota
          $this->referenciaAnimal[] = $mascota; // Agregar la mascota al array
        }

        // Mostrar todas las mascotas del cliente
        public function mostrarMascotas() {
           if (empty($this->referenciaAnimal)) {
             echo "Este cliente no tiene mascotas." . PHP_EOL;
             return;
            }
        
            echo "Mascotas del cliente " . htmlspecialchars($this->getNombre()) . ":" . PHP_EOL;
            foreach ($this->referenciaAnimal as $mascota) {
             echo "- ";
             $mascota->mostrar(); // Llamar al método mostrar de cada mascota
            }
        }

        //Muestra por pantalla un cliente
        public function mostrar(){
        echo "Dni: " . $this->getDni() 
            . ", Nombre: " . $this->getNombre() 
            . PHP_EOL;
            $this->mostrarMascotas(); // Llama al método para mostrar las mascotas
        }

        // Guarda en la base de datos
        public function guardar() {
          try {
             $sql = "INSERT INTO Cliente (nombre, dni, id)
                    VALUES (:nombre, :dni, :id)";
                    
             $stmt = Conexion::prepare($sql);
             $stmt->bindParam(':nombre', $this->nombre);
             $stmt->bindParam(':dni', $this->dni);
             $stmt->bindParam(':id', $this->id);
            
               if ($stmt->execute()) {
                 foreach ($this->referenciaAnimal as $mascota) {
                      // Asignar el ID del cliente a la mascota antes de guardarla
                     $mascota->setClienteId($this->getId()); 
                     $mascota->guardar(); // Guarda cada mascota en la base de datos
                    }
                 return true;
                }
            } catch (PDOException $e) {
                 echo "Error al guardar cliente: " . htmlspecialchars($e->getMessage());
                }
               return false;
        } 
    

        // Borra el cliente de la base de datos
        public function borrar() {
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


        // Modifica al Cliente en la base de datos
        public function modificar() {
          try {  
            //prepara la consulta SQL
            $sql = "UPDATE Cliente SET nombre = :nombre
            WHERE id = :id";

         // Prepara la declaración
          $stmt = Conexion::prepare($sql);
    
         // Asocia los parámetros
           $stmt->bindParam(':nombre', $this->nombre);
           $stmt->bindParam(':id', $this->id);

            if ($stmt->execute()) { 
                echo "Cliente modificado exitosamente."; 
                return true; 
            } else { 
                echo "No se pudo modificar el cliente."; 
                return false; 
            }

             } catch (PDOException $e) {
            echo "Error al modificar cliente: " . $e->getMessage();
            }
            return false;
        } 
    }
