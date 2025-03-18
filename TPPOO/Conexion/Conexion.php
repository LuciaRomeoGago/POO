<?php
class Conexion
{
    private static $db = null; // Almacena instancia de conexión a la DB, inicialmente no hay conexión

    private function __construct()
    {
        // Configuración de la conexión a la base de datos MySQL
        $servername = "sql10.freesqldatabase.com";
        $database = "sql10768330";
        $username = "sql10768330";
        $password = "1bFDBkBLRb";

        try {
            // Crear una instancia de PDO para conexión a MySQL
            $dsn = "mysql:host=$servername;dbname=$database"; // Cadena de conexión para MySQL
            self::$db = new PDO($dsn, $username, $password);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configuración para manejar errores
            //echo "Conexión exitosa con PDO a MySQL";
        } catch (PDOException $e) {
            // Manejo de errores de conexión
            echo 'Error de conexión: ' . $e->getMessage();
        }
    }

    // Retorna la conexión ya establecida a la DB, si no existe, la establece
    static function getConexion()
    {
        if (self::$db === null) {
            new self(); // Si no hay conexión, crea una nueva instancia
        }
        return self::$db;
    }

    // Ejecuta una consulta SQL y devuelve un arreglo de resultados
    static function query($sql)
    {
        try {
            $statement = self::getConexion()->query($sql); // Ejecuta la consulta
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
            self::getConexion()->exec($sql); // Ejecuta la consulta
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
        self::$db = null; // Al poner la conexión como null, se cierra la conexión
    }
}
