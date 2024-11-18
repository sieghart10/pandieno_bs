<?php
session_start();
include '../db.php';

header('Content-Type: application/json');

// Ensure no other output is sent before JSON response
ob_clean();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if it's a cancel order request
if (isset($_POST['cancel_order'])) {
    $orderId = $_POST['order_id'];

    // Update order status to cancelled
    $sql = "UPDATE user_orders SET order_status = 'cancelled' WHERE order_id = :order_id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':order_id' => $orderId,
        ':user_id' => $user_id
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => 'Order cancelled successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to cancel the order.']);
    }
    exit(); // stop further execution after cancellation
}

// Fetch user's orders if it's not a cancel request
$sql = " SELECT o.order_id, o.book_id, o.price, o.quantity, o.date, o.payment_method, o.order_status, b.title, b.cover_image
        FROM user_orders o
        JOIN books b ON o.book_id = b.book_id
        WHERE o.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($orders) {
    echo json_encode($orders);
} else {
    echo json_encode(['error' => 'No orders found.']);
    exit();
}
if (isset($_POST['delete_order'])) {
    $orderId = $_POST['order_id'];

    $sql = "DELETE FROM user_orders WHERE order_id = :order_id AND user_id = :user_id AND order_status = 'cancelled'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':order_id' => $orderId,
        ':user_id' => $_SESSION['user_id']
    ]);

    if ($stmt->rowCount() > 0) {
        echo "Order deleted successfully.";
    } else {
        echo "Failed to delete the order.";
    }
}