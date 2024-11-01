<?php
session_start();
include '../db.php'; // Database connection

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
