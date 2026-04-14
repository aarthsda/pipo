<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'pipo';
    private $username = 'root';
    private $password = '';
    private static $conn;

    public static function getConexao() {
        if (!isset(self::$conn)) {
            $db = new self();
            try {
                $dsn = 'mysql:host=' . $db->host . ';dbname=' . $db->db_name . ';charset=utf8mb4';
                self::$conn = new PDO($dsn, $db->username, $db->password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('Erro de conexão: ' . $e->getMessage());
            }
        }
        return self::$conn;
    }

    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>
