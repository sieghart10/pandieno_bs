<?php
// Database connection parameters
$host = getenv('DB_HOST') ?: 'localhost';
$db = getenv('DB_NAME') ?: 'pandieno_bookstore';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
} catch (PDOException $e) {
    // Log and handle connection error
    error_log('Connection failed: ' . $e->getMessage());
    die('Database connection error. Please try again later.');
}

// Function to get the PDO instance
function getConnection() {
    global $pdo;
    return $pdo;
}
