<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch user's cart items
$stmt = $pdo->prepare("SELECT books.title, cart.quantity, books.price 
                       FROM cart 
                       JOIN books ON cart.book_id = books.id 
                       WHERE cart.user_id = :user_id");
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
