<?php
// Master database connection parameters (for writes)
$master_host = getenv('DB_MASTER_HOST') ?: '192.168.8.109';
$master_db = getenv('DB_NAME') ?: 'pandieno_bookstore';
$master_user = getenv('DB_USER') ?: 'root';
$master_pass = getenv('DB_PASS') ?: '';

// Slave database connection parameters (for reads)
$slave_host = getenv('DB_SLAVE_HOST') ?: '192.168.8.110';
$slave_db = getenv('DB_NAME') ?: 'pandieno_bookstore';
$slave_user = getenv('DB_USER') ?: 'root';
$slave_pass = getenv('DB_PASS') ?: '';

// Create both PDO instances
try {
    // Master connection for writes
    $master_pdo = new PDO(
        "mysql:host=$master_host;dbname=$master_db;charset=utf8",
        $master_user,
        $master_pass
    );
    $master_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $master_pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");

    // Slave connection for reads
    $slave_pdo = new PDO(
        "mysql:host=$slave_host;dbname=$slave_db;charset=utf8",
        $slave_user,
        $slave_pass
    );
    $slave_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $slave_pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
} catch (PDOException $e) {
    error_log('Connection failed: ' . $e->getMessage());
    die('Database connection error. Please try again later.');
}

// Function to get connection for read operations
function getReadConnection() {
    global $slave_pdo;
    return $slave_pdo;
}

// Function to get connection for write operations
function getWriteConnection() {
    global $master_pdo;
    return $master_pdo;
}

// General connection function (defaults to read connection)
function getConnection() {
    return getReadConnection();
}
