<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'interface.php');

class ClienteManager extends arrayIdManager{

    //De la base de datos levanta los clientes y los agrega al arreglo para manipularlos
    public function levantar(){
        $sql = "select * 
                from Cliente";
        $clientes = Conexion::query($sql);
        
        // Verificar si se obtuvieron resultados
        if ($clientes === false || empty($clientes)) {
        echo "No se encontraron clientes en la base de datos." . PHP_EOL;
        return; // Salir del método si no hay clientes
        }

        foreach ($clientes as $cliente){
            //crea el objeto cliente
            $nuevoCliente = new Cliente(
                $cliente->nombre,
                $cliente->dni,
                $cliente->telefono, // Asegúrate de usar el campo correcto
                $cliente->direccion // Si tu clase Cliente tiene este parámetro
            );
    
            // Asignar el ID de la base de datos al objeto Cliente
            $nuevoCliente->setId($cliente->id);
    
            // Agregar el cliente al arreglo
            $this->agregar($nuevoCliente);
        }
    }
    
    //Crea el arreglo de Carreras a partir de los datos de la base de datos
    public function __construct() {
        $this->levantar();
    }
    

    //   Guarda el cliente en la base de datos y le setea el id generado por la base de datos al insertarlo
    public function alta() {
        $nombre = Menu::readln("Ingrese su nombre y apellido: ");
        $dni = Menu::readln("Ingrese su dni: ");
        $telefono =  Menu::readln("Ingrese su telefono: ");

        //Crea el nuevo objeto cliente
        $cliente = new Cliente($nombre,$dni,$telefono);

        //Lo inserta en la base de datos
        $cliente->guardar();

        //Lo agrega al arreglo
        $this->agregar($cliente);

        // Preguntar si desea agregar una mascota
        $agregarMascota = Menu::readln("¿Desea agregar una mascota? (si/no): ");
          if (strtolower($agregarMascota) === 'si') {
            $nombreMascota = Menu::readln("Ingrese el nombre de la mascota: ");
            $raza = Menu::readln("Ingrese la raza de la mascota: ");
            $edad = Menu::readln("Ingrese la edad de la mascota: ");
            $historialMedico = Menu::readln("Ingrese el historial médico de la mascota: ");

        // Crea el nuevo objeto Mascota
        $mascota = new Mascota($nombreMascota, $raza, $edad, $historialMedico);
        
        // Agrega la mascota al cliente
        $cliente->agregarMascota($mascota);
        
        // Mensaje de confirmación
        echo "La mascota se ha agregado exitosamente." . PHP_EOL;
    }
    
    echo "El cliente se ha creado con éxito." . PHP_EOL;
        $rta = Menu::readln("El cliente se ha creado con éxito");
    }

    //Dar de baja un cliente, se pide el id del cliente a eliminar. Se elimina de la base de datos y del arreglo
    public function baja(){
        $id = Menu::readln("Ingrese el id cliente a eliminar:");
        if ($this->existeId($id)){
            $cliente = $this->getPorId($id);
            Menu::writeln('Está por eliminar al siguiente cliente del sistema: '. PHP_EOL);
            $cliente->mostrar();
            $rta = Menu::readln(PHP_EOL . '¿Está seguro? S/N: ');            
            if (strtolower($rta) === 's') {  
                // Lo elimina de la base de datos
                $cliente->borrar();
                // Lo elimina del arreglo
                $this->eliminarPorId($id);
                echo "El cliente fue eliminado con éxito." . PHP_EOL;
            }
        } else {
            echo "No existe el ID a eliminar." . PHP_EOL;
        }
    }
    
    // Actualizar los datos de un cliente por su ID
    public function modificarCliente() {
	    $id = Menu::readln("Ingrese Id de cliente a modificar: ");
        if($this->existeId($id)){
            $clienteModificado = $this->getPorId($id);         	   
            Menu::writeln('Está por modificar al siguiente cliente del sistema: '. PHP_EOL);
            $clienteModificado->mostrar();
            $rta = Menu::readln(PHP_EOL . '¿Está seguro? S/N: ');            
            if($rta == 'S' or $rta == 's') {  
                Menu::writeln("A continuación ingrese los nuevos datos, ENTER para dejarlos sin modificar");
                $nombre = Menu::readln("Ingrese el nombre y apellido: ");
                if ($nombre != ""){
                    $clienteModificado->setNombre($nombre);
                }
                $dni = Menu::readln("Ingrese el dni: ");
                if ($dni != ""){
                    $clienteModificado->setDni($dni);
                }
                   // Modificar dirección
                $direccion = Menu::readln("Ingrese la dirección: ");
                if ($direccion != "") {
                   $clienteModificado->setDireccion($direccion);
                }

            //Lo modifica en la Base de Datos
            $clienteModificado->modificar();
            $rta = Menu::readln("El cliente fue modificado con éxito");
           } else {
                Menu::writeln("El id ingresado no se encuentra entre nuestros clientes");
        }
    }
}
       
    // Mostrar por pantalla todos los clientes
	public function mostrar(){
		$clientes = $this->getArreglo();
		Menu::cls();		
		Menu::subtitulo('Lista de los clientes existentes en nuestro sistema');
		$lineas = 0;
		  
      foreach ($clientes as $cliente) {
	    	$cliente->mostrar();
   	   $lineas+=1;
         if ((($lineas) % (Menu::lineasPorPagina())) === 0) {
		   	Menu::waitForEnter();
      		Menu::cls(); // Limpiar la pantalla antes de imprimir las siguientes líneas
    		}
        } 
        Menu::waitForEnter();   
    }
}
