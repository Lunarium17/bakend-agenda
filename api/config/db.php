<?php
class Database {

    private $host = "localhost";
    private $port = "3307";
    private $db_name = "agenda_dbs";
    private $username = "root";
    private $password = "IGLOIWTR*5mark";
    public $conn;

    /*private $host = "sql113.infinityfree.com";
    private $port = "3306";
    private $db_name = "if0_40575858_agenda_dbs";
    private $username = "if0_40575858";
    private $password = "FQ4UqKEn54";
    public $conn;*/

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>