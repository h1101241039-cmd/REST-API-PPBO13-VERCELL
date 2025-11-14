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
        // Default MySQL (karena Railway pakai MySQL)
        $this->type     = getenv('DB_TYPE') ?: 'mysql';
        $this->host     = getenv('DB_HOST') ?: 'localhost';
        $this->port     = getenv('DB_PORT') ?: '3306';
        $this->db_name  = getenv('DB_NAME') ?: 'kampus_db';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: 'mamakdoe-00';

        // Railway TIDAK pakai SSLMODE (hapus agar tidak error di MySQL)
        $this->sslmode  = ($this->type === 'pgsql') ? (getenv('DB_SSLMODE') ?: 'require') : '';
    }

    public function connect()
    {
        $this->conn = null;

        try {

            // Buat DSN tanpa sslmode untuk MySQL (karena membuat koneksi gagal)
            if ($this->type === 'mysql') {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name}";
            } 
            // Untuk PostgreSQL (jika suatu saat dipakai)
            else {
                $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name};sslmode={$this->sslmode}";
            }

            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {

            // Jika database belum dibuat → buat otomatis (lokal saja)
            if (strpos($e->getMessage(), 'Unknown database') !== false && $this->type === 'mysql') {

                $tempConn = new PDO("mysql:host={$this->host};port={$this->port}", $this->username, $this->password);
                $tempConn->exec("CREATE DATABASE IF NOT EXISTS {$this->db_name}");
                $tempConn = null;

                // Reconnect
                $this->conn = new PDO(
                    "mysql:host={$this->host};port={$this->port};dbname={$this->db_name}",
                    $this->username,
                    $this->password
                );

            } else {
                die(json_encode(["error" => "Koneksi gagal: " . $e->getMessage()]));
            }
        }

        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createTableIfNotExists();

        return $this->conn;
    }

    private function createTableIfNotExists()
    {
        if ($this->type === 'pgsql') {

            $sql = "
            CREATE TABLE IF NOT EXISTS mahasiswa (
                id SERIAL PRIMARY KEY,
                nama VARCHAR(100) NOT NULL,
                jurusan VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            ";

        } else {

            $sql = "
            CREATE TABLE IF NOT EXISTS mahasiswa (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nama VARCHAR(100) NOT NULL,
                jurusan VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";

        }

        $this->conn->exec($sql);
    }
}
