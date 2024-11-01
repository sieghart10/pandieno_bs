<?php
session_start();
include '../db.php';

$user_id = $_SESSION['user_id'];

// Fetch user's cart items
$stmt = $pdo->prepare("SELECT books.title, carts.quantity, books.price 
                       FROM carts 
                       JOIN books ON carts.item_id  = books.book_id 
                       WHERE carts.user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML to display cart -->
<h2>Your Cart</h2>
<ul>
  <?php foreach ($cart_items as $item): ?>
    <li><?php echo $item['title'] . " - Quantity: " . $item['quantity'] . " - Price: " . $item['price']; ?></li>
  <?php endforeach; ?>
</ul>
