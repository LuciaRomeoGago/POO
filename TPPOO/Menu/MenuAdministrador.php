<?php
require_once('Menu.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ClienteManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'VeterinarioManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ProductoManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'InventarioManager.php'); 

class MenuAdmin extends Menu {
    private $clienteManager;
    private $veterinarioManager;
    private $productoManager;
    private $clienteId;
    private $veterinarioId;

    public function __construct()
    {
        $this->clienteManager = new ClienteManager();
        $this->veterinarioManager = new VeterinarioManager();
        $this->productoManager = new ProductoManager();
        $this->clienteId = null;
        $this->veterinarioId = null;
    }
    
    // Menú principal general
    public function menuPrincipal()
    {
        $this->clienteId = null;
        $this->veterinarioId = null;

        $titulo = "Bienvenido a la Veterinaria 'Patitas'";
        $opciones = [];

        $opciones[0][0] = 0;
        $opciones[0][1] = "Salir del sistema";
        $opciones[0][2] = array($this, "salirSistema");

        $opciones[1][0] = 1;
        $opciones[1][1] = "Ingresar como Cliente";
        $opciones[1][2] = array($this, "menuCliente");

        $opciones[2][0] = 2;
        $opciones[2][1] = "Ingresar como Veterinario";
        $opciones[2][2] = array($this, "menuVeterinario");

        self::menu($titulo, $opciones);
    }

    // Menú para operaciones del Cliente
    protected function menuCliente()
    {
        if ($this->clienteId === null) {
            echo "Ingrese su ID: ";
            $id = trim(fgets(STDIN));

            if (!$this->clienteManager->existeId($id)) {
                echo "Cliente no encontrado." . PHP_EOL;
                return;
            }
            $this->clienteId = $id;
            $clienteSeleccionado = $this->clienteManager->getPorId($id);
            $mascotaManager = new MascotaManager($clienteSeleccionado);

            $titulo = "Bienvenido, " . htmlspecialchars($clienteSeleccionado->getNombre());
            $opciones = [];

            $opciones[0][0] = 0;
            $opciones[0][1] = "Volver al menú principal";
            $opciones[0][2] = array($this, "menuPrincipal");

            $opciones[1][0] = 1;
            $opciones[1][1] = "Administre sus mascotas";
            $opciones[1][2] = function () use ($mascotaManager) {
                $this->menuGestionMascotas($mascotaManager);
            };

            $opciones[2][0] = 2;
            $opciones[2][1] = "Mostrar todas sus mascotas";
            $opciones[2][2] =   array($mascotaManager, "mostrar");

            $opciones[3][0] = 3;
            $opciones[3][1] = "Comprar productos";
            $opciones[3][2] = array($this, "menuCompraProductos");

            $opciones[4][0] = 4;
            $opciones[4][1] = "Mostrar inventario";
            $opciones[4][2] = function () use ($clienteSeleccionado) {
                InventarioManager::mostrarInventario($clienteSeleccionado);
            };

            self::menu($titulo, $opciones);
        }
    }

    // Submenú para gestionar mascotas
    protected function menuGestionMascotas(MascotaManager $mascotaManager)   {
        $titulo = "Gestión de Mascotas";
        $opciones = [];

        $opciones[0][0] = 0;
        $opciones[0][1] = "Volver al menú del cliente";
        $opciones[0][2] = array($this, "menuCliente");

        $opciones[1][0] = 1;
        $opciones[1][1] = "Agregar mascota";
        $opciones[1][2] = array($mascotaManager, "alta");

        $opciones[2][0] = 2;
        $opciones[2][1] = "Eliminar mascota";
        $opciones[2][2] = array($mascotaManager, "baja");

        $opciones[3][0] = 3;
        $opciones[3][1] = "Modificar mascota";
        $opciones[3][2] = function () use ($mascotaManager) { 
            $mascotaManager->modificar();
        };

        self::menu($titulo, $opciones);
    }

    // Submenu para compra de Productos
    protected function menuCompraProductos() {
        $titulo = "Gestión de Productos";
        $opciones = [];

        $opciones[0][0] = 0;
        $opciones[0][1] = "Volver al menú del cliente";
        $opciones[0][2] = array($this, "menuCliente");

        $opciones[1][0] = 1;
        $opciones[1][1] = "Mostrar todos los productos disponibles";
        $opciones[1][2] = array($this->productoManager, "mostrar");

        $opciones[2][0] = 2;
        $opciones[2][1] = "Comprar producto";
        $opciones[2][2] =  function () {
                $clienteSeleccionado = $this->clienteManager->getPorId($this->clienteId);
                InventarioManager::comprarProducto($clienteSeleccionado, $this->productoManager);
            };

        self::menu($titulo, $opciones);
    }

    // Menú de operaciones del veterinario
    protected function menuVeterinario() {
        if ($this->veterinarioId === null) {
            echo "Ingrese su ID: ";
            $id = trim(fgets(STDIN));

            if (!$this->veterinarioManager->existeId($id)) {
                echo "Veterinario no encontrado." . PHP_EOL;
                return;
            }

            $this->veterinarioId = $id;
            $veterinarioSeleccionado = $this->veterinarioManager->getPorId($id);

            $titulo = "Menu administrativo de veterinario para: " . htmlspecialchars($veterinarioSeleccionado->getNombre());
            $opciones = [];

            $opciones[0][0] = 0;
            $opciones[0][1] = "Volver al menu principal";
            $opciones[0][2] = array($this, "menuPrincipal");

            $opciones[1][0] = 1;
            $opciones[1][1] = "Administrar Clientes";
            $opciones[1][2] = array($this, 'menuAdministrarClientes');

            $opciones[2][0] = 2;
            $opciones[2][1] = "Administrar Mascotas";
            $opciones[2][2] = array($this, 'menuAdministrarMascotas'); 

            $opciones[3][0] = 3;
            $opciones[3][1] = "Administrar Productos";
            $opciones[3][2] = array($this, 'menuAdministrarProductos');

            self::menu($titulo, $opciones);
        }
    }

    // Menu administrativo Clientes
    protected function menuAdministrarClientes(){
        $veterinarioSeleccionado = $this->veterinarioManager->getPorId($this->veterinarioId);
        $clienteManager = new ClienteManager($veterinarioSeleccionado);


        $titulo = "Menu administrativo de Clientes";
        $opciones = [];

        $opciones[0][0] = 0;
        $opciones[0][1] = "Volver al menu anterior";
        $opciones[0][2] = array($this, "menuVeterinario");

        $opciones[1][0] = 1;
        $opciones[1][1] = "Agregar cliente";
        $opciones[1][2] = array($clienteManager, "alta");

        $opciones[2][0] = 2;
        $opciones[2][1] = "Eliminar cliente";
        $opciones[2][2] = array($clienteManager, "baja");

        $opciones[3][0] = 3;
        $opciones[3][1] = "Modificar cliente";
        $opciones[3][2] = array($clienteManager, "modificar");

        $opciones[4][0] = 4;
        $opciones[4][1] = "Mostrar todos los clientes";
        $opciones[4][2] = array($clienteManager, "mostrar");

        self::menu($titulo, $opciones);
    }


    // Menú administrativo mascotas
    protected function menuAdministrarMascotas(){
        echo "Lista de clientes:" . PHP_EOL;
        $this->clienteManager->mostrar();

        echo "Ingrese el ID del cliente para gestionar sus mascotas: ";
        $clienteId = trim(fgets(STDIN));
        if (!$this->clienteManager->existeId($clienteId)) {
            echo "Cliente no encontrado." . PHP_EOL;
            return;
        }

        $clienteSeleccionado = $this->clienteManager->getPorId($clienteId);
        $mascotaManager = new MascotaManager($clienteSeleccionado);
        
        $titulo = "Menu veterinario administrativo de Mascotas de: " . htmlspecialchars($clienteSeleccionado->getNombre());
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
        $opciones[3][2] = function () use ($mascotaManager) { 
            $mascotaManager->modificar(true);
        };

        $opciones[4][0] = 4;
        $opciones[4][1] = "Mostrar todas las mascotas";
        $opciones[4][2] = array($mascotaManager, "mostrar");

        self::menu($titulo, $opciones);
    }

    // Menu administrativo de Productos
    protected function menuAdministrarProductos() {
        $titulo = "Menu administrativo de Productos";
        $opciones = [];

        $opciones[0][0] = 0;
        $opciones[0][1] = "Volver al menu anterior";
        $opciones[0][2] = array($this, "menuVeterinario");

        $opciones[1][0] = 1;
        $opciones[1][1] = "Agregar producto";
        $opciones[1][2] = array($this->productoManager, "alta");

        $opciones[2][0] = 2;
        $opciones[2][1] = "Eliminar producto";
        $opciones[2][2] = array($this->productoManager, "baja");

        $opciones[3][0] = 3;
        $opciones[3][1] = "Actualizar producto";
        $opciones[3][2] = array($this->productoManager, "modificar");

        $opciones[4][0] = 4;
        $opciones[4][1] = "Mostrar todos los productos";
        $opciones[4][2] = array($this->productoManager, "mostrar");

        self::menu($titulo, $opciones);
    }

    protected function salirSistema() {
        parent::exit();
    }
}
