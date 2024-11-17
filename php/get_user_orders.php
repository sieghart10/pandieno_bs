<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT o.order_id, o.book_id, o.price, o.quantity, o.date, o.payment_method, o.order_status, b.title 
        FROM user_orders o 
        JOIN books b ON o.book_id = b.book_id 
        WHERE o.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($orders) {
    echo json_encode($orders);
} else {
    echo json_encode(['error' => 'No orders found.']);
}

$sql = "SELECT cover_image FROM books WHERE book_id = :book_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':book_id', $bookId); // $bookId is the ID of the ordered book
$stmt->execute();
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if ($book) {
    $sql = "INSERT INTO user_orders (book_id, user_id, price, quantity, payment_method, order_status, cover_image, address_id) 
            VALUES (:book_id, :user_id, :price, :quantity, :payment_method, :order_status, :cover_image, :address_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':book_id' => $bookId,
        ':user_id' => $userId,
        ':price' => $price,
        ':quantity' => $quantity,
        ':payment_method' => $paymentMethod,
        ':order_status' => 'pending',
        ':cover_image' => $book['cover_image'],
        ':address_id' => $addressId
    ]);
}

