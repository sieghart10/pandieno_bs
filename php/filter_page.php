<?php
// Include database connection
include_once '../db.php'; // Ensure db.php has a PDO setup
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'top_rated';
$filterTitle = ucfirst(str_replace('_', ' ', $filter)); // Converts 'top_rated' to 'Top Rated', 'top_sales' to 'Top Sales'

// Initialize $books as an empty array to avoid undefined variable issues
$books = [];

try {
    // Filtering logic based on the `books` table fields
    switch ($filter) {
        case 'latest':
            $query = "SELECT * FROM books ORDER BY publish_date DESC";
            break;
        case 'top_sales':
            $query = "SELECT * FROM books ORDER BY sales_count DESC";
            break;
        case 'top_rated':
            $query = "SELECT * FROM books WHERE avg_rating IS NOT NULL ORDER BY avg_rating DESC";
            break;
        default:
            $query = "SELECT * FROM books";
    }
    
    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    // Fetch the results into $books array
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Error fetching books: " . $e->getMessage();
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
                  <input type="text" placeholder="Search item..." class="search-bar">
                </li>
                <li class="cart">
                  <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728372447/shopping-cart_1_v3hyar.png" alt="cart">
                  <span class="cart-count">2</span>
                  <a href="http://localhost:3000/php/login.php">Log in</a> | <a href="http://localhost:3000/php/signup.php">Sign up</a>
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
          <div class="categories">
            <ul>
              <li>Fantasy</li>
              <li>Triller</li>
              <li>Fictiom</li>
              <li>Horror</li>
              <li>Romance</li>
              <li>Mystery</li>
              <li>Nonfiction</li>
              <li>Poetry</li>
              <li>Novel</li>
              <li>Adventure</li>
            </ul>
          </div>
        </div>
        <div class="right-container">
          <div class="dropdown">
            <span id="sort_color">Sort by:</span>
            <select>
              <option>Name</option>
              <option>A - Z</option>
              <option>Z - A</option>
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
          <div class="book-list">
              <?php foreach ($books as $book): ?>
                  <!-- Wrap each book item in a link to item_page.php -->
                  <a href="http://localhost:3000/php/item_page.php?book_id=<?php echo $book['book_id']; ?>" class="book-link">
                      <div class="book-item">
                          <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover of <?php echo htmlspecialchars($book['title']); ?>" class="book-cover" />
                          <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                          <p>Author: <?php echo htmlspecialchars($book['author']); ?></p>
                          <p>Price: <?php echo htmlspecialchars($book['price']); ?></p>
                      </div>
                  </a>
              <?php endforeach; ?>
          </div>
      </section>
    </main>
  </body>
  <footer>
    <p>© 2024 Pandieno Bookstore. All Rights Reserved .</p>
  </footer>
</html>