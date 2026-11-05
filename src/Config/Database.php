<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    // Credenciales por defecto de XAMPP
    private $host = '127.0.0.1';
    private $db_name = 'app_taller_php';
    private $username = 'root'; // Usuario por defecto en XAMPP
    private $password = '';     // Sin contraseña por defecto en XAMPP
    private $charset = 'utf8mb4';
    
    // Propiedad estática que guardará la única instancia de la conexión (Singleton)
    private static $conn = null;

    // Constructor privado para evitar que se instancie con "new Database()" desde fuera
    private function __construct() {}

    // Método estático para obtener la conexión
    public static function getConnection() {
        // Si la conexión aún no existe, la creamos
        if (self::$conn == null) {
            try {
                $instance = new self();
                
                // DSN (Data Source Name)
                $dsn = "mysql:host=" . $instance->host . ";dbname=" . $instance->db_name . ";charset=" . $instance->charset;
                
                // Opciones de seguridad y manejo de errores para PDO
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en caso de error
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arrays asociativos por defecto
                    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa consultas preparadas nativas
                ];

                self::$conn = new PDO($dsn, $instance->username, $instance->password, $options);
                
            } catch(PDOException $exception) {
                // En un entorno de producción, aquí se guardaría en un archivo de log en vez de imprimirlo
                die("Error de conexión a la base de datos: " . $exception->getMessage());
            }
        }

        // Retornamos la instancia de la conexión
        return self::$conn;
    }
}