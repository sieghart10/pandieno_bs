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
    <title>Home</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" /> -->
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" /><!--font IM Fell DW Pica SC-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" /><!--font inter -->
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet"><!--im fell eng-->
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" />
    <link rel="stylesheet" href="http://localhost:3000/css/order_success.css" />
    <script type="module" src="../scripts/userProfile.js" defer></script>
</head>
<body>
    <nav>
        <div class="nav-container">
            <div class="left-nav">
                <ul>
                    <li>
                    <a href="http://localhost:3000/index.php">
                        <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1727950923/edcd0988-0c42-466e-b5fd-76660fe9afeb_ktknm8-removebg-preview_mmikc5.png" alt="logo">
                    </a>
                    </li>
                    <li>
                        <a href="http://localhost:3000/index.php"><h2>Pandieño Bookstore</h2></a>
                    </li>
                    <li><h3>|&nbsp&nbspProfile</h3></li>
                </ul>
            </div>
            <div class="right-nav">
                <ul>
                    <!-- <li>
                        <input type="text" placeholder="Search item..." class="search-bar">
                    </li> -->
                    <li>
                        <a href="http://localhost:3000/php/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>            
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
    </main>
    <footer>
        <p>&copy; 2024 Pandiemo Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>




