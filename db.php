<?php
class DatabaseConnection {
    private static $master_instance = null;
    private static $slave_instance = null;
    private $pdo;

    private function __construct($host, $dbname, $user, $pass) {
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Log the error details
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }

    public static function getMaster() {
        if (self::$master_instance === null) {
            $host = getenv('DB_MASTER_HOST') ?: '192.168.8.109';
            $dbname = getenv('DB_NAME') ?: 'pandieno_bookstore';
            $user = getenv('DB_USER') ?: 'pandieno_user';
            $pass = getenv('DB_PASS') ?: '2109';
            
            self::$master_instance = new DatabaseConnection($host, $dbname, $user, $pass);
        }
        return self::$master_instance->pdo;
    }

    public static function getSlave() {
        if (self::$slave_instance === null) {
            $host = getenv('DB_SLAVE_HOST') ?: '192.168.8.110';
            $dbname = getenv('DB_NAME') ?: 'pandieno_bookstore';
            $user = getenv('DB_USER') ?: 'pandieno_user';
            $pass = getenv('DB_PASS') ?: '2109';  // Use the same password as master
            
            self::$slave_instance = new DatabaseConnection($host, $dbname, $user, $pass);
        }
        return self::$slave_instance->pdo;
    }

    public static function getConnection($isWrite = false) {
        try {
            return $isWrite ? self::getMaster() : self::getSlave();
        } catch (Exception $e) {
            // If slave fails, fallback to master
            if (!$isWrite) {
                try {
                    return self::getMaster();
                } catch (Exception $e) {
                    error_log("Both master and slave connections failed: " . $e->getMessage());
                    throw $e;
                }
            }
            throw $e;
        }
    }
}

// Helper functions for backward compatibility
function getReadConnection() {
    return DatabaseConnection::getConnection(false);
}

function getWriteConnection() {
    return DatabaseConnection::getConnection(true);
}

function getConnection() {
    return getReadConnection();
}