<?php
// includes/db.php - Optimized Database Manager with Singleton Pattern

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        // Load config if exists
        $configFile = __DIR__ . '/config.php';
        if (file_exists($configFile)) {
            require_once $configFile;
        }

        try {
            if (defined('DB_CONNECTION') && DB_CONNECTION === 'sqlite') {
                // SQLite Connection
                $dbPath = __DIR__ . '/../db/database.sqlite';
                $dbDir = dirname($dbPath);
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0777, true);
                }
                // Check if new DB to init schema later
                $isNewDb = !file_exists($dbPath);

                $this->pdo = new PDO("sqlite:" . $dbPath);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Init Schema if needed or if getting started
                if ($isNewDb || filesize($dbPath) == 0) {
                    $this->initSchema();
                }
            } else {
                // MySQL Connection
                $dsn = "mysql:host=" . (defined('DB_HOST') ? DB_HOST : '127.0.0.1') . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $this->pdo = new PDO($dsn, DB_USER, DB_PASS);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }

        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());

            // Check if API request (JSON)
            $isApi = (
                (defined('IS_API') && IS_API) ||
                (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
            );

            if ($isApi) {
                // For APIs, let the caller handle the error (to return JSON)
                throw new Exception("Database Connection Failed");
            }

            // Throw exception so it can be caught by the caller (functions.php or index.php)
            // This allows the app to fallback to mock data if DB is down.
            throw new Exception("Database Connection Failed: " . $e->getMessage());
        }
    }

    // Singleton Pattern
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    private function initSchema()
    {
        $schemaFile = __DIR__ . '/../db/schema_sqlite.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            $this->pdo->exec($sql);
        }
    }

    // Helper methods for common operations
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage());
            return false;
        }
    }

    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : null;
    }

    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function execute($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt !== false;
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    // Prevent cloning and unserialization
    private function __clone()
    {
    }
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}