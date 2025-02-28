<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include '../db.php';

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Ignore comments
        list($key, $value) = explode('=', $line, 2);
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

$serverIP = $_ENV['SERVER_IP'] ?? '127.0.0.1';

$pdo = getReadConnection();

if (isset($_SESSION['user_id'])) {
    // Update the user's status to inactive

    $sql_update_status = "UPDATE users SET status = 'inactive' WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql_update_status);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to login page
    header("Location: http://$serverIP/pandieno_bookstore/index.php");
    exit;
}
