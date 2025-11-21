<?php
class Database
{
    private $type;
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $sslmode;
    public $conn;

    public function __construct() {
        $this->type     = getenv('DB_TYPE')     ?: 'pgsql';
        $this->host     = getenv('DB_HOST')     ?: getenv('PGHOST') ?: 'localhost';
        $this->port     = getenv('DB_PORT')     ?: getenv('PGPORT') ?: '5432';
        $this->db_name  = getenv('DB_NAME')     ?: getenv('PGDATABASE') ?: 'postgres';
        $this->username = getenv('DB_USER')     ?: getenv('PGUSER') ?: 'postgres';
        $this->password = getenv('DB_PASS')     ?: getenv('PGPASSWORD') ?: 'mamakdie-00';
        $this->sslmode  = getenv('DB_SSLMODE')  ?: 'require';
    }

    public function connect()
    {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name};sslmode={$this->sslmode}";

            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die(json_encode([
                "error" => "Koneksi gagal: " . $e->getMessage(),
                "host" => $this->host
            ]));
        }

        $this->createTableIfNotExists();
        return $this->conn;
    }

    private function createTableIfNotExists()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS mahasiswa (
                id SERIAL PRIMARY KEY,
                nama VARCHAR(100) NOT NULL,
                jurusan VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->conn->exec($sql);
    }
}
