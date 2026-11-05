<?php

namespace App\Config;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    // Credenciales por defecto de XAMPP
    private $host = '127.0.0.1';
    private $port = 3306;        // Puerto estandar de MySQL/MariaDB
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
                $dsn = "mysql:host=" . $instance->host
                     . ";port=" . $instance->port
                     . ";dbname=" . $instance->db_name
                     . ";charset=" . $instance->charset;
                
                // Opciones de seguridad y manejo de errores para PDO
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en caso de error
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arrays asociativos por defecto
                    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa consultas preparadas nativas
                ];

                self::$conn = new PDO($dsn, $instance->username, $instance->password, $options);
                
            } catch(PDOException $exception) {
                // Se lanza una excepcion con un mensaje seguro para mostrar al
                // usuario; la excepcion original queda como causa.previous para
                // poder revisarla en el registro de errores.
                throw new RuntimeException(
                    'No fue posible conectar con la base de datos. Verifique que MySQL este en ejecucion '
                    . 'y que las credenciales del archivo src/Config/Database.php sean correctas.',
                    0,
                    $exception
                );
            }
        }

        // Retornamos la instancia de la conexión
        return self::$conn;
    }
}