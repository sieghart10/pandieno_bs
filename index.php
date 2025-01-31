 <!-- index.php -->
<?php
session_start();
require_once 'db.php';  // Changed from include to require_once

$env = parse_ini_file(__DIR__ . '/.env');

// Define server IP address from environment variable
$serverIP = isset($env['SERVER_IP']) ? $env['SERVER_IP'] : '127.0.0.1'; // Default to localhost if not set

try {
    // Get read connection for SELECT queries
    $pdo = getReadConnection();
    
    // Initialize variables
    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';
    $price = isset($_GET['price']) ? trim($_GET['price']) : '';

    // Fetch current user details if logged in
    $currentUser = null;
    $cartItemCount = 0;
    if (isset($_SESSION['user_id'])) {
        $userId = intval($_SESSION['user_id']); 

        // Get user data - using read connection
        $stmt = $pdo->prepare("SELECT username, email FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get cart item count - using read connection
        $stmt = $pdo->prepare("
            SELECT SUM(quantity) AS total_items 
            FROM cart_items 
            JOIN carts ON cart_items.cart_id = carts.cart_id 
            WHERE carts.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $cartResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $cartItemCount = $cartResult['total_items'] ?? 0;
    }

    // Base query for books
    $query = "SELECT book_id, title, cover_image FROM books WHERE 1=1";
    $params = [];

    // Add search filtering
    if (!empty($searchQuery)) {
        $query .= " AND (title LIKE :search OR author LIKE :search OR category LIKE :search OR description LIKE :search OR keywords LIKE :search)";
        $params['search'] = '%' . $searchQuery . '%';
    }

    // Add category filtering
    if (!empty($category)) {
        $query .= " AND category = :category";
        $params['category'] = $category;
    }

    // Add sorting
    $orderBy = [];
    if ($sort === 'newest') {
        $orderBy[] = "publish_date DESC";
    } elseif ($sort === 'oldest') {
        $orderBy[] = "publish_date ASC";
    }

    if ($price === 'low') {
        $orderBy[] = "price ASC";
    } elseif ($price === 'high') {
        $orderBy[] = "price DESC";
    }

    if (!empty($orderBy)) {
        $query .= " ORDER BY " . implode(", ", $orderBy);
    }

    // Prepare and execute the final query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle database errors gracefully
    die("Error fetching data: " . $e->getMessage());
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
    <link rel="stylesheet" href="http://<?php echo htmlspecialchars($serverIP); ?>/css/mainpage.css" />
    <link rel="stylesheet" type="text/css" href="http://<?php echo htmlspecialchars($serverIP); ?>/css/main.css" />
    <!-- <script type="module" src="http://<?php echo htmlspecialchars($serverIP); ?>/scripts/script.js" defer></script> -->
    <script>
        function submitForm() {
            document.getElementById('filter-form').submit();
        }
    </script>

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
                            <a href="http://<?php echo htmlspecialchars($serverIP); ?>/php/shoppingcart.php">
                                <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728372447/shopping-cart_1_v3hyar.png" alt="cart">
                                <?php if ($cartItemCount > 0): ?>
                                    <span class="cart-count"><?php echo $cartItemCount; ?></span>
                                <?php endif; ?>
                            </a>
                        <?php else: ?>
                            <a href="http://<?php echo htmlspecialchars($serverIP); ?>/php/login.php">
                                <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728372447/shopping-cart_1_v3hyar.png" alt="cart">
                            </a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if (!isset($_SESSION['user_id'])): // Check if user is logged out ?>
                            <a href="http://<?php echo htmlspecialchars($serverIP); ?>/php/login.php">Log in</a> | <a href="http://<?php echo htmlspecialchars($serverIP); ?>/php/signup.php">Sign up</a>
                        <?php else: ?>
                            <div class="username-profile">
                                <span><?php echo htmlspecialchars($currentUser['username']); ?></span>
                                <a href="/php/profile.php">
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
        <section class="filters">
            <div class="sort-books">
                <form method="GET" action="index.php" id="filter-form">
                    <span id="sort_color">Sort by:</span>
                    <select name="category" onchange="submitForm()">
                        <option value="" <?php echo empty($category) ? 'selected' : ''; ?>>By Category</option>
                        <option value="comedy" <?php echo $category === 'comedy' ? 'selected' : ''; ?>>Comedy</option>
                        <option value="horror" <?php echo $category === 'horror' ? 'selected' : ''; ?>>Horror</option>
                        <option value="drama" <?php echo $category === 'drama' ? 'selected' : ''; ?>>Drama</option>
                        <option value="action" <?php echo $category === 'action' ? 'selected' : ''; ?>>Action</option>
                        <option value="romance" <?php echo $category === 'romance' ? 'selected' : ''; ?>>Romance</option>
                        <option value="sci-fi" <?php echo $category === 'sci-fi' ? 'selected' : ''; ?>>Sci-Fi</option>
                        <option value="fantasy" <?php echo $category === 'fantasy' ? 'selected' : ''; ?>>Fantasy</option>
                        <option value="mystery" <?php echo $category === 'mystery' ? 'selected' : ''; ?>>Mystery</option>
                        <option value="thriller" <?php echo $category === 'thriller' ? 'selected' : ''; ?>>Thriller</option>
                        <option value="historical" <?php echo $category === 'historical' ? 'selected' : ''; ?>>Historical</option>
                        <option value="biography" <?php echo $category === 'biography' ? 'selected' : ''; ?>>Biography</option>
                        <option value="self-help" <?php echo $category === 'self-help' ? 'selected' : ''; ?>>Self-Help</option>
                        <option value="children" <?php echo $category === 'children' ? 'selected' : ''; ?>>Children</option>
                        <option value="young adult" <?php echo $category === 'young adult' ? 'selected' : ''; ?>>Young Adult</option>
                        <option value="non-fiction" <?php echo $category === 'non-fiction' ? 'selected' : ''; ?>>Non-Fiction</option>
                        <option value="poetry" <?php echo $category === 'poetry' ? 'selected' : ''; ?>>Poetry</option>
                        <option value="graphic novel" <?php echo $category === 'graphic novel' ? 'selected' : ''; ?>>Graphic Novel</option>
                    </select>
                    <select name="sort" onchange="submitForm()">
                        <option value="" <?php echo empty($sort) ? 'selected' : ''; ?>>Latest</option>
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest - Oldest</option>
                        <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest - Newest</option>
                    </select>
                    <select name="price" onchange="submitForm()">
                        <option value="" <?php echo empty($price) ? 'selected' : ''; ?>>Price Range</option>
                        <option value="low" <?php echo $price === 'low' ? 'selected' : ''; ?>>Low - High</option>
                        <option value="high" <?php echo $price === 'high' ? 'selected' : ''; ?>>High - Low</option>
                    </select>
                </form>
            </div>

            <div>
                <ul>
                    <li>
                        <span><a href="http://<?php echo htmlspecialchars($serverIP); ?>/php/filter_page.php?filter=top_rated">Top Rated</a></span>
                    </li>
                    <li>
                        <span> | </span>
                        <span><a href="http://<?php echo htmlspecialchars($serverIP); ?>/php/filter_page.php?filter=latest">Latest</a></span>
                    </li>
                    <li>
                        <span> | </span>
                        <span><a href="http://<?php echo htmlspecialchars($serverIP); ?>/php/filter_page.php?filter=top_sales">Top Sales</a></span>
                    </li>
                </ul>
            </div>
            <div class="book-container">
                <?php if (empty($books)): ?>
                    <p>
                        <?php 
                        // Check if searchQuery or sortQuery is set and display the relevant message
                        if (!empty($searchQuery)) {
                            echo 'No books found for "' . htmlspecialchars($searchQuery) . '".';
                        } elseif (!empty($sortQuery)) {
                            echo 'No books found for sorted by "' . htmlspecialchars($sortQuery) . '".';
                        } else {
                            echo 'No books found.';
                        }
                        ?>
                    </p>
                <?php else: ?>
                    <?php foreach ($books as $book): ?>
                        <div class="book-item">
                            <a href="http://<?php echo htmlspecialchars($serverIP); ?>/php/item_page.php?book_id=<?php echo $book['book_id']; ?>">
                                <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                <p><?php echo htmlspecialchars($book['title']); ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <footer class="footer">
        <p>© 2024 Pandieño Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>