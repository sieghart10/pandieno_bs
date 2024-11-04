<?php
session_start();
include '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cartItemId = $data['cart_item_id'];

$stmt = $pdo->prepare("
    DELETE FROM cart_items 
    WHERE cart_item_id = :cart_item_id
    AND cart_id = (SELECT cart_id FROM carts WHERE user_id = :user_id)
");
$stmt->bindParam(':cart_item_id', $cartItemId, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$success = $stmt->execute();

echo json_encode(['success' => $success]);