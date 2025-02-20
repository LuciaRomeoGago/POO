<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Mascota.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'interface.php');

class MascotaManager extends arrayIdManager{ //la clase puede manejar un arreglo de objetos
    private $cliente;

    public function __construct(Cliente $cliente) {
        $this->cliente = $cliente;
        $this->levantar();
    }

    // Levanta(para obtener) las mascotas del cliente desde la base de datos
    public function levantar() {
        try {
             $sql = "SELECT * FROM Mascota 
                WHERE clienteId = :clienteId";
             $stmt = Conexion::prepare($sql);
             $stmt->bindParam(':clienteId', $this->cliente->getId());
             $stmt->execute();
             $mascotas = $stmt->fetchAll(PDO::FETCH_OBJ);

             foreach ($mascotas as $mascota) {
                 $nuevaMascota = new Mascota(
                 $mascota->nombre,
                 $mascota->edad,
                 $mascota->raza,
                 $mascota->historialMedico
                );
             //Establezco el ID despues de crear el objeto(anteriormente el objeto queda sin ID)
             $mascota->setId($mascota->id);
             // Agregar al arreglo
             $this->agregar($nuevaMascota);
            }
        } catch (PDOException $e) {
            error_log("Error al levantar mascotas: ". $e->getMessage());
        }
    }

    // Método para agregar una mascota
    public function alta() {
        // Preguntar por los datos de la nueva mascota
         $nombreMascota = Menu::readln("Ingrese el nombre de la mascota: ");
         $edad = Menu::readln("Ingrese la edad de la mascota: ");
         $raza = Menu::readln("Ingrese la raza de la mascota: ");
         $historialMedico = Menu::readln("Ingrese el historial médico de la mascota: ");

         // Validación de datos
        if (empty($nombreMascota) || empty($edad) || empty($raza)) {
            echo "Error: Los campos nombre, edad y raza son obligatorios." . PHP_EOL;
            return false;
        }

        // Verificar si ya existe una mascota con el mismo nombre para este cliente
            foreach ($this->cliente->getReferenciaAnimal() as $mascota) {
                if ($mascota->getNombre() === $nombreMascota) {
                    echo "Ya existe una mascota con ese nombre para este cliente." . PHP_EOL;
                    return false; 
                }
            }
        
        // Crear y guardar la nueva mascota
        $mascota = new Mascota($nombreMascota, $edad, $raza, $historialMedico);
        $mascota->setClienteId($this->cliente->getId());
            
            if ($mascota->guardar()) {
                $this->agregar($mascota);
                echo "La mascota se ha creado con éxito." . PHP_EOL;
                return true;
            } else {
                echo "Hubo un error al crear la mascota." . PHP_EOL;
                return false;
            }
    }

    // Método para modificar una mascota
    public function modificar($elementoModificado) {
            $id = Menu::readln("Ingrese Id de la mascota a modificar: ");
           
            if($this->existeId($id)){
                $mascotaModificado = $this->getPorId($id);     
                
                if ($mascotaModificado === null) {
                    echo "No se encontró ninguna mascota con ese ID." . PHP_EOL;
                    return; // Salir si no se encuentra la mascota
                }

                Menu::writeln('Está por modificar la  siguiente mascota del sistema: '. PHP_EOL);
                $mascotaModificado->mostrar();
                $rta = Menu::readln(PHP_EOL . '¿Está seguro? S/N: ');            
                if($rta == 'S' or $rta == 's') {  
                    Menu::writeln("Ingrese el nuevo nombre de la mascota (deje en blanco para no modificar): ");
                    $nombre = Menu::readln("Ingrese el nombre: ");
                    if ($nombre != ""){
                        $mascotaModificado->setNombre($nombre);
                    }
                       
                    $edad = Menu::readln("Ingrese la nueva edad de la mascota (deje en blanco para no modificar): ");
                    if ($edad != "") {
                       $mascotaModificado->setEdad($edad);
                    }
                    $raza = Menu::readln("Ingrese la nueva raza de la mascota (deje en blanco para no modificar): ");
                    if ($raza != ""){
                        $mascotaModificado->setRaza($raza);
                    }
                    $historialMedico = Menu::readln("Ingrese el nuevo historial médico de la mascota (deje en blanco para no modificar): ");
                    if ($historialMedico !== "") {
                       $mascotaModificado->setHistorialMedico($historialMedico);
                    }
    
                //Lo modifica en la Base de Datos
                $mascotaModificado->modificar();
                $rta = Menu::readln("La mascota fue modificada con éxito");
               } else {
                    Menu::writeln("El id ingresado no se encuentra entre nuestras mascotas");
            }
        }
    }

    // Método para eliminar una mascota
    public function baja() {
        // Solicitar el ID de la mascota a eliminar
     $id = Menu::readln("Ingrese el ID de la mascota a eliminar: ");
    
     // Verificar si existe la mascota
     if ($this->existeId($id)) {
         $mascota = $this->getPorId($id); 
             if ($mascota === null) {
             echo "No se encontró ninguna mascota con ese ID." . PHP_EOL;
             return; // Salir si no se encuentra la mascota
            }

         // Mostrar información de la mascota a eliminar
         Menu::writeln('Está por eliminar la siguiente mascota: '. PHP_EOL);
         $mascota->mostrar();

         // Confirmar si el usuario desea continuar
         $rta = Menu::readln(PHP_EOL . '¿Está seguro que desea eliminar esta mascota? (S/N): ');
        
         if (strtolower($rta) === 's') {
             // Llamar al método borrar() de la clase Mascota para eliminarla de la base de datos
             if ($mascota->borrar()) {
                 // Eliminar la mascota del arreglo gestionado por MascotaManager
                 $this->eliminarPorId($id); 
                 echo "La mascota ha sido eliminada con éxito." . PHP_EOL;
                } else {
                 echo "Hubo un error al intentar eliminar la mascota." . PHP_EOL;
                }
            } else {
             echo "Eliminación cancelada." . PHP_EOL;
            }
        } else {
         echo "El ID ingresado no se encuentra entre nuestras mascotas." . PHP_EOL;
        }
    }

    // Método para mostrar las mascotas
    public function mostrar() {
        // Obtener las mascotas asociadas al cliente
      $mascotas = $this->getArreglo(); // debe devolver las mascotas
         if (empty($mascotas)) {
         echo "Este cliente no tiene mascotas." . PHP_EOL;
         return; // Salir si no hay mascotas
        }

         Menu::cls(); // Limpiar la pantalla si es necesario
         Menu::subtitulo('Lista de mascotas del cliente: ' . htmlspecialchars($this->cliente->getNombre()));
    
         foreach ($mascotas as $mascota) {
             $mascota->mostrar(); // Llama al método mostrar() de cada mascota
            }

         Menu::waitForEnter(); // Esperar a que el usuario presione Enter antes de continuar
    }
    
}
