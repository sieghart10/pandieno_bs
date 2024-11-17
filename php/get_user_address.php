<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT a.house_no, a.street, a.barangay, a.city, a.province 
        FROM addresses a 
        JOIN users u ON a.address_id = u.address_id 
        WHERE u.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$address = $stmt->fetch(PDO::FETCH_ASSOC);

if ($address) {
    echo json_encode($address);
} else {
    echo json_encode(['error' => 'Address not found.']);
}