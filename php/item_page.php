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

// Get the book_id from the URL
if (isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']);

    // Fetch the book details from the database
    $stmt = $pdo->prepare("SELECT title, author, category, publish_date, price, description, quantity, cover_image FROM books WHERE book_id = :book_id");
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch related books based on category or author (excluding the current book)
    $stmt_related = $pdo->prepare("
        SELECT book_id, title, cover_image 
        FROM books 
        WHERE (category = :category OR author = :author) 
        AND book_id != :book_id
    ");
    $stmt_related->bindParam(':category', $book['category'], PDO::PARAM_STR);
    $stmt_related->bindParam(':author', $book['author'], PDO::PARAM_STR);
    $stmt_related->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt_related->execute();
    $relatedBooks = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

    // Fetch other books that don't match the category or author
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
    // Redirect back to index.php if no book_id is provided
    header('Location: index.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?php echo htmlspecialchars($book['title']); ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="http://localhost:3000/css/main.css" />
    <link rel="stylesheet" href="http://localhost:3000/css/item_page.css" />
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
                        <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728372447/shopping-cart_1_v3hyar.png" alt="cart">
                        <span class="cart-count">2</span>
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
      <section class="book-detail">
        <div class="book-image">
          <!-- Display the book cover image -->
          <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
        </div>
        <div class="book-info">
          <!-- Display the book title, author, and other details -->
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
            <button>Add to Cart</button>
            <a href="checkout.html"><button>Buy Now</button></a>
          </div>
          <div class="ratings">
            <p>Ratings: 5/5 ★</p>
            <a href="#">Reviews</a>
          </div>
        </div>
      </section>
      <section class="bookstore-info">
        <div class="store-logo">
          <img src="logo.png" alt="Pandieño Bookstore Logo">
        </div>
        <div class="store-details">
          <h3>Pandieño Bookstore</h3>
          <a href="about.html" class="view-shop">View Shop</a>
        </div>
      </section>
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
        <h3>Other Books</h3>
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
