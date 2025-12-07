<?php 
class Database {

    // Si existen variables de entorno (hosting), se usan.
    // Si NO existen (local), se usan tus credenciales locales.

    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $this->host     = getenv("DB_HOST") ?: "bpx01lkc2gjrjagwjuvo-mysql.services.clever-cloud.com";
        $this->port     = getenv("DB_PORT") ?: "3306";
        $this->db_name  = getenv("DB_NAME") ?: "bpx01lkc2gjrjagwjuvo";
        $this->username = getenv("DB_USER") ?: "u56ypq9031ncbwwm";
        $this->password = getenv("DB_PASS") ?: "lgSYnMWcfX3HJBMSbfEv";
    }

    public function getConnection() {
        $this->conn = null;

        try {

            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["ok" => false, "message" => "Error de conexión a la base de datos."]);
            exit;
        }

        return $this->conn;
    }
}
?>
