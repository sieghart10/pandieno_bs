<?php
session_start();
include '../db.php';

try {
    $stmt = $pdo->query("SELECT * FROM books");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Pandieño Bookstore</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" />
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/admin.css" />
    <!-- <script type="module" src="script.js"></script> -->
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
                    <li><h3>|&nbsp&nbspInventory</h3></li>
                </ul>
            </div>
            <div class="right-nav">
                <ul>
                    <li>
                        <a href="http://localhost:3000/php/admin_logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <section class="inventory-section">
            <h2>BOOK INVENTORY</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Cover Image</th>
                        <th>Description</th>
                        <th>Keywords</th>
                        <th>Publish Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($result)) { ?>
                        <tr><td colspan="11">No books found.</td></tr>
                    <?php } else { ?>
                        <?php foreach ($result as $row) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td><?php echo htmlspecialchars($row['author']); ?></td>
                                <td><?php echo htmlspecialchars($row['isbn']); ?></td>
                                <td><?php echo '₱' . number_format($row['price'], 2); ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><img src="<?php echo $row['cover_image']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" width="50"></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['keywords']); ?></td>
                                <td><?php echo $row['publish_date']; ?></td>
                                <td>
                                    <form action="edit_book.php" method="get" style="display:inline;">
                                        <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
                                        <button class="edit_button" type="submit">Edit</button>
                                    </form>

                                    <form action="delete_book.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                        <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
                                        <button class="delete_button" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
            <br>
            <form action="add_book.php" method="post" enctype="multipart/form-data">
                <!-- <br><input type="text" name="title" placeholder="Title" required>
                <input type="text" name="category" placeholder="Category" required>
                <input type="text" name="author" placeholder="Author" required>
                <input type="text" name="isbn" placeholder="ISBN" required>
                <input type="number" step="0.01" name="price" placeholder="Price" required>
                <input type="number" name="quantity" placeholder="Quantity" required>
                <input type="file" name="cover_image" accept="image/*" required>
                <textarea name="description" placeholder="Description"></textarea>
                <input type="text" name="keywords" placeholder="Keywords">
                <input type="date" name="publish_date" placeholder="Publish Date">-->
                <button class="add_button" type="submit">Add Book</button>
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Pandieño Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
