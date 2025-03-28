<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Mascota.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'ABMinterface.php');

class MascotaManager extends arrayIdManager implements ABMinterface
{ 
    private $cliente;

    public function __construct(Cliente $cliente){
        $this->cliente = $cliente;
        $this->levantar();
    }

    public function setCliente(Cliente $cliente){
        $this->cliente = $cliente;
    }

    // Levanta Mascotas del Cliente
    public function levantar(){
        try {
            $mascotas = MascotaModelo::getPorClienteId($this->cliente->getId());

            foreach ($mascotas as $mascota) {
                $this->agregar($mascota);
            }
        } catch (PDOException $e) {
            error_log("Error al levantar mascotas: " . $e->getMessage());
        }
    }

    // Agregar Mascota
    public function alta() {
        $nombreMascota = Menu::readln("Ingrese el nombre de la mascota: ");
        $edad = Menu::readln("Ingrese la edad de la mascota: ");
        $raza = Menu::readln("Ingrese la raza de la mascota: ");
        $historialMedico = Menu::readln("Ingrese el historial médico de la mascota: ");

        // Validación
        if (!preg_match('/^[a-zA-Z\s]+$/', ($nombreMascota))) {
            echo "Error: El nombre de la mascota debe contener solo letras y espacios." . PHP_EOL;
            return false;
        }

        if (!ctype_digit($edad)) {
            echo "Error: La edad debe ser un número entero." . PHP_EOL;
            return false;
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', ($raza))) {
            echo "Error: La raza debe contener solo letras y espacios." . PHP_EOL;
            return false;
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', ($historialMedico))) {
            echo "Error: El historial de la mascota debe contener solo letras y espacios." . PHP_EOL;
            return false;
        }

        if (empty($nombreMascota) || empty($edad) || empty($raza)) {
            echo "Error: Los campos nombre, edad y raza son obligatorios." . PHP_EOL;
            return false;
        }

        foreach ($this->cliente->getReferenciaAnimal() as $mascota) {
            if ($mascota->getNombre() === $nombreMascota) {
                echo "Ya existe una mascota con ese nombre para este cliente." . PHP_EOL;
                return false;
            }
        }

        $mascota = new Mascota($nombreMascota, $edad, $raza, $historialMedico);
        $mascota->setClienteId($this->cliente->getId());

        $mascotaModelo = new MascotaModelo();
        if ($mascotaModelo->guardar($mascota)) {
            $this->agregar($mascota);
            echo "La mascota se ha creado con éxito." . PHP_EOL;
            return true;
        } else {
            echo "Hubo un error al crear la mascota." . PHP_EOL;
            return false;
        }
    }

    // Elimina una Mascota
    public function baja() {
        $mascotas = $this->getArreglo();
        if (empty($mascotas)) {
            echo "Este cliente no tiene mascotas para eliminar, intente de nuevo." . PHP_EOL;
            return; 
        }

        Menu::cls(); 
        Menu::subtitulo('Lista de mascotas del cliente para eliminar: ' . htmlspecialchars($this->cliente->getNombre()));

        foreach ($mascotas as $mascota) {
            $mascota->mostrar();
        }

        Menu::waitForEnter();
        $id = Menu::readln("Ingrese el ID de la mascota a eliminar: ");

        if ($this->existeId($id)) {
            $mascota = $this->getPorId($id);
            if ($mascota === null) {
                echo "No se encontró ninguna mascota con ese ID." . PHP_EOL;
                return; 
            }

            Menu::writeln('Está por eliminar la siguiente mascota: ' . PHP_EOL);
            $mascota->mostrar();


            if (strtolower(Menu::readln(PHP_EOL . '¿Está seguro que desea eliminar esta mascota? (S/N): ')) === 's') {
                $mascotaModelo = new MascotaModelo();

                if ($mascotaModelo->borrar($mascota)) {
                    $this->eliminarPorId($id);
                    echo "Se ha eliminado la mascota con éxito." . PHP_EOL;
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

    // Mostrar Mascotas
    public function mostrar() {
        $this->levantar();
        $mascotas = $this->getArreglo(); 
        if (empty($mascotas)) {
            echo "Este cliente no tiene mascotas." . PHP_EOL;
            return; 
        }

        Menu::cls(); 
        Menu::subtitulo('Lista de mascotas del cliente: ' . htmlspecialchars($this->cliente->getNombre()));

        foreach ($mascotas as $mascota) {
            $mascota->mostrar(); 
        }
        Menu::waitForEnter(); 
    }

    public function modificar($esVeterinario = false) {
        $mascotas = $this->getArreglo(); 
        if (empty($mascotas)) {
            echo "Este cliente no tiene mascotas para modificar, intente de nuevo." . PHP_EOL;
            return; 
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
                    echo "Operación cancelada.";
                    return;
                }
            } else {
                Menu::writeln('Está por modificar la siguiente mascota del sistema: ' . PHP_EOL);
                $mascotaEncontrada->mostrar();
                if ((Menu::readln(PHP_EOL . '¿Está seguro? S/N: ')) === 'S') {
                 
                    $nombre = trim(Menu::readln("Ingrese el nuevo nombre de la mascota (deje en blanco para no modificar): "));
                    while ($nombre != "" && !preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
                        echo "Error: El nombre debe contener solo letras y espacios." . PHP_EOL;
                        $nombre = trim(Menu::readln("Ingrese el nuevo nombre de la mascota: "));
                    }
                    if ($nombre != "") {
                        $mascotaEncontrada->setNombre($nombre);
                    }
            
                    $edad = trim(Menu::readln("Ingrese la nueva edad de la mascota (deje en blanco para no modificar): "));
                    while ($edad != "" && (!is_numeric($edad) || $edad <= 0)) {
                        echo "Error: La edad debe ser un número positivo." . PHP_EOL;
                        $edad = trim(Menu::readln("Ingrese la nueva edad de la mascota: "));
                    }
                    if ($edad != "") {
                        $mascotaEncontrada->setEdad((int)$edad);
                    }

                    $raza = trim(Menu::readln("Ingrese la nueva raza de la mascota (deje en blanco para no modificar): "));
                    while ($raza != "" && preg_match('/^[a-zA-Z\s]+$/', $raza)) {
                        echo "Error: La raza debe contener solo letras y espacios. ".PHP_EOL;
                        $raza = trim(Menu::readln("Ingrese la nueva raza de la mascota (deje en blanco para no modificar): "));
                    } 
                     if ($raza != ""){
                        $mascotaEncontrada->setRaza($raza);
                    }

                    if ($esVeterinario) {
                        $historialMedico = trim(Menu::readln("Ingrese el nuevo historial médico de la mascota (deje en blanco para no modificar): "));
                        while ($historialMedico != "" && preg_match('/^[a-zA-Z\s]+$/', $historialMedico)) {
                            echo "Error: El historial medico debe contener solo letras y espacios. ".PHP_EOL;
                            $mascotaEncontrada->setHistorialMedico($historialMedico);
                            $historialMedico = trim(Menu::readln("Ingrese el nuevo historial médico de la mascota (deje en blanco para no modificar): "));
                        }
                        if ($historialMedico != ""){
                            $mascotaEncontrada->setHistorialMedico($historialMedico);
                        }
                    }
                    $mascotaModelo = new MascotaModelo();
                    if ($mascotaModelo->modificar($mascotaEncontrada)) {
                        echo "Se ha modificado la mascota con éxito." . PHP_EOL;
                        return;
                    } else {
                        echo "Hubo un error al modificar la mascota." . PHP_EOL;
                    }
                }
                echo "Se ha cancelado la modificacion" . PHP_EOL;
                break;
            }
        }
    }
}

