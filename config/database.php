<?php
class Database {
    private static $instance = null;
    private $pdo;
    private $host = 'localhost';
    private $dbname = 'sistema_lanches';
    private $username = 'root';
    private $password = '';

    private function __construct() {
        try {
            $this->pdo = $this->createConnection(true);
        } catch (PDOException $e) {
            if ($this->isUnknownDatabaseError($e)) {
                try {
                    $this->bootstrapDatabase();
                } catch (Throwable $bootstrapError) {
                    die("Erro na conexão com o banco de dados: " . $bootstrapError->getMessage());
                }
            } else {
                die("Erro na conexão com o banco de dados: " . $e->getMessage());
            }
        }
    }

    private function createConnection($useDatabase = true) {
        $dsn = "mysql:host={$this->host};charset=utf8mb4";

        if ($useDatabase) {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        }

        $pdo = new PDO($dsn, $this->username, $this->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }

    private function isUnknownDatabaseError(PDOException $e) {
        $mysqlErrorCode = $e->errorInfo[1] ?? null;

        return (int) $mysqlErrorCode === 1049 || stripos($e->getMessage(), 'Unknown database') !== false;
    }

    private function bootstrapDatabase() {
        $serverConnection = $this->createConnection(false);
        $serverConnection->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $this->pdo = $this->createConnection(true);
        $this->importDatabaseSchema();
    }

    private function importDatabaseSchema() {
        $sqlFile = dirname(__DIR__) . '/database.sql';

        if (!file_exists($sqlFile)) {
            throw new RuntimeException('Arquivo database.sql não encontrado para inicializar o banco.');
        }

        $sql = file_get_contents($sqlFile);

        if ($sql === false) {
            throw new RuntimeException('Não foi possível ler o arquivo database.sql.');
        }

        $this->pdo->exec($sql);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Método para executar queries preparadas
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Método para obter último ID inserido
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}

// Função helper para obter conexão
function getDB() {
    return Database::getInstance()->getConnection();
}
?>