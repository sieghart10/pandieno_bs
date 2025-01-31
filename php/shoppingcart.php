<!-- shoppingcart.php -->
<?php
session_start();
include '../db.php';

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header('Location: login.php');
    exit;
}

$cartItems = [];
if ($currentUser) {
    $searchQuery = '';
    if (isset($_GET['search'])) {
        $searchQuery = trim($_GET['search']);
        $searchParam = '%' . $searchQuery . '%';
        
        $stmt = $pdo->prepare("
            SELECT ci.cart_item_id, ci.quantity, b.title, b.price, b.cover_image 
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.cart_id
            JOIN books b ON ci.book_id = b.book_id
            WHERE c.user_id = :user_id
            AND (
                b.title LIKE :search 
                OR b.author LIKE :search 
                OR b.category LIKE :search
            )
        ");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT ci.cart_item_id, ci.quantity, b.title, b.price, b.cover_image 
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.cart_id
            JOIN books b ON ci.book_id = b.book_id
            WHERE c.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
    }
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Shopping Cart</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" />
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/shoppingcart.css" />
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet">
    <script src="http://localhost:3000/scripts/quantity.js" defer></script>
    <script src="http://localhost:3000/scripts/checkout.js" defer></script>
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
                </ul>
            </div>
            <div class="right-nav">
                <ul>
                    <li>
                        <!-- Modified search form to search within cart items -->
                        <form method="GET" action="shoppingcart.php">
                            <input type="text" name="search" placeholder="Search in cart..." class="search-bar" 
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit">Search</button>
                        </form>
                    </li>
                    <li class="cart">
                        <a href="http://localhost:3000/php/shoppingcart.php">
                            <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728372447/shopping-cart_1_v3hyar.png" alt="cart">
                            <?php if ($cartItemCount > 0): ?>
                                <span class="cart-count"><?php echo $cartItemCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="http://localhost:3000/php/login.php">Log in</a> | <a href="http://localhost:3000/php/signup.php">Sign up</a>
                        <?php else: ?>
                            <div class="username-profile">
                                <span><?php echo htmlspecialchars($currentUser['username']); ?></span>
                                <a href="../php/profile.php">
                                <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728921898/profile_evrssf.png" alt="User Profile Picture">
                                </a>
                            </div>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <main>
    <section class="all-products">
        <div class="product-details">
            <p class="product">Product</p>
            <p class="unit-price">Unit Price</p>
            <p class="quantity">Quantity</p>
            <p class="totalprice">Total Price</p>
            <p class="action">Action</p>
        </div>
    </section>

    <?php if (!empty($cartItems)): // Only show the item section if there are items ?>
        <section class="item-section">
            <?php foreach ($cartItems as $item): ?>
                <div class="items">
                    <input type="checkbox" id="checkbox-item-<?php echo $item['cart_item_id']; ?>" class="item-select" name="checkbox-item">
                    <div class="item-cover-title">
                        <img src="<?php echo htmlspecialchars($item['cover_image']); ?>" alt="item-image" class="item-image">
                        <p class="item-title"><?php echo htmlspecialchars($item['title']); ?></p>
                    </div>
                    <p class="item-price">₱<?php echo number_format($item['price'], 2); ?></p>
                    <div class="quantity-control">
                        <button class="decrease" onclick="decreaseQuantity('item-quantity-<?php echo $item['cart_item_id']; ?>')">-</button>
                        <span id="item-quantity-<?php echo $item['cart_item_id']; ?>" class="item-quantity"><?php echo htmlspecialchars($item['quantity']); ?></span>
                        <button class="increase" onclick="increaseQuantity('item-quantity-<?php echo $item['cart_item_id']; ?>')">+</button>
                    </div>
                    <p class="item-totalprice">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                    <button class="item-action" onclick="deleteItem(<?php echo $item['cart_item_id']; ?>)">Delete</button>
                </div>
                <input type="hidden" class="book-id" value="<?php echo $book['book_id']; ?>">
            <?php endforeach; ?>
        </section>
        <?php else: // Show a message if the cart is empty ?>
            <section class="item-section">
                <?php if (isset($_GET['search'])): ?>
                    <p>No items found matching "<?php echo htmlspecialchars($_GET['search']); ?>" in your cart.</p>
                <?php else: ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <div class="foot-details">
            <div class="left-foot">
                <ul>
                    <li>
                        <input type="checkbox" id="checkbox-foot" name="checkbox-foot" class="select-all-checkbox" onclick="toggleSelectAll()">
                        <label class="select-all" for="checkbox-foot">Select All (0)</label>
                    </li>
                    <li>
                        <button class="delete-all">Delete All</button>
                    </li>
                </ul>
            </div>
            <div>
                <ul>
                    <li>
                        <p class="totalitems"></p>
                        <a href="javascript:void(0);" onclick="checkoutSelectedItems()">
                            <button class="checkout-btn">Check Out</button>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>