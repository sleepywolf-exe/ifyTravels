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
            if (defined('DB_CONNECTION') && DB_CONNECTION === 'mysql') {
                // MySQL Connection
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $this->pdo = new PDO($dsn, DB_USER, DB_PASS);
            } else {
                // SQLite Connection (Default fallback)
                $dbPath = defined('DB_SQLITE_PATH') ? DB_SQLITE_PATH : __DIR__ . '/../db/database.db';
                $isNew = !file_exists($dbPath);

                $this->pdo = new PDO('sqlite:' . $dbPath);

                if ($isNew) {
                    $this->initSchema();
                }
            }

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Check for specific MySQL errors effectively
            $msg = $e->getMessage();
            if (strpos($msg, 'Connection refused') !== false || strpos($msg, 'Access denied') !== false) {
                // Fallback to SQLite if MySQL fails? No, user explicitly requested CPanel connection.
                // We should throw error to warn them configuration is wrong.
                error_log("DB Connection Failed: $msg");
                die("Database Connection Error. Please check config.php settings.");
            }

            error_log("Database connection failed: " . $e->getMessage());
            die("Database Error.");
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