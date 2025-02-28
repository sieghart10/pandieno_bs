<?php
session_start();
include '../db.php';

$pdo = getReadConnection();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT username, first_name, middle_name, last_name, email, gender, birthday FROM users WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User not found.']);
}
