<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // Update the user's status to inactive
    include '../db.php';
    $sql_update_status = "UPDATE users SET status = 'inactive' WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql_update_status);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to login page
    header("Location: http://localhost:3000/index.php");
    exit;
}
