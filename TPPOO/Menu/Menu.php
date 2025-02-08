<?php
require_once('MenuAdministrador.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Mascota.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'arrayIdManager.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'interface.php');

class Menu {
    
    private static $lineasPorPagina = 10;   
    
    public function __construct() {
    }

    // Función que muestra una línea en pantalla con el salto de línea
    public static function writeln($texto) {   
        echo ($texto);
        echo(PHP_EOL);
    }

    // Función que muestra una línea en pantalla con el salto de línea
    public static function readln($texto) {
        echo ($texto);
        $rta = readline();
        echo(PHP_EOL);
        return $rta;
   }   
   
   // Retorna la cantidad de líneas que se ven por página en pantalla
   public static function lineasPorPagina(){
       return self::$lineasPorPagina;
   }
   
   // Función para esperar a que el usuario presione Enter   
   public static function waitForEnter() {
       echo "Presiona Enter para continuar...";
       fgets(STDIN);
   }
   
   // Limpia la pantalla dependiendo del sistema operativo que estemos usando 
   public static function cls(){
      if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
          popen('cls', 'w');
      } else {
          system("clear");
      }
   }

   public function pantallaBienvenida($nombreSistema){
       self::writeln("**************************************");
       self::writeln("**                                 **");     
       self::writeln("**   Bienvenidos a ".$nombreSistema."      **");
       self::writeln("**                                 **");     
       self::writeln("**************************************"); 
       self::writeln("");                            
   }

   public function pantallaDespedida(){
       self::writeln("Gracias por utilizar nuestro sistema");
       self::writeln("");
   }

   public static function subtitulo($subtitulo){
       echo PHP_EOL;
       self::writeln($subtitulo);
       self::writeln(str_repeat('-', mb_strlen($subtitulo)));
   }

   protected function exit(){
       return 1;   
   }

   // Opciones es una matriz, en cada fila el array opción tiene el número de la opción, nombre de la opción y la función
   protected function menu($titulo, $opciones) {
       $opcion = 1;

       while($opcion != 0){
           echo (PHP_EOL);
           echo ('---------------------------'.PHP_EOL);
           echo ($titulo.PHP_EOL);
           echo ('---------------------------'.PHP_EOL);
    
           foreach ($opciones as $opcion) {
               echo ($opcion[0] .' - '. $opcion[1]. PHP_EOL );
           } 
    
           echo(PHP_EOL);            
           $opcion = readline('Elija una opción: ');
        
           if (isset($opciones[$opcion])) {
               $funcion = $opciones[$opcion][2];
               // La función tiene argumentos                
               if (isset($opciones[$opcion][3])){
                   call_user_func($funcion,$opciones[$opcion][3]);                
               } else {
                   call_user_func($funcion);
               } 
            } else {
                self::writeln("Opción inválida");
            }
        }
    }

    // Método para iniciar el sistema
    public function iniciarSistema() {
        $this->pantallaBienvenida("Sistema de Gestión Veterinaria 'Patitas'");
        $menuAdmin = new MenuAdmin();
        $menuAdmin->menuPrincipal(); // Inicia el menú principal del administrador
    }

    // Main menu display
    public static function displayMainMenu() {
        self::writeln("\n--- Menú Principal ---");
        self::writeln("1. Ingresar como Veterinario");
        self::writeln("2. Ingresar como Cliente");
        self::writeln("3. Salir");
    }

    // Veterinarian menu display
    public static function displayVeterinarianMenu() {
        self::writeln("\n--- Menú Veterinario ---");
        self::writeln("1. Agregar Mascota");
        self::writeln("2. Modificar Mascota");
        self::writeln("3. Eliminar Mascota");
        self::writeln("4. Mostrar Mascotas");
        self::writeln("5. Volver al Menú Principal");
    }

    // Client menu display
    public static function displayClientMenu() {
        self::writeln("\n--- Menú Cliente ---");
        self::writeln("1. Crear Cliente");
        self::writeln("2. Modificar Cliente");
        self::writeln("3. Eliminar Cliente");
        self::writeln("4. Mostrar Clientes");
        self::writeln("5. Volver al Menú Principal");
    }
}