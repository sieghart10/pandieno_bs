<?php
session_start();
include '../db.php';

$pdo = getReadConnection();

header('Content-Type: application/json');

ob_clean();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['cancel_order'])) {
    $orderId = $_POST['order_id'];

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
