<!-- profile.php -->
<?php
session_start(); // Start the session

include '../db.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "User not logged in. Please log in to continue.";
    header('Location: ../php/login.php'); // Redirect to login page
    exit();
}

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    // Query the database to get the user's data
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>Home</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" /> -->
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" /><!--font IM Fell DW Pica SC-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" /><!--font inter -->
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet"><!--im fell eng-->
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" />
    <link rel="stylesheet" href="http://localhost:3000/css/profile.css" />
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
                    <li class="cart">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="http://localhost:3000/php/shoppingcart.php">
                                <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728372447/shopping-cart_1_v3hyar.png" alt="cart">
                                <?php if ($cartItemCount > 0): ?>
                                    <span class="cart-count"><?php echo $cartItemCount; ?></span>
                                <?php endif; ?>
                            </a>
                        <?php else: ?>
                            <a href="http://localhost:3000/php/login.php">
                                <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728372447/shopping-cart_1_v3hyar.png" alt="cart">
                            </a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if (!isset($_SESSION['user_id'])): // Check if user is logged out ?>
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
                    <li>
                        <a href="http://localhost:3000/php/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <section class="profile-burger">
            <h3>My&nbspAccount</h3>
            <a href="#" class="profile" id="profile-link">Profile</a>
            <a href="#" class="address" id="address-link">Address</a>
            <a href="#" class="orders" id="orders-link">Orders</a>
        </section>
        <section class="profile-section" id="content-section">
            <!-- Content dynamically updated here -->
            <div>
                <h1>Welcome to your account</h1>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Pandiemo Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
