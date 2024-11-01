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

    // Fetch the selected book details
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

    // Format average rating
    $averageRating = $book['average_rating'] ? number_format($book['average_rating'], 1) : 0;

    // Fetch related books based on category or author (excluding the current book)
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

    // Fetch other books that don't match the category or author, limit to 4
    $stmt_others = $pdo->prepare("
        SELECT book_id, title, cover_image 
        FROM books 
        WHERE (category != :category AND author != :author)
    ");
    // LIMIT 8  -- Limit the number of other books shown
    $stmt_others->bindParam(':category', $book['category'], PDO::PARAM_STR);
    $stmt_others->bindParam(':author', $book['author'], PDO::PARAM_STR);
    $stmt_others->execute();
    $otherBooks = $stmt_others->fetchAll(PDO::FETCH_ASSOC);
    
} else {
    // Redirect back to index.php if no book_id is provided
    header('Location: index.php');
    exit;
}

// Handle form submission to update the book
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id']);
    $title = $_POST['title'];
    $category = $_POST['category'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $description = $_POST['description'];
    $keywords = $_POST['keywords'];
    $publish_date = $_POST['publish_date'];

    // Get the current cover image from the database
    $stmt = $pdo->prepare("SELECT cover_image FROM books WHERE book_id = :book_id");
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();
    $current_book = $stmt->fetch(PDO::FETCH_ASSOC);
    $cover_image = $current_book['cover_image']; // Default to current image path

    // Handle the image upload
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        try {
            // Create a unique public_id using the book ID and current timestamp
            $timestamp = time();
            $public_id = 'book_' . $book_id . '_' . $timestamp; // Unique public_id
    
            // Upload the file to Cloudinary
            $uploadedFile = $cloudinary->uploadApi()->upload($_FILES['cover_image']['tmp_name'], [
                'folder' => 'pandieno_bookstore/cover_images', // Set a folder path in Cloudinary
                'public_id' => $public_id, // Set the unique identifier
                'overwrite' => true,
                'resource_type' => 'image'
            ]);
    
            // Update the cover image path to Cloudinary's URL
            $cover_image = $uploadedFile['secure_url'];
        } catch (Exception $e) {
            echo 'Image upload failed: ', $e->getMessage();
            exit();
        }
    }

    // Update the book details
    $stmt = $pdo->prepare("UPDATE books SET title = :title, category = :category, author = :author, isbn = :isbn, price = :price, quantity = :quantity, description = :description, keywords = :keywords, publish_date = :publish_date, cover_image = :cover_image WHERE book_id = :book_id");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':isbn', $isbn);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':keywords', $keywords);
    $stmt->bindParam(':publish_date', $publish_date);
    $stmt->bindParam(':cover_image', $cover_image); // Use the potentially updated cover image
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();

    header("Location: admin_dashboard.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?php echo htmlspecialchars($book['title']); ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" /><!--font IM Fell DW Pica SC-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" /><!--font inter -->
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet"><!--im fell eng-->
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
                        <form method="GET" action="http://localhost:3000/index.php">
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
          <a href="http://localhost:3000/about.html" class="view-shop">View Shop</a>
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
