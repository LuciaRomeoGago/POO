<?php
/*    class Conexion{
    
        private static $db = null;
            
        //Obtiene los datos de ingresos a la DB de un archivo json local
       /* private static function getDatosDb(){
            $nombreArchivo = 'Conexion' . DIRECTORY_SEPARATOR . 'base.json';
            if (is_readable($nombreArchivo)){
                $datos = file_get_contents($nombreArchivo);
                $datos = json_decode($datos);
                return $datos;
            }
            return null;
        }
        
        private function __construct(){
                $servidor= 'batyr.db.elephantsql.com';
                $usuario= 'fklvtlhv';
                $contrasena= 'fcVvnsbFt7cHt2ShFf5rUg2yJsZwEKOM';
               // $basededatos= 'fklvtlhv';
            

            try {
                // Cadena de conexión
                //$datosDb = self::getDatosDb();
                /*if(isset($datosDb)){
                	$dsn = "pgsql=servidor=$datosDb->servidor;base_de_datos=$datosDb->base_de_datos;usuario=$datosDb->usuario;contrasena=$datosDb->contrasena";
                  // Crear una instancia de PDO
         	      self::$db = new PDO($dsn);
        	        // Configurar el modo de error de PDO para manejar excepciones
            	    self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                  // Puedes usar esta conexión para realizar consultas
                } else {
                 		echo 'Error de conexión: no se puede acceder al archivo con los datos de acceso a la Base de Datos' . PHP_EOL;
                }
                 
             $conn = new PDO($servidor, $usuario, $contrasena);
                 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                 echo "Conexión OK con PDO";
                } catch (PDOException $e) {
                // Manejo de errores
                echo 'Error de conexión: ' . $e->getMessage();
            }
        }

        // Retorna la conexión ya establecida a la DB, si no existe la establece

        static function getConexion(){
            if (isset (self::$db))
                return self::$db;
            else
                return new self();
        }
        
        /**
        * Recibe un sql de consulta y devuelve un arreglo de objetos
         
        static function query($sql) {
				try {
        			$pDO = self::getConexion();
        			$statement = $pDO->query($sql);
        			if ($statement === false) {
            	// Si la consulta falla, lanzamos una excepción personalizada
            		throw new PDOException("Error al ejecutar la consulta: " . $pDO->errorInfo()[2]);
        			}
        			$resultado = $statement->fetchAll();
        			return $resultado;
    			} catch (PDOException $e) {
                // Captura la excepción y muestra un mensaje de error
        			echo "Error: " . $e->getMessage();
 
   			}
        }

   
        // Recibe un SQL para ejecucion
        static function ejecutar($sql) {
            $pDO = self::getConexion();
				try {            
            	$pDO->exec($sql); // Cambié query por exec para operaciones no SELECT
            	return true;
            } catch (PDOException $e) { // Captura la excepción y muestra un mensaje de error
        			echo "Error: datos ingresados inválidos". htmlspecialchars($e->getMessage()); //. $e->getMessage();
					return false;
 
   			}
        }

        //Prepara la sentencia sql

        static function prepare($sql) {
            $pDO = self::getConexion();
            return $pDO->prepare($sql);
        }

 
        static function getLastId() {
            $pDO = self::getConexion();
            $lastId = $pDO->lastInsertId();
            return $lastId;
        }
 
        
        static function closeConexion() {
            self::$db = null;
        }
    }*/

class Conexion {
    private static $db = null; // almacena instancia de conexion a db, se incializa como que no hay conexion al principio

    private function __construct() {
        // Configuración de la conexión a la base de datos PostgreSQL
       /* $host = 'sql10.freesqldatabase.com';
        $puerto = '3306';
        $dbname = 'sql10763804';
        $usuario = 'sql10763804';
        $contrasena = 'YW49tuyKvg';
*/
$servername = "sql10.freesqldatabase.com";
$database = "sql10763804";
$username = "sql10763804";
$password = "YW49tuyKvg";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
mysqli_close($conn);
        try {
            // Crear una instancia de PDO
            $dsn = "pgsql:host=$servername;dbname=$database"; //crea cadena de conexion para PostgreSQL
            self::$db = new PDO($dsn, $username, $password);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Conexión OK con PDO";
        } catch (PDOException $e) {
            // Manejo de errores de conexión
            echo 'Error de conexión: ' . $e->getMessage();
        }
    }

    // Retorna la conexión ya establecida a la DB, si no existe la establece
    static function getConexion() {
        if (self::$db === null) {
            new self(); // Si no hay conexión, crea una nueva instancia
        }
        return self::$db;
    }

    // Ejecuta una consulta SQL y devuelve un arreglo de resultados
    static function query($sql) {
        try {
            $statement = self::getConexion()->query($sql);
            if ($statement === false) {
                throw new PDOException("Error al ejecutar la consulta: " . self::getConexion()->errorInfo()[2]);
            }
            $resultado = $statement->fetchAll();
            return $resultado;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Ejecuta una consulta SQL sin necesidad de obtener resultados (INSERT, UPDATE, DELETE)
    static function ejecutar($sql) {
        try {
            self::getConexion()->exec($sql);
            return true;
        } catch (PDOException $e) {
            echo "Error: datos ingresados inválidos " . htmlspecialchars($e->getMessage());
            return false;
        }
    }

    // Prepara una sentencia SQL para su ejecución
    static function prepare($sql) {
        return self::getConexion()->prepare($sql);
    }

    // Obtiene el último ID insertado
    static function getLastId() {
        return self::getConexion()->lastInsertId();
    }

    // Cierra la conexión con la base de datos
    static function closeConexion() {
        self::$db = null;
    }
}
