<?php
session_start();
include '../db.php';

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    // Query the database to get the user's data
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Home</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" />
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/shoppingcart.css" />
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet"/><!--font IM Fell DW Pica SC-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" /><!--font inter -->
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet"><!--im fell eng-->
    <script type="module" src="http://localhost:3000/scripts/quantity.js" defer></script>
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
                        <form method="GET" action="index.php">
                            <input type="text" name="search" placeholder="Search item..." class="search-bar">
                            <button type="submit">Search</button>
                        </form>
                    </li>
                    <li class="cart">
                        <a href="http://localhost:3000/php/shoppingcart.php">
                            <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728372447/shopping-cart_1_v3hyar.png" alt="cart">
                            <span class="cart-count">2</span>
                        </a>
                    </li>
                    <li>
                        <?php if (!isset($_SESSION['user_id'])): // Check if user is logged out ?>
                            <a href="http://localhost:3000/php/login.php">Log in</a> | <a href="http://localhost:3000/php/signup.php">Sign up</a>
                        <?php else: ?>
                            <span><?php echo htmlspecialchars($currentUser['username']); ?></span>
                            <a href="http://localhost:3000/php/profile.php">
                                <img src="path_to_profile_icon.png" alt="Profile" style="width: 20px; height: 20px;">.
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

  <main>
    <section class="all-products">
        <div class="product-details">
            <input type="checkbox" id="checkbox-top" name="checkbox-top">
            <label for="checkbox-top">Product</label>
            <p class="unit-price">Unit Price</p>
            <p class="quantity">Quantity</p>
            <p class="totalprice">Total Price</p>
            <p class="action">Action</p>
        </div>
    </section>
    <section>
        <section class="product">
            <div class="shop-details">
                <input type="checkbox" id="checkbox-shop" name="checkbox-shop">
                <label for="checkbox-shop">Shop Name</label>
            </div>
        </section>
        <section class="item-section">
            <div class="items">
                <input type="checkbox" id="checkbox-item" class="item-select" name="checkbox-item">
                <img src="" alt="item-image" class="item-image">
                <p class="item-price">₱</p>
                <div class="quantity-control">
                    <button class="decrease" onclick="decreaseQuantity('item-quantity-1')">-</button>
                    <span id="item-quantity-1" class="item-quantity" data-max="<?php echo htmlspecialchars($user_orders['quantity']); ?>">1</span>
                    <button class="increase" onclick="increaseQuantity('item-quantity-1')">+</button>
                </div>
                <p class="item-totalprice">₱</p>
                <button class="item-action">Delete</button>
            </div>
        </section>
    </section>
  </main>
  <footer>
    <div class="foot-details">
        <div class="left-foot">
            <ul>
                <li>
                    <input type="checkbox" id="checkbox-foot" name="checkbox-foot">
                    <label for="checkbox-foot">Select All (0)</label>
                </li>
                <li>
                    <button class="delete-all">Delete All</button>
                </li>
            </ul>
        </div>
        <div>
            <ul>
                <li>
                    <p class="totalitems">Total (0 Item/s): ₱ 0</p>
                    <button class="checkout-btn">Check Out</button>
                </li>
            </ul>
        </div>
    </div>
  </footer>
</body>
</html>