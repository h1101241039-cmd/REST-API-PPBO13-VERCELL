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
        $this->type     = $_ENV['DB_TYPE']     ?? 'pgsql';
        $this->host     = $_ENV['DB_HOST']     ?? 'localhost';
        $this->port     = $_ENV['DB_PORT']     ?? '5432';
        $this->db_name  = $_ENV['DB_NAME']     ?? 'postgres';
        $this->username = $_ENV['DB_USER']     ?? 'postgres';
        $this->password = $_ENV['DB_PASS']     ?? '';
        $this->sslmode  = $_ENV['DB_PASS']     ??'require'; // Supabase WAJIB SSL
    }

    public function connect()
    {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name};sslmode=require";

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
