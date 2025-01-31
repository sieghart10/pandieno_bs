<?php
session_start();
include '../db.php';

$currentUser = null;
$cartItemCount = 0;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

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

if (isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']);

    $stmt = $pdo->prepare("
    SELECT 
        title, author, category, publish_date, price, description, quantity, cover_image,
        (SELECT AVG(rating) FROM reviews WHERE book_id = :book_id) AS average_rating
    FROM 
        books 
    WHERE 
        book_id = :book_id
    ");
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    $averageRating = $book['average_rating'] ? number_format($book['average_rating'], 1) : 0;

    $stmt_related = $pdo->prepare("
        SELECT book_id, title, cover_image 
        FROM books 
        WHERE (category = :category OR author = :author) 
        AND book_id != :book_id
        LIMIT 4  -- Limit the number of related books shown
    ");
    $stmt_related->bindParam(':category', $book['category'], PDO::PARAM_STR);
    $stmt_related->bindParam(':author', $book['author'], PDO::PARAM_STR);
    $stmt_related->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt_related->execute();
    $relatedBooks = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

    $stmt_others = $pdo->prepare("
        SELECT book_id, title, cover_image 
        FROM books 
        WHERE (category != :category AND author != :author)
    ");
    $stmt_others->bindParam(':category', $book['category'], PDO::PARAM_STR);
    $stmt_others->bindParam(':author', $book['author'], PDO::PARAM_STR);
    $stmt_others->execute();
    $otherBooks = $stmt_others->fetchAll(PDO::FETCH_ASSOC);
} else {
    header('Location: http://localhost:3000/index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo htmlspecialchars($book['title']); ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="http://localhost:3000/css/main.css" />
    <link rel="stylesheet" href="http://localhost:3000/css/item_page.css" />
    <script src="http://localhost:3000/scripts/addToCart.js" defer></script>
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
                        <form method="GET" action="http://localhost:3000/index.php">
                            <input type="text" name="search" placeholder="Search item..." class="search-bar">
                            <button type="submit">Search</button>
                        </form>
                    </li>
                    <li class="cart">
                        <a href="http://localhost:3000/php/shoppingcart.php">
                            <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728372447/shopping-cart_1_v3hyar.png" alt="cart">
                            <span class="cart-count" style="display: none;"></span> <!-- Initially hidden -->
                        </a>
                    </li>
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
      <section class="book-detail">
        <div class="book-image">
          <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
        </div>
        <div class="book-info">
          <h2><?php echo htmlspecialchars($book['title']); ?></h2>
          <p class="author">By <?php echo htmlspecialchars($book['author']); ?></p>
          <p class="publish-date">Publication Date: <?php echo htmlspecialchars($book['publish_date']); ?></p>
          <p class="price">₱ <?php echo htmlspecialchars($book['price']); ?></p>
          <p class="description"><?php echo htmlspecialchars($book['description']); ?></p>
          <div class="quantity">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" value="1" min="1" max="<?php echo htmlspecialchars($book['quantity']); ?>">
            <span><?php echo htmlspecialchars($book['quantity']); ?> pieces available</span>
          </div>
          <div class="buttons">
            <?php if ($book['quantity'] > 0): ?>
                <button onclick="addToCart(<?php echo $book_id; ?>)">Add to Cart</button>
                <button onclick="redirectToCheckout(<?php echo $book_id; ?>)">Buy Now</button>
            <?php else: ?>
                <button disabled style="cursor: not-allowed; background-color: gray;">Out of Stock</button>
            <?php endif; ?>
          </div>
          <div class="ratings">
              <p>Ratings: <?php echo htmlspecialchars($averageRating); ?> / 5 ★</p>
              <?php if (isset($_SESSION['user_id'])): ?>
                  <a href="http://localhost:3000/php/review_page.php?book_id=<?php echo $book_id; ?>">Reviews</a>
              <?php else: ?>
                  <a href="http://localhost:3000/php/login.php">Reviews</a>
              <?php endif; ?>
          </div>
        </div>
      </section>
      <section class="bookstore-info">
        <div class="store-logo">
          <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1727950923/edcd0988-0c42-466e-b5fd-76660fe9afeb_ktknm8-removebg-preview_mmikc5.png" alt="Pandieño Bookstore Logo">
        </div>
        <div class="store-details">
          <h3>Pandieño Bookstore</h3>
          <a href="http://localhost:3000/html/about.html" class="view-shop">View Shop</a>
        </div>
      </section>

      <!-- Related Book Section -->
      <section class="related-books">
        <h3>Related Books</h3>
        <div class="book-container">
            <?php if (empty($relatedBooks)): ?>
                <p>No related books found.</p>
            <?php else: ?>
                <?php foreach ($relatedBooks as $related): ?>
                    <div class="book-item">
                        <a href="http://localhost:3000/php/item_page.php?book_id=<?php echo $related['book_id']; ?>">
                            <img src="<?php echo htmlspecialchars($related['cover_image']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                            <p><?php echo htmlspecialchars($related['title']); ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Other Books Section -->
    <section class="other-books">
        <h3>More Books</h3>
        <div class="book-container">
            <?php if (empty($otherBooks)): ?>
                <p>No other books found.</p>
            <?php else: ?>
                <?php foreach ($otherBooks as $other): ?>
                    <div class="book-item">
                        <a href="http://localhost:3000/php/item_page.php?book_id=<?php echo $other['book_id']; ?>">
                            <img src="<?php echo htmlspecialchars($other['cover_image']); ?>" alt="<?php echo htmlspecialchars($other['title']); ?>">
                            <p><?php echo htmlspecialchars($other['title']); ?></p>
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