<?php
require_once('Menu.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ClienteManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'MascotaManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'VeterinarioManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'ProductoManager.php');

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

    // Menú principal para seleccionar el rol
    public function menuPrincipal()
    {
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

    // Menú para operaciones de cliente
    protected function menuCliente()
    {
        if ($this->clienteId === null) {

            echo "Ingrese su ID: ";
            $id = trim(fgets(STDIN));

            if (!$this->clienteManager->existeId($id)) {
                echo "Cliente no encontrado." . PHP_EOL;
                return;
            }

            // guardo id del cliente en la propiedad
            $this->clienteId = $id;
            // uso id del cliente almacenado
            $id = $this->clienteId;

            // Obtener el cliente correspondiente
            $clienteSeleccionado = $this->clienteManager->getPorId($id);
            $mascotaManager = new MascotaManager($clienteSeleccionado);

            // Menú principal del cliente
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
                ClienteManager::mostrarInventario($clienteSeleccionado);
            };

            self::menu($titulo, $opciones);
        }
    }

    // Submenú para gestionar mascotas
    protected function menuGestionMascotas(MascotaManager $mascotaManager)
    {
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
        $opciones[3][2] = array($mascotaManager, "modificar2");

        self::menu($titulo, $opciones);
    }

    protected function menuCompraProductos()
    {
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
        $opciones[2][2] = //array($this->productoManager, "comprarProducto");
            function () {
                // Obtener el cliente seleccionado
                $clienteSeleccionado = $this->clienteManager->getPorId($this->clienteId);
                // Llamar a la función comprarProducto de ClienteManager
                $this->clienteManager->comprarProducto($clienteSeleccionado, $this->productoManager);
            };

        self::menu($titulo, $opciones);
    }


    /* // Menú de operaciones con mascotas
        $titulo = "Administre sus mascotas: " . htmlspecialchars($clienteSeleccionado->getNombre());
        
        // Opciones del menú, TODO ASOCIADO BIEN, creo
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
        $opciones[3][2] =  array ($mascotaManager, "modificar2");

		$opciones[4][0] = 4;
		$opciones[4][1] = "Mostrar todas las mascotas";
		$opciones[4][2] = array($mascotaManager,"mostrar");

		self::menu($titulo, $opciones);
    }
*/
    // Menú para operaciones de veterinario
    protected function menuVeterinario()
    {
        if ($this->veterinarioId === null) {

            echo "Ingrese su ID: ";
            $id = trim(fgets(STDIN));

            if (!$this->veterinarioManager->existeId($id)) {
                echo "Veterinario no encontrado." . PHP_EOL;
                return;
            }

            // guardo id del veterinario en la propiedad
            $this->veterinarioId = $id;
            // uso id del veterinario almacenado
            $id = $this->veterinarioId;


            // Obtener el veterinario correspondiente
            $veterinarioSeleccionado =     $this->veterinarioManager->getPorId($id);
            $clienteManager = new ClienteManager($veterinarioSeleccionado);

            $titulo = "Menu administrativo de veterinario para: " . htmlspecialchars($veterinarioSeleccionado->getNombre());

            // Opciones del menú
            $opciones = [];

            $opciones[0][0] = 0;
            $opciones[0][1] = "Volver al menu principal";
            $opciones[0][2] = array($this, "menuPrincipal");

            $opciones[1][0] = 1;
            $opciones[1][1] = "Administrar Clientes";
            $opciones[1][2] = array($this, 'menuAdministrarClientes'); // Mostrar todos los clientes

            $opciones[2][0] = 2;
            $opciones[2][1] = "Administrar Mascotas";
            $opciones[2][2] = array($this, 'menuAdministrarMascotas'); // Método para gestionar mascotas

            $opciones[3][0] = 3;
            $opciones[3][1] = "Administrar Productos";
            $opciones[3][2] = array($this, 'menuAdministrarProductos');

            self::menu($titulo, $opciones);
        }
    }



    protected function menuAdministrarClientes()
    {
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
        $opciones[3][2] = array($this->clienteManager, "modificarCliente");

        $opciones[4][0] = 4;
        $opciones[4][1] = "Mostrar todos los clientes";
        $opciones[4][2] = array($this->clienteManager, "mostrar");

        self::menu($titulo, $opciones);
    }


    // Menú para administrar mascotas (por veterinarios)
    protected function menuAdministrarMascotas()
    {
        // Mostrar la lista de clientes
        echo "Lista de clientes:" . PHP_EOL;
        $this->clienteManager->mostrar();

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
        $titulo = "Menu veterinario administrativo de Mascotas de: " . htmlspecialchars($clienteSeleccionado->getNombre());

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
        $opciones[3][2] = array($mascotaManager, "modificar2");

        $opciones[4][0] = 4;
        $opciones[4][1] = "Mostrar todas las mascotas";
        $opciones[4][2] = array($mascotaManager, "mostrar");

        self::menu($titulo, $opciones);
    }


    protected function menuAdministrarProductos()
    {
        // Menú para administrar clientes
        $titulo = "Menu administrativo de Productos";

        // Opciones del menú
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
        $opciones[3][2] = array($this->productoManager, "modificarProducto");

        $opciones[4][0] = 4;
        $opciones[4][1] = "Mostrar todos los productos";
        $opciones[4][2] = array($this->productoManager, "mostrar");

        self::menu($titulo, $opciones);
    }

    // Método público para mostrar el menú
    public function mostrarMenu($titulo, $opciones)
    {
        $this->menu($titulo, $opciones); // Llama al método protegido
    }

    protected function salirSistema()
    {
        parent::exit();
    }
}
