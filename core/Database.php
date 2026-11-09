<?php
/**
 * Clase Database - Conexión a MySQL
 * Patrón Singleton para gestionar una única conexión
 */

class Database {
    private static $instance = null;
    private $connection;

    // Credenciales de BD
    private $host = 'localhost';
    private $db_name = 'ServiTrack';
    private $user = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    private function __construct() {
        $this->connect();
    }

    /**
     * Patrón Singleton - obtener instancia única
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Conectar a la base de datos
     */
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};port=3307;dbname={$this->db_name};charset={$this->charset}";
            
            $this->connection = new PDO(
                $dsn,
                $this->user,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * Obtener conexión
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Evitar clonación
     */
    private function __clone() {}
}
