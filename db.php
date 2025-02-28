<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class DatabaseConnection {
    private static $master_instance = null;
    private static $slave_instance = null;
    private static $master_priority = false;
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
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }

    public static function enableMasterPriority() {
        self::$master_priority = true;
    }

    public static function disableMasterPriority() {
        self::$master_priority = false;
    }

    public static function getMaster() {
        if (self::$master_instance === null) {
            $host = $_ENV['DB_MASTER_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];
            self::$master_instance = new DatabaseConnection($host, $dbname, $user, $pass);
        }
        return self::$master_instance->pdo;
    }

    public static function getSlave() {
        if (self::$slave_instance === null) {
            $host = $_ENV['DB_SLAVE_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];
            self::$slave_instance = new DatabaseConnection($host, $dbname, $user, $pass);
        }
        return self::$slave_instance->pdo;
    }

    public static function getConnection($isWrite = false) {
        try {
      	    if (!$isWrite && !self::$master_priority) {
                try {
                    return self::getSlave();
                } catch (Exception $e) {
                    error_log("Slave connection failed, falling back to master: " . $e->getMessage());
                    return self::getMaster();
                }
            }
            try {
                return self::getMaster();
            } catch (Exception $e) {
                if (!$isWrite) {
                    error_log("Master connection failed, attempting slave: " . $e->getMessage());
                    return self::getSlave();
                }
                throw $e;
            }
        } catch (Exception $e) {
            error_log("All database connections failed: " . $e->getMessage());
            throw $e;
        }
    }

}

function getReadConnection() {
    return DatabaseConnection::getConnection(false);
}

function getWriteConnection() {
    return DatabaseConnection::getConnection(true);
}

function getConnection() {
    return DatabaseConnection::getConnection(false);
}
