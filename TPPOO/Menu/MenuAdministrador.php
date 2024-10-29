<?php
require_once('Menu.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ClienteManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'VeterinarioManager.php');

class MenuAdmin extends Menu {
    private $clienteManager;
    private $mascotaManager;
    private $veterinarioManager;

    public function __construct() {
        $this->clienteManager = new ClienteManager();
        $this->veterinarioManager = new VeterinarioManager();
    }

    // Menú principal para seleccionar el rol
    public function menuPrincipal() {
        $titulo = "Bienvenido a la Veterinaria 'Patitas'";
        $opciones = [];

        $opciones[0][0] = 0;
        $opciones[0][1] = "Salir del sistema";
        $opciones[0][2] = array($this, "exit");

        $opciones[1][0] = 1;
        $opciones[1][1] = "Entrar como Cliente";
        $opciones[1][2] = array($this, "menuCliente");

        $opciones[2][0] = 2;
        $opciones[2][1] = "Entrar como Veterinario";
        $opciones[2][2] = array($this, "menuVeterinario");

        self::menu($titulo, $opciones);
    }

    // Menú para operaciones de cliente
    protected function menuCliente() {
        echo "Ingrese su ID: ";
        $id = trim(fgets(STDIN));

        if (!$this->clienteManager->existeId($id)) {
            echo "Cliente no encontrado." . PHP_EOL;
            return;
        }

        // Obtener el cliente correspondiente
        $clienteSeleccionado = $this->clienteManager->getPorId($id);
        $mascotaManager = new MascotaManager($clienteSeleccionado);

        // Menú de operaciones con mascotas
        $titulo = "Administre sus mascotas: " . htmlspecialchars($clienteSeleccionado->getNombre());
        
        // Opciones del menú
        $opciones = [];
        
        $opciones[0][0] = 0;
        $opciones[0][1] = "Volver al menu principal";
        $opciones[0][2] = array($this, "menuPrincipal");

        $opciones[1][0] = 1;
        $opciones[1][1] = "Agregar mascota";
        $opciones[1][2] = array($mascotaManager, "alta");

        $opciones[2][0] = 2;
        $opciones[2][1] = "Eliminar mascota";
        $opciones[2][2] = array($mascotaManager, "baja");

        $opciones[3][0] = 3;
        $opciones[3][1] = "Modificar mascota";
        $opciones[3][2] = array($mascotaManager, "modificar");

		$opciones[4][0] = 4;
		$opciones[4][1] = "Mostrar todas las mascotas";
		$opciones[4][2] = array($mascotaManager,"mostrar");

		self::menu($titulo, $opciones);
    }

    // Menú para operaciones de veterinario
    protected function menuVeterinario() {
        echo "Ingrese su ID de Veterinario: ";
        $idVeterinario = trim(fgets(STDIN));

        if (!$this->veterinarioManager->existeId($idVeterinario)) {
            echo "Veterinario no encontrado." . PHP_EOL;
            return;
        }

		// Obtener el veterinario correspondiente
		$veterinarioSeleccionado = 	$this -> veterinarioManager -> getPorId($idVeterinario); 

		$titulo = "Menu administrativo de veterinario para: " . htmlspecialchars($veterinarioSeleccionado->getNombre());
        
		// Opciones del menú
		$opciones = [];
		
		$opciones[0][0] = 0;
		$opciones[0][1] = "Volver al menu principal";
		$opciones[0][2] = array($this, "menuPrincipal");

		$opciones[1][0] = 1;
		$opciones[1][1] = "Administrar Clientes";
		$opciones[1][2] = array($this->clienteManager, 'mostrar'); // Mostrar todos los clientes

		$opciones[2][0] = 2;
		$opciones[2][1] = "Administrar Mascotas";
		$opciones[2][2] = array($this, 'ABMmascotas'); // Método para gestionar mascotas

		self::menu($titulo, $opciones);
    }

    // Menú para administrar mascotas (por veterinarios)
    protected function AdministrarMascotas() {
        // Solicitar ID del cliente
         echo "Ingrese el ID del cliente para gestionar sus mascotas: ";
         $clienteId = trim(fgets(STDIN));

        if (!$this->clienteManager->existeId($clienteId)) {
           echo "Cliente no encontrado." . PHP_EOL;
           return;
        }

         // Obtener el cliente correspondiente
          $clienteSeleccionado = $this->clienteManager->getPorId($clienteId);
          $mascotaManager = new MascotaManager($clienteSeleccionado);
          // Menú de operaciones con mascotas
         $titulo = "Menu administrativo de Mascotas para: " . htmlspecialchars($clienteSeleccionado->getNombre());
    
         // Opciones del menú
         $opciones = [];
    
         $opciones[0][0] = 0;
         $opciones[0][1] = "Volver al menu anterior";
         $opciones[0][2] = array($this, "menuVeterinario");

         $opciones[1][0] = 1;
         $opciones[1][1] = "Agregar mascota";
         $opciones[1][2] = array($mascotaManager, "alta");

         $opciones[2][0] = 2;
         $opciones[2][1] = "Eliminar mascota";
         $opciones[2][2] = array($mascotaManager, "baja");

         $opciones[3][0] = 3;
         $opciones[3][1] = "Modificar mascota";
         $opciones[3][2] = array($mascotaManager, "modificar");

	     $opciones[4][0] = 4;
	     $opciones[4][1] = "Mostrar todas las mascotas";
	     $opciones[4][2] = array($mascotaManager,"mostrar");

	    self::menu($titulo, $opciones);
        }

        protected function AdministrarClientes() {
            // Menú para administrar clientes
            $titulo = "Menu administrativo de Clientes";
        
            // Opciones del menú
            $opciones = [];
        
            $opciones[0][0] = 0;
            $opciones[0][1] = "Volver al menu anterior";
            $opciones[0][2] = array($this, "menuVeterinario");
        
            $opciones[1][0] = 1;
            $opciones[1][1] = "Agregar cliente";
            $opciones[1][2] = array($this->clienteManager, "alta");
        
            $opciones[2][0] = 2;
            $opciones[2][1] = "Eliminar cliente";
            $opciones[2][2] = array($this->clienteManager, "baja");
        
            $opciones[3][0] = 3;
            $opciones[3][1] = "Modificar cliente";
            $opciones[3][2] = array($this->clienteManager, "modificar");
        
            $opciones[4][0] = 4;
            $opciones[4][1] = "Mostrar todos los clientes";
            $opciones[4][2] = array($this->clienteManager, "mostrar");
        
            self::menu($titulo, $opciones);
        }
    }

      
