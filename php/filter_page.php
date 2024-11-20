 <!-- filter_page.php -->
 <?php
session_start();
include_once '../db.php'; // Database connection

// Retrieve the search, category, and filter parameters
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'top_rated'; // Default filter
$filterTitle = ucfirst(str_replace('_', ' ', $filter));

// Initialize books as an empty array
$books = [];

try {
    // Base query with optional filters
    $query = "SELECT * FROM books WHERE 1=1";
    $params = [];

    // Add search condition if search is provided
    if (!empty($searchQuery)) {
        $query .= " AND (title LIKE :search OR author LIKE :search OR keywords LIKE :search)";
        $params[':search'] = '%' . $searchQuery . '%';
    }

    // Add category filter if category is provided
    if (!empty($category)) {
        $query .= " AND category = :category";
        $params[':category'] = $category;
    }

    // Add additional filters (e.g., latest, top_sales, etc.)
    switch ($filter) {
        case 'latest':
            $query .= " ORDER BY publish_date DESC";
            break;
        case 'top_sales':
            $query .= " ORDER BY sales_count DESC";
            break;
        case 'top_rated':
            $query .= " AND avg_rating IS NOT NULL ORDER BY avg_rating DESC";
            break;
        default:
            $query .= " ORDER BY publish_date DESC"; // Default to latest
    }

    // Prepare, execute, and fetch results
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error fetching books: " . $e->getMessage();
}

// Fetch current user and cart details
if (isset($_SESSION['user_id'])) {
    // Query the database to get the user's data
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch cart details if the user is logged in
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart_items ci JOIN carts c ON ci.cart_id = c.cart_id WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cartItemCount = $result['count'];
} else {
    // If the user is not logged in, set $currentUser to null and $cartItemCount to 0
    $currentUser = null;
    $cartItemCount = 0;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pandieño Bookstore</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet"/><!--font IM Fell DW Pica SC-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" /><!--font inter -->
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet"><!--im fell eng-->
    <link rel="stylesheet" href="http://localhost:3000/css/filter_page.css" />
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" />
    <script type="module" src="http://localhost:3000/script/script.js" defer></script>
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
                    <form method="GET" action="filter_page.php">
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>" />
                        <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search item..." class="search-bar">
                        <!-- <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>" /> -->
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
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="http://localhost:3000/php/login.php">Log in</a> | <a href="http://localhost:3000/php/signup.php">Sign up</a>
                        <?php else: ?>
                            <div class="username-profile">
                                <span><?php echo htmlspecialchars($currentUser['username']); ?></span>
                                <a href="profile.php">
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
      <section>
        <div class="left-container">
          <div class="filter-name">
            <h1><?php echo $filterTitle; ?></h1>
          </div>
          <div class="sort-by">
            <h3>Sort by</h3>
            <ul>
                <li><a href="filter_page.php?filter=top_rated">Top Rated</a></li>
                <li><a href="filter_page.php?filter=latest">Latest</a></li>
                <li><a href="filter_page.php?filter=top_sales">Top Sales</a></li>
            </ul>
          </div>
          <div class="categories">
                <ul>
                    <li>Category</li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=fantasy">Fantasy</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=thriller">Thriller</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=mystery">Mystery</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=horror">Horror</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=romance">Romance</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=action">Action</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=comedy">Comedy</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=drama">Drama</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=sci-fi">Sci-Fi</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=historical">Historical</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=biography">Biography</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=self-help">Self-Help</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=children">Children</a></li>
                    <li><a href="filter_page.php?filter=<?php echo htmlspecialchars($filter); ?>&category=young-adult">Young Adult</a></li>
                </ul>
          </div>
        </div>

        <div class="right-container">
            <div class="book-list">
            <?php 
            // Check if the books array is empty
            if (empty($books)): 
                // Check if search or category is set and display a relevant message
                if (!empty($searchQuery)) {
                    echo "<p>No books matched for <strong>" . htmlspecialchars($searchQuery) . "</strong>.</p>";
                } elseif (!empty($category)) {
                    echo "<p>No books available for <strong>" . htmlspecialchars(ucfirst($category)) . "</strong> category yet.</p>";
                } else {
                    echo "<p>No books available at the moment. Please check back later.</p>";
                }
            else: 
                // Display books if available
                foreach ($books as $book): ?>
                    <a href="item_page.php?book_id=<?php echo $book['book_id']; ?>">
                        <div class="book-item">
                            <img src="<?php echo $book['cover_image']; ?>" alt="Book cover">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
        </div>
      </section>
    </main>
</body>
<!-- <footer>
  <p>© 2024 Pandieno Bookstore. All Rights Reserved .</p>
</footer> -->
</html>