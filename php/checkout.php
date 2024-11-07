<?php
session_start();
include '../db.php';

// Check if user is logged in
$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username, email, first_name, last_name FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header('Location: login.php');
    exit;
}

// Get user's address
$stmt = $pdo->prepare("
    SELECT * FROM addresses a
    JOIN users u ON u.address_id = a.address_id
    WHERE u.user_id = :user_id
");
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$address = $stmt->fetch(PDO::FETCH_ASSOC);


$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$address = $stmt->fetch(PDO::FETCH_ASSOC);

// Initialize variables
$orderItems = [];
$merchandiseTotal = 0;
$shippingTotal = 0;
$totalPayment = 0;

// Get selected items from the cart
if (isset($_GET['items'])) {
    $selectedItems = json_decode($_GET['items'], true);
    
    // Validate selected items
    if (!is_array($selectedItems) || empty($selectedItems)) {
        header('Location: shoppingcart.php');
        exit;
    }
    
    // Prepare placeholders safely
    $placeholders = str_repeat('?,', count($selectedItems) - 1) . '?';
    
    $stmt = $pdo->prepare("
        SELECT ci.cart_item_id, ci.quantity, b.book_id, b.title, b.price, b.cover_image 
        FROM cart_items ci
        JOIN carts c ON ci.cart_id = c.cart_id
        JOIN books b ON ci.book_id = b.book_id
        WHERE ci.cart_item_id IN ($placeholders)
        AND c.user_id = ?  -- Added security check
    ");
    
    // Add user_id to the parameters for security
    $params = array_merge($selectedItems, [$_SESSION['user_id']]);
    $stmt->execute($params);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate totals
    $merchandiseTotal = array_reduce($orderItems, function($carry, $item) {
        return $carry + ($item['price'] * $item['quantity']);
    }, 0);

    // Calculate shipping based on total items and price
    $totalItems = array_sum(array_column($orderItems, 'quantity'));
    $shippingTotal = calculateShippingCost($totalItems, $merchandiseTotal);

    $totalPayment = $merchandiseTotal + $shippingTotal;
} else {
    header('Location: shoppingcart.php');
    exit;
}

// Get cart item count
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(quantity), 0) AS total_items 
    FROM cart_items 
    JOIN carts ON cart_items.cart_id = carts.cart_id 
    WHERE carts.user_id = :user_id
");
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$cartItemCount = $result['total_items'];

// Function to calculate shipping cost
function calculateShippingCost($totalItems, $merchandiseTotal) {
    // Base shipping rate
    $baseRate = 50;
    
    // Add additional shipping cost for every 5 items
    $additionalCost = floor($totalItems / 5) * 10;
    
    // Free shipping for orders over 1000
    if ($merchandiseTotal >= 1000) {
        return 0;
    }
    
    return $baseRate + $additionalCost;
}

$cartItemCount = 0;
if ($currentUser) {
    $stmt = $pdo->prepare("
        SELECT SUM(quantity) AS total_items 
        FROM cart_items 
        JOIN carts ON cart_items.cart_id = carts.cart_id 
        WHERE carts.user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cartItemCount = $result['total_items'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Checkout - Pandieño Bookstore</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/checkout.css" />
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet">
  </head>
<body>
    <nav>
        <div class="nav-container">
            <div class="left-nav">
                <ul>
                    <li>
                        <a href="../index.php">
                            <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1727950923/edcd0988-0c42-466e-b5fd-76660fe9afeb_ktknm8-removebg-preview_mmikc5.png" alt="logo">
                        </a>
                    </li>
                    <li>
                        <a href="../index.php"><h2>Pandieño Bookstore</h2></a>
                    </li>
                </ul>
            </div>
            <div class="right-nav">
                <ul>
                    <li>
                        <form method="GET" action="shoppingcart.php">
                            <input type="text" name="search" placeholder="Search in cart..." class="search-bar" 
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit">Search</button>
                        </form>
                    </li>
                    <li class="cart">
                        <a href="shoppingcart.php">
                            <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728372447/shopping-cart_1_v3hyar.png" alt="cart">
                            <?php if ($cartItemCount > 0): ?>
                                <span class="cart-count"><?php echo $cartItemCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <?php if ($currentUser): ?>
                            <span><?php echo htmlspecialchars($currentUser['username']); ?></span>
                            <a href="profile.php">
                                <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728921898/profile_evrssf.png" alt="Profile" style="width: 20px; height: 20px;">
                            </a>
                        <?php else: ?>
                            <a href="login.php">Log in</a> | <a href="signup.php">Sign up</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <main>
        <section class="delivery-address">
            <div class="address-header">
                <i class="icon-location"></i> Delivery Address
            </div>
            <div class="address-details">
                <p><i class="icon-user"></i><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></p>
                <div>
                    <?php if ($address): ?>
                        <p>
                            <?php echo htmlspecialchars(
                                $address['house_no'] . ' ' . 
                                $address['street'] . ', ' . 
                                $address['barangay'] . ', ' . 
                                $address['city'] . ', ' . 
                                $address['province']
                            ); ?>
                        </p>
                    <?php else: ?>
                        <p>No address found. Please add your delivery address.</p>
                    <?php endif; ?>
                    <a href="update_address.php" class="address-change">Change <i class="icon-edit"></i></a>
                </div>
            </div>            
        </section>

        <section class="products-top">
            <div>
                <h3>Products Ordered</h3>
                <h5>Price</h5>
                <h5>Quantity</h5>
                <h5>Item Subtotal</h5>
            </div>
        </section>

        <section class="product-ordered">
            <div class="product-details">
                <?php if (empty($orderItems)): ?>
                    <p>No items selected for checkout</p>
                <?php else: ?>
                    <?php foreach ($orderItems as $item): ?>
                        <div class="product-info">
                            <!-- <p class="delivery">Pandieño Bookstore | <a href="#" class="change-address">Chat Now<i class="icon-edit"></i></a></p> -->
                            <div class="product-item">
                                <img src="<?php echo htmlspecialchars($item['cover_image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 50px; margin-right: 10px;">
                                <div class="item-details">
                                    <div class="product-prices">
                                        <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                        <p class="unitiy">₱<?php echo number_format($item['price'], 2); ?></p>
                                        <p class="quantity"> <?php echo $item['quantity']; ?></p>
                                        <p class="itemsubtotal">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <div class="payment-method">
            <div class="payment-header">Payment Method</div>
            <div class="payment-details">
                <div class="payment-summary">
                    <p class="delivery">Cash on Delivery<a href="#" class="change-address">Change <i class="icon-edit"></i></a></p>
                    <p class="Merchandise">Merchandise Subtotal <span>₱<?php echo number_format($merchandiseTotal, 2); ?></span></p>
                    <p class="Shipping">Shipping Total <span>₱<?php echo number_format($shippingTotal, 2); ?></span></p>
                    <p class="total">Total Payment <span class="prize">₱<?php echo number_format($totalPayment, 2); ?></span></p>
                </div>
            </div>
            <p class="line">-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</p>
            <form id="orderForm" method="POST" action="http://localhost:3000/php/process_order.php">
                <input type="hidden" name="items" value="<?php echo htmlspecialchars($_GET['items']); ?>">
                <input type="hidden" name="total" value="<?php echo $totalPayment; ?>">
                <button type="submit" class="checkout-btn">Place Order</button>
            </form>
        </div>
    </main>
    <footer>
        <p>© 2024 Pandieño Bookstore. All Rights Reserved.</p>
    </footer>

    <script>
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to place this order?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>