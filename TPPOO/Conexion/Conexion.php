<?php
    class Conexion{
    
        private static $db = null;
            
        //Obtiene los datos de ingresos a la DB de un archivo json local
        private static function getDatosDb(){
            $nombreArchivo = 'datos' . DIRECTORY_SEPARATOR . 'base.json';
            if (is_readable($nombreArchivo)){
                $datos = file_get_contents($nombreArchivo);
                $datos = json_decode($datos);
                return $datos;
            }
            return null;
        }
        
        private function __construct(){
            try {
                // Cadena de conexión
                $datosDb = self::getDatosDb();
                if(isset($datosDb)){
                	$dsn = "pgsql:host=$datosDb->host;port=$datosDb->port;dbname=$datosDb->database;user=$datosDb->user;password=$datosDb->password";
                  // Crear una instancia de PDO
         	      self::$db = new PDO($dsn);
        	        // Configurar el modo de error de PDO para manejar excepciones
            	    self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                  // Puedes usar esta conexión para realizar consultas
                 } else {
                 		echo 'Error de conexión: no se puede acceder al archivo con los datos de acceso a la Base de Datos' . PHP_EOL;
                 }
                 
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
         */
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
    }