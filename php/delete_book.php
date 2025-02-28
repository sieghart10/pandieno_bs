<?php
session_start();
include '../db.php'; // Database connection

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

// Check if the user is logged in (optional, if you have authentication)
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = intval($_POST['book_id']); 

   
    $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = :book_id");
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->execute();


    header("Location: admin_dashboard.php");
    exit();
} else {

    header("Location: admin_dashboard.php");
    exit();
}
