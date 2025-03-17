<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Mascota.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'interface.php');

class MascotaManager extends arrayIdManager
{ //la clase puede manejar un arreglo de objetos
    private $cliente;

    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
        $this->levantar();
    }

    // Levanta(para obtener) las mascotas del cliente desde la base de datos
    public function levantar()
    {
        try {
            $sql = "SELECT * FROM Mascota WHERE clienteId = :clienteId";
            $stmt = Conexion::prepare($sql);
            $clienteId = $this->cliente->getId();
            $stmt->bindParam(':clienteId', $clienteId);
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
                $nuevaMascota->setId($mascota->id);
                // Agregar al arreglo
                $this->agregar($nuevaMascota);
            }
        } catch (PDOException $e) {
            error_log("Error al levantar mascotas: " . $e->getMessage());
        }
    }

    // Método para agregar una mascota
    public function alta()
    {
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
    /* public function modificar($clienteId) {
            $id = Menu::readln("Ingrese Id de la mascota a modificar: ");
            $mascota = $this->getPorId($id); // da la mascota por su ID

           // Verifica si la mascota no existe
         if ($mascota === null) {
             Menu::writeln("No se encontró ninguna mascota con ese ID." . PHP_EOL);
             return; // Salir del método si no se encuentra la mascota
            }

            if ($mascota->getClienteId() !== $clienteId) {
                Menu::writeln("La mascota no pertenece al cliente." . PHP_EOL);
                return; // Salir del método si no pertenece al cliente
            }
                Menu::writeln('Está por modificar la  siguiente mascota del sistema: '. PHP_EOL);
                $mascota->mostrar();

                $rta = Menu::readln(PHP_EOL . '¿Está seguro? S/N: ');            
                if($rta == 'S' or $rta == 's') {  
                     $nombre = trim(Menu::readln("Ingrese el nuevo nombre de la mascota (deje en blanco para no modificar): "));
                     if ($nombre != ""){
                         $mascota->setNombre($nombre);
                        }
               
                      $edad = trim(Menu::readln("Ingrese la nueva edad de la mascota (deje en blanco para no modificar): "));
                      if ($edad != "" && is_numeric($edad)) {
                         $mascota->setEdad((int)$edad); // Asegúrate que sea un número entero
                        }
            
                      $raza = trim(Menu::readln("Ingrese la nueva raza de la mascota (deje en blanco para no modificar): "));
                       if ($raza != ""){
                         $mascota->setRaza($raza);
                        }
            
                      $historialMedico = trim(Menu::readln("Ingrese el nuevo historial médico de la mascota (deje en blanco para no modificar): "));
                       if ($historialMedico !== "") {
                         $mascota->setHistorialMedico($historialMedico);
                        }
    
                      //Lo modifica en la Base de Datos
                       $mascota->modificar();
                      Menu::readln("La mascota fue modificada con éxito");
                    } else {
                     Menu::writeln("Operacion cancelada.");
        }
    }
    */


    // Método para eliminar una mascota
    public function baja()
    {
        // Obtener las mascotas asociadas al cliente
        $mascotas = $this->getArreglo(); // debe devolver las mascotas
        if (empty($mascotas)) {
            echo "Este cliente no tiene mascotas para eliminar, intente de nuevo." . PHP_EOL;
            return; // Salir si no hay mascotas
        }

        Menu::cls(); // Limpiar la pantalla si es necesario
        Menu::subtitulo('Lista de mascotas del cliente para eliminar: ' . htmlspecialchars($this->cliente->getNombre()));

        foreach ($mascotas as $mascota) {
            $mascota->mostrar(); // Llama al método mostrar() de cada mascota
        }

        Menu::waitForEnter(); // Esperar a que el usuario presione Enter antes de continuar
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
            Menu::writeln('Está por eliminar la siguiente mascota: ' . PHP_EOL);
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
    public function mostrar()
    {
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


    public function modificar2()
    {
        // Obtener las mascotas asociadas al cliente
        $mascotas = $this->getArreglo(); // debe devolver las mascotas
        if (empty($mascotas)) {
            echo "Este cliente no tiene mascotas para modificar, intente de nuevo." . PHP_EOL;
            return; // Salir si no hay mascotas
        }

        $this->mostrar();

        while (true) {
            $idMascota = Menu::readln("Ingrese ID de la mascota a modificar: ");

            $mascotaEncontrada = null;
            foreach ($mascotas as $mascota) {
                if ($mascota->getId() == $idMascota) {
                    $mascotaEncontrada = $mascota;
                    break;
                }
            }

            if (!$mascotaEncontrada) {
                echo ("No se encontró una mascota con ese ID para este cliente. Intente de nuevo" . PHP_EOL);
                $rta = Menu::readln("¿Desea intentarlo de nuevo? S/N: ");
                if (strtolower($rta) !== 's') {
                    break; // Salir del bucle si no quiere intentarlo de nuevo
                }
            } else {
                Menu::writeln('Está por modificar la siguiente mascota del sistema: ' . PHP_EOL);
                $mascotaEncontrada->mostrar();
                $rta = Menu::readln(PHP_EOL . '¿Está seguro? S/N: ');
                if ($rta == 'S' or $rta == 's') {
                    $nombre = trim(Menu::readln("Ingrese el nuevo nombre de la mascota (deje en blanco para no modificar): "));
                    if ($nombre != "") {
                        $mascotaEncontrada->setNombre($nombre);
                    }

                    $edad = trim(Menu::readln("Ingrese la nueva edad de la mascota (deje en blanco para no modificar): "));
                    if ($edad != "" && is_numeric($edad)) {
                        $mascotaEncontrada->setEdad((int)$edad); // número entero tiene que ser
                    }

                    $raza = trim(Menu::readln("Ingrese la nueva raza de la mascota (deje en blanco para no modificar): "));
                    if ($raza != "") {
                        $mascotaEncontrada->setRaza($raza);
                    }

                    // Si no se modificó el historial médico, asegúrate de que tenga un valor
                    if (!$mascotaEncontrada->getHistorialMedico()) {
                        $mascotaEncontrada->setHistorialMedico('Sin historial médico');
                    }
                }

                if ($this->modificar($mascotaEncontrada)) {
                    echo "La mascota se ha modificado con éxito." . PHP_EOL;
                } else {
                    echo "Hubo un error al modificar la mascota." . PHP_EOL;
                }
                break; // Salir del bucle si se encontró y modificó la mascota
            }
        }
        Menu::waitForEnter();
    }
    
    

         
        }
        /* $idMascota = Menu::readln("Ingrese ID de la mascota a modificar: ");
         
         $mascotaEncontrada = null;
         foreach($mascotas as $mascota) {
            if ($mascota->getId() == $idMascota) {
                $mascotaEncontrada = $mascota;
                break;
            }
         }
         
         /*if (!$mascotaEncontrada) {
            echo ("No se encontro una mascota con ese ID para este cliente. Intente de nuevo" . PHP_EOL);
         }

             Menu::writeln('Está por modificar la  siguiente mascota del sistema: '. PHP_EOL);
             $mascotaEncontrada->mostrar();
             $rta = Menu::readln(PHP_EOL . '¿Está seguro? S/N: ');            
             if($rta == 'S' or $rta == 's') {  

                  $nombre = trim(Menu::readln("Ingrese el nuevo nombre de la mascota (deje en blanco para no modificar): "));
                  if ($nombre != ""){
                      $mascotaEncontrada->setNombre($nombre);
                     }
            
                   $edad = trim(Menu::readln("Ingrese la nueva edad de la mascota (deje en blanco para no modificar): "));
                   if ($edad != "" && is_numeric($edad)) {
                      $mascotaEncontrada->setEdad((int)$edad); // número entero tiene que ser
                     }
         
                   $raza = trim(Menu::readln("Ingrese la nueva raza de la mascota (deje en blanco para no modificar): "));
                    if ($raza != ""){
                      $mascotaEncontrada->setRaza($raza);
                     }
         
                   $historialMedico = trim(Menu::readln("Ingrese el nuevo historial médico de la mascota (deje en blanco para no modificar): "));
                    if ($historialMedico !== "") {
                      $mascotaEncontrada->setHistorialMedico($historialMedico);
                     }
 
                   //Lo modifica en la Base de Datos
                   if ($mascotaEncontrada) {
                    Menu::writeln('Está por modificar la  siguiente mascota del sistema: '. PHP_EOL);
                    $mascotaEncontrada->mostrar();
                    $rta = Menu::readln(PHP_EOL . '¿Está seguro? S/N: ');
                    if($rta == 'S' or $rta == 's') {
                        $nombre = trim(Menu::readln("Ingrese el nuevo nombre de la mascota (deje en blanco para no modificar): "));
                                  if ($nombre != ""){
                                      $mascotaEncontrada->setNombre($nombre);
                                     }
                            
                                   $edad = trim(Menu::readln("Ingrese la nueva edad de la mascota (deje en blanco para no modificar): "));
                                   if ($edad != "" && is_numeric($edad)) {
                                      $mascotaEncontrada->setEdad((int)$edad); // número entero tiene que ser
                                     }
                         
                                   $raza = trim(Menu::readln("Ingrese la nueva raza de la mascota (deje en blanco para no modificar): "));
                                    if ($raza != ""){
                                      $mascotaEncontrada->setRaza($raza);
                                     }
                         
                                   $historialMedico = trim(Menu::readln("Ingrese el nuevo historial médico de la mascota (deje en blanco para no modificar): "));
                                    if ($historialMedico !== "") {
                                      $mascotaEncontrada->setHistorialMedico($historialMedico);
                                     }
                                }
                            } else { 
                                 echo ("No se encontro una mascota con ese ID para este cliente. Intente de nuevo" . PHP_EOL);
                            }                
            if ($this->modificar($mascotaEncontrada)) {
                echo "La mascota se ha modificado con éxito." . PHP_EOL;
                return true;
            } else {
                echo "Hubo un error al modificar la mascota." . PHP_EOL;
                return false;
            }
            Menu::waitForEnter();
        } */
/* while (true) {
 $idMascota = Menu::readln("Ingrese ID de la mascota a modificar: ");
         
         $mascotaEncontrada = null;
         foreach($mascotas as $mascota) {
            if ($mascota->getId() == $idMascota) {
                $mascotaEncontrada = $mascota;
                break;
            }
        }
            if (!$mascotaEncontrada) {
            echo ("No se encontro una mascota con ese ID para este cliente. Intente de nuevo" . PHP_EOL);
            $rta = Menu::readln("¿Desea intentarlo de nuevo? S/N: ");
            if (strtolower($rta) !== 's') {
                break; // Salir del bucle si no quiere intentarlo de nuevo
            }
        } else {
            Menu::writeln('Está por modificar la  siguiente mascota del sistema: '. PHP_EOL);
            $mascotaEncontrada->mostrar();
            $rta = Menu::readln(PHP_EOL . '¿Está seguro? S/N: ');
            if($rta == 'S' or $rta == 's') {
                $nombre = trim(Menu::readln("Ingrese el nuevo nombre de la mascota (deje en blanco para no modificar): "));
                if ($nombre != ""){
                    $mascotaEncontrada->setNombre($nombre);
                }

                $edad = trim(Menu::readln("Ingrese la nueva edad de la mascota (deje en blanco para no modificar): "));
                if ($edad != "" && is_numeric($edad)) {
                    $mascotaEncontrada->setEdad((int)$edad); // número entero tiene que ser
                }

                $raza = trim(Menu::readln("Ingrese la nueva raza de la mascota (deje en blanco para no modificar): "));
                if ($raza != ""){
                    $mascotaEncontrada->setRaza($raza);
                }

                $historialMedico = trim(Menu::readln("Ingrese el nuevo historial médico de la mascota (deje en blanco para no modificar): "));
                if ($historialMedico !== "") {
                    $mascotaEncontrada->setHistorialMedico($historialMedico);
                }
            }

            if ($this->modificar($mascotaEncontrada)) {
                echo "La mascota se ha modificado con éxito." . PHP_EOL;
            } else {
                echo "Hubo un error al modificar la mascota." . PHP_EOL;
            }
            break; // Salir del bucle si se encontró y modificó la mascota
        }
    }
    Menu::waitForEnter();
}*/