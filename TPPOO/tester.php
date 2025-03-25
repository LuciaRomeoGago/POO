<?php
require_once('Clases' . DIRECTORY_SEPARATOR . 'ClienteManager.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'Inventario.php');
require_once('Conexion' . DIRECTORY_SEPARATOR . 'Conexion.php');
require_once('Clases' . DIRECTORY_SEPARATOR . 'VeterinarioManager.php');
require_once ('Clases' . DIRECTORY_SEPARATOR . 'Mascota.php');
require_once ('Clases' . DIRECTORY_SEPARATOR . 'ClienteModelo.php');
require_once ('Clases' . DIRECTORY_SEPARATOR . 'Cliente.php');

// Simulación de la clase Menu para pruebas (si no la tienes ya)
class tester {
    public static $testInputs = [];

    public static function readln($prompt = "") {
        echo $prompt;
        if (!empty(self::$testInputs)) {
            return array_shift(self::$testInputs);
        } else {
            return readline(); // Para pruebas interactivas
        }
    }
}

// Datos de prueba (simulando la entrada del usuario)
tester::$testInputs = [
    "Juan Perez", // Nombre
    "12345678", // DNI
    "7" // id
    // "si",       // ¿Desea agregar una mascota?
    // "Firulais", // Nombre de la mascota
    // "2",        // Edad de la mascota
    // "Callejero",// Raza de la mascota
    // "Ninguno"   // Historial médico de la mascota
];

// Crear una instancia de ClienteManager
$clienteManager = new ClienteManager();

// Llamar al método alta()
$clienteManager->alta();

echo "Ejecución completada. Verifique la base de datos o la salida para confirmar el éxito." . PHP_EOL;