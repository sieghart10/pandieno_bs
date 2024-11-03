 <!-- index.php -->
<?php
session_start();
include 'db.php';

// Search query logic remains unchanged
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    // Update the SQL query to include the keyword search
    $stmt = $pdo->prepare("
        SELECT book_id, title, cover_image 
        FROM books 
        WHERE title LIKE :search 
          OR author LIKE :search 
          OR category LIKE :search 
          OR description LIKE :search  
          OR keywords LIKE :search
    ");
    $stmt->execute(['search' => '%' . $searchQuery . '%']);
} else {
    $stmt = $pdo->query('SELECT book_id, title, cover_image FROM books');
}

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the current user's data if logged in
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
    <title>Pandieño Bookstore</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" /><!--font IM Fell DW Pica SC-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" /><!--font inter -->
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet"><!--im fell eng-->
    <link rel="stylesheet" href="http://localhost:3000/css/mainpage.css" />
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" />
    <!-- <script type="module" src="http://localhost:3000/scripts/script.js" defer></script> -->
</head>
<body>
    <nav>
        <div class="nav-container">
            <div class="left-nav">
                <ul>
                    <li>
                        <a href="index.php">
                            <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1727950923/edcd0988-0c42-466e-b5fd-76660fe9afeb_ktknm8-removebg-preview_mmikc5.png" alt="logo">
                        </a>
                    </li>
                    <li>
                        <a href="index.php"><h2>Pandieño Bookstore</h2></a>
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
                            <span><?php echo htmlspecialchars($currentUser['username']); ?></span>
                            <a href="http://localhost:3000/php/profile.php">
                                <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728921898/profile_evrssf.png" alt="Profile" style="width: 20px; height: 20px;">
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <section class="filters">
            <div>
                <span id="sort_color">Sort by:</span>
                <select>
                    <option>By Category</option>
                    <option>Fantasy</option>
                    <option>Thriller</option>
                    <option>Fiction</option>
                    <option>Horror</option>
                    <option>Romance</option>
                    <option>Mystery</option>
                    <option>Nonfiction</option>
                    <option>Poetry</option>
                    <option>Novel</option>
                    <option>Adventure</option>
                </select>
                <select>
                    <option>Latest</option>
                    <option>Newest - Oldest</option>
                    <option>Oldest - Newest</option>
                </select>
                <select>
                    <option>Price Range</option>
                    <option>Low - High</option>
                    <option>High - Low</option>
                </select>
            </div>
            <div>
                <ul>
                    <li>
                        <span><a href="http://localhost:3000/php/filter_page.php?filter=top_rated">Top Rated</a></span>
                    </li>
                    <li>
                        <span> | </span>
                        <span><a href="http://localhost:3000/php/filter_page.php?filter=latest">Latest</a></span>
                    </li>
                    <li>
                        <span> | </span>
                        <span><a href="http://localhost:3000/php/filter_page.php?filter=top_sales">Top Sales</a></span>
                    </li>
                </ul>
            </div>
            <div class="book-container">
                <?php if (empty($books)): ?>
                    <p>No books found for "<?php echo htmlspecialchars($searchQuery); ?>".</p>
                <?php else: ?>
                    <?php foreach ($books as $book): ?>
                        <div class="book-item">
                            <a href="http://localhost:3000/php/item_page.php?book_id=<?php echo $book['book_id']; ?>">
                                <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                <p><?php echo htmlspecialchars($book['title']); ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <footer>
        <p>© 2024 Pandieño Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
