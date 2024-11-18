<?php
session_start();
include '../db.php';

// Check if there is a success message and order ID in session
if (!isset($_SESSION['success']) || !isset($_SESSION['current_order_id'])) {
    // Redirect to checkout if no success message or order ID is available
    header("Location: checkout.php");
    exit;
}

// Retrieve the success message
$successMessage = $_SESSION['success'];
unset($_SESSION['success']);  // Unset after using it

// Retrieve the order ID from session
$orderId = $_SESSION['current_order_id'];
unset($_SESSION['current_order_id']);  // Unset after using it

// Get the user's details
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's address (check if the address_id exists in the users table)
if ($currentUser['address_id']) {
    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE address_id = ?");
    $stmt->execute([$currentUser['address_id']]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch order details for the current order ID
$stmt = $pdo->prepare("SELECT uo.*, b.title, b.price, b.cover_image 
                       FROM user_orders uo
                       JOIN books b ON uo.book_id = b.book_id
                       WHERE uo.order_id = ?");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total order amount
$totalAmount = 0;
foreach ($orderItems as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

$shippingTotal = 50;  // Fixed shipping fee
$grandTotal = $totalAmount + $shippingTotal;

// Prepare address display
// Prepare address display using the available fields
$addressDisplay = $address 
    ? htmlspecialchars($address['house_no']) . ' ' . htmlspecialchars($address['street']) . ', ' 
      . htmlspecialchars($address['barangay']) . ', ' . htmlspecialchars($address['city']) . ', ' 
      . htmlspecialchars($address['province'])
    : 'No address available.';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file -->
</head>
<body>

    <div class="order-success-container">
        <h1>Order Successful!</h1>
        <p>Thank you, <?= htmlspecialchars($currentUser['first_name']) ?>, for your order!</p>
        <p>Your order has been placed successfully and is now being processed.</p>

        <h2>Order Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Shipping Details</h3>
        <p><strong>Address:</strong> <?= $addressDisplay ?></p>
        <p><strong>Shipping Fee:</strong> $<?= number_format($shippingTotal, 2) ?></p>

        <h3>Total Amount</h3>
        <p><strong>Subtotal:</strong> $<?= number_format($totalAmount, 2) ?></p>
        <p><strong>Grand Total:</strong> $<?= number_format($grandTotal, 2) ?></p>

        <p>If you have any questions or need assistance, please contact our customer service.</p>

        <a href="../index.php" class="btn">Return to Home</a> <!-- Redirect to home page or other page -->
    </div>

</body>
</html>
