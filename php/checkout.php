<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "User not logged in. Please log in to continue.";
    header("Location: login.php");
    exit;
}

$currentUser = null;
$address = null;
$orderItems = [];
$merchandiseTotal = 0;
$shippingTotal = 50; // Fixed shipping rate
$totalPayment = 0;
$cartItemCount = 0;

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch address data
if ($currentUser['address_id']) {
    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE address_id = ?");
    $stmt->execute([$currentUser['address_id']]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch cart items count to ensure there are items in the cart
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart_items ci JOIN carts c ON ci.cart_id = c.cart_id WHERE c.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$cartItemCount = $result['count'];

if ($cartItemCount == 0) {
    $_SESSION['error'] = "Your cart is empty. Please add items before proceeding to checkout.";
    header("Location: shoppingcart.php"); // Redirect to the cart page
    exit;
}

// Check if the user has selected any items for checkout
if (isset($_GET['items'])) {
    $selectedItems = json_decode($_GET['items'], true);
    if (!$selectedItems || count($selectedItems) == 0) {
        $_SESSION['error'] = "You must select items to proceed to checkout.";
        header("Location: shoppingcart.php"); // Redirect to cart page
        exit;
    }

    // Fetch selected items
    $placeholders = str_repeat('?,', count($selectedItems) - 1) . '?';
    $stmt = $pdo->prepare("SELECT b.*, ci.quantity 
                           FROM cart_items ci
                           JOIN books b ON ci.book_id = b.book_id
                           WHERE ci.cart_item_id IN ($placeholders)");
    $stmt->execute($selectedItems);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate merchandise total
    foreach ($orderItems as $item) {
        $merchandiseTotal += $item['price'] * $item['quantity'];
    }

    // Set shipping to 0 if merchandise total > 1000
    if ($merchandiseTotal > 1000) {
        $shippingTotal = 0;
    }

    // Calculate total payment
    $totalPayment = $merchandiseTotal + $shippingTotal;

}

if (count($orderItems) == 0) {
    $_SESSION['error'] = "You must select items to proceed to checkout.";
    header("Location: shoppingcart.php"); // Redirect to cart page
    exit;
}

// Process checkout when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    try {
        // Validate stock and place order
        foreach ($orderItems as $item) {
            $book_id = $item['book_id'];
            $quantity = $item['quantity'];
            
            // Check if stock is sufficient
            $stmt = $pdo->prepare("SELECT quantity FROM books WHERE book_id = ?");
            $stmt->execute([$book_id]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($book['quantity'] < $quantity) {
                throw new Exception("Not enough stock for {$item['title']}");
            }

            // Update book stock
            $stmt = $pdo->prepare("UPDATE books SET quantity = quantity - ? WHERE book_id = ?");
            $stmt->execute([$quantity, $book_id]);

            // Insert into user_orders table
            $stmt = $pdo->prepare("INSERT INTO user_orders (book_id, user_id, price, quantity, payment_method, order_status, address_id) 
                                   VALUES (?, ?, ?, ?, 'cash_on_delivery', 'pending', ?)");
            $stmt->execute([$book_id, $currentUser['user_id'], $item['price'], $quantity, $currentUser['address_id']]);
            
            // Capture the last inserted order ID (use the most recent order)
            $_SESSION['current_order_id'] = $pdo->lastInsertId();
        }

        // Remove items from the cart
        $placeholders = str_repeat('?,', count($selectedItems) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_item_id IN ($placeholders)");
        $stmt->execute($selectedItems);

        // Commit transaction
        $pdo->commit();

        // Redirect to success page
        $_SESSION['success'] = "Order placed successfully!";
        header("Location: order_success.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
        header("Location: checkout.php");
        exit;
    }
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
    <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
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
                            <div class="username-profile">
                                <span><?php echo htmlspecialchars($currentUser['username']); ?></span>
                                <a href="profile.php">
                                <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728921898/profile_evrssf.png" alt="User Profile Picture">
                                </a>
                            </div>
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
                    <!-- <a href="update_address.php" class="address-change">Change <i class="icon-edit"></i></a> -->
                </div>
            </div>            
        </section>

        <section class="products-top">
            <div class="products-header">
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
                    <p class="delivery">Cash on Delivery
                        <!-- <a href="#" class="change-address">Change <i class="icon-edit"></i></a> -->
                    </p>
                    <p class="Merchandise">Merchandise Subtotal <span>₱<?php echo number_format($merchandiseTotal, 2); ?></span></p>
                    <p class="Shipping">Shipping Total <span>₱<?php echo number_format($shippingTotal, 2); ?></span></p>
                    <p class="total">Total Payment <span class="prize">₱<?php echo number_format($totalPayment, 2); ?></span></p>
                </div>
            </div>
            <hr class="line" />
            <form method="POST" action="">
                <input type="hidden" name="items" value='<?php echo json_encode($orderItems); ?>'>
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
            // Show confirmation dialog
            var userConfirmed = confirm('Are you sure you want to place this order?');
            
            // If the user cancels, prevent form submission
            if (!userConfirmed) {
                e.preventDefault(); // Prevent form submission
            }
            // No need to prevent form submission when user confirms
        });
    </script>

</body>
</html>