<?php
session_start();
include '../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['book_id']) || !isset($data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'book_id and quantity are required']);
    exit;
}

$book_id = $data['book_id'];
$quantity = $data['quantity'];

if (!is_numeric($book_id) || !is_numeric($quantity)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input values']);
    exit;
}

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in.']);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cart) {
        $stmt = $pdo->prepare("INSERT INTO carts (user_id) VALUES (:user_id)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $cart_id = $pdo->lastInsertId();
    } else {
        $cart_id = $cart['cart_id'];
    }

    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE cart_id = :cart_id AND book_id = :book_id");
    $stmt->bindParam(':cart_id', $cart_id);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        $new_quantity = $cart_item['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id");
        $stmt->bindParam(':quantity', $new_quantity);
        $stmt->bindParam(':cart_item_id', $cart_item['cart_item_id']);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart_items (cart_id, book_id, quantity) VALUES (:cart_id, :book_id, :quantity)");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->bindParam(':book_id', $book_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Book added to cart!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

// file_put_contents('debug.log', print_r($data, true), FILE_APPEND);
