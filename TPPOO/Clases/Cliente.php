<?php
    class Cliente {
        private $nombre;
        private $dni;
        private $id; 
        private $referenciaAnimal; 

        public function __construct($nombre,$dni, $id = null){
            $this->nombre=$nombre;
            $this->dni=$dni;
            $this->id=$id ?? uniqid('Cliente_', true); // unico de c/cliente, si no se proporciona, se genera automaticamente
            $this->referenciaAnimal=[]; //array, almacena las mascotas asociadas al cliente
        }

        // Getters, permiten acceder a las propiedades privadas de la clase sin que sus valores se modifiquen directamente

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

        // Setters, permiten modificar las propiedades, algunas retornan $this para permitir encadenar llamadas

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

        public function setMascotas($mascotas){ //establece las mascotas asociadas al cliente si es necesario
            $this->referenciaAnimal = $mascotas;
        }


        //Metodos

        // Agregar una mascota, establece el id de cliente, y permite agregar una instancia de mascota a este
        public function agregarMascota(Mascota $mascota) {
            $mascota->setClienteId($this->getId()); 
          $this->referenciaAnimal[] = $mascota; 
        }

        // Mostrar todas las mascotas del cliente, verifica si hay mascotas y las imprime con el metodo mostrar() de c/mascota
        public function mostrarMascotas() {
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
        public function mostrar(){
        echo "Dni: " . $this->getDni() 
            . ", Nombre: " . $this->getNombre() 
            . PHP_EOL;
            $this->mostrarMascotas(); 
        }

        // Guarda en la base de datos
        public function guardar() {
          try {
            //prepara y ejecuta consulta para insertar el cliente
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

        // busca un cliente por su ID en la base de datos, esta harcodeado lo del a db, podria cambiarlo
        public static function buscarPorId($clienteId) {
            $host = 'sql10.freesqldatabase.com';      
            $dbname = 'sql10763804';   
            $usuario = 'sql10763804';      
            $contrasena = 'YW49tuyKvg';      
    
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $contrasena);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
                $stmt = $pdo->prepare("SELECT id, nombre, dni 
                                       FROM Cliente 
                                       WHERE id = :id"); //me dice que sino use en vez id=? pero no me funcionaba asi
                $stmt->execute([':id'=>$clienteId]);
                $clienteData = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($clienteData) {
                    // Crear el cliente
                    $cliente = new Cliente($clienteData['nombre'], $clienteData['dni'], $clienteData['id']);
    
                    // Tengo método estático en la clase Mascota para obtener las mascotas por ID de cliente
                    $cliente->setMascotas(Mascota::getMascotasByClienteId($cliente->getId()));
    
                    return $cliente;
                } else {
                    return null;
                }
    
            } catch (PDOException $e) {
                // Manejar errores de conexión o consulta
                error_log("Error en buscarPorId: " . $e->getMessage()); // Log del error
                return null; 
            } finally {
                //Cerrar la conexion
                $pdo = null;
            }
        }
    }
