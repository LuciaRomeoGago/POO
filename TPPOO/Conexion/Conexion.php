<?php
class Conexion
{
    private static $db = null; // Almacena la unica instancia de conexión a la DB, inicialmente no hay conexión

    private function __construct()
    {
        // Configuración de la conexión a la base de datos MySQL
        $servername = "localhost";
        $database = "sistemadegestionvet";
        $username = "root";
        $password = "Hipatia2023!";

        try {
            // Crear una instancia de PDO para conexión a MySQL
            $dsn = "mysql:host=$servername;dbname=$database"; 
            self::$db = new PDO($dsn, $username, $password);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        } catch (PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
        }
    }

    // Retorna la conexión ya establecida a la DB, si no existe, la establece
    static function getConexion()
    {
        if (self::$db === null) {
            new self(); 
        }
        return self::$db;
    }

    // Ejecuta una consulta SQL y devuelve un arreglo de resultados
    static function query($sql)
    {
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
    static function ejecutar($sql)
    {
        try {
            self::getConexion()->exec($sql); 
            return true;
        } catch (PDOException $e) {
            echo "Error: datos ingresados inválidos " . htmlspecialchars($e->getMessage());
            return false;
        }
    }

    // Prepara una sentencia SQL para su ejecución
    static function prepare($sql)
    {
        return self::getConexion()->prepare($sql);
    }

    // Obtiene el último ID insertado
    static function getLastId()
    {
        return self::getConexion()->lastInsertId();
    }

    // Cierra la conexión con la base de datos
    static function closeConexion()
    {
        self::$db = null; 
    }
}
