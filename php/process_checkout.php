<?php
session_start();
include '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to checkout']);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['items']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'No items selected']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT address_id FROM addresses 
        WHERE user_id = ? 
        ORDER BY address_id DESC 
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$address) {
        throw new Exception('No delivery address found');
    }

    $stmt = $pdo->prepare("
        INSERT INTO user_orders (user_id, date, payment_method, order_status, address_id)
        VALUES (?, NOW(), 'cash_on_delivery', 'pending', ?)
    ");
    $stmt->execute([$_SESSION['user_id'], $address['address_id']]);
    $orderId = $pdo->lastInsertId();

    foreach ($data['items'] as $item) {
        $stmt = $pdo->prepare("
            SELECT quantity as stock FROM books WHERE book_id = ?
        ");
        $stmt->execute([$item['book_id']]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$book) {
            throw new Exception("Book not found: " . $item['book_id']);
        }

        if ($book['stock'] < $item['quantity']) {
            throw new Exception("Insufficient stock for book: " . $item['title']);
        }

        $stmt = $pdo->prepare("
            UPDATE books 
            SET quantity = quantity - ?,
                sales_count = sales_count + ?,
                published_sales = published_sales + (price * ?)
            WHERE book_id = ?
        ");
        $stmt->execute([$item['quantity'], $item['quantity'], $item['quantity'], $item['book_id']]);

        $stmt = $pdo->prepare("
            INSERT INTO user_orders (order_id, book_id, user_id, price, quantity)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $orderId,
            $item['book_id'],
            $_SESSION['user_id'],
            $item['price'],
            $item['quantity']
        ]);

        $stmt = $pdo->prepare("
            DELETE FROM cart_items 
            WHERE cart_item_id = ?
        ");
        $stmt->execute([$item['cart_item_id']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $orderId]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}