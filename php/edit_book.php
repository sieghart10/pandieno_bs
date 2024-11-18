<?php
session_start();
include '../db.php'; // Database connection
include '../cloudinary_config.php';

// Get the ENUM values for categories
$stmt = $pdo->prepare("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_NAME = 'books' AND COLUMN_NAME = 'category'");
$stmt->execute();
$column_type = $stmt->fetchColumn();

if ($column_type) {
    $enum_values = str_replace("enum(", "", $column_type);
    $enum_values = str_replace(")", "", $enum_values);
    $categories = explode(",", $enum_values);
    $categories = array_map(function($value) {
        return trim($value, "'");
    }, $categories);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to update the book
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

    // Check if the ISBN already exists in the database (excluding the current book)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM books WHERE isbn = :isbn AND book_id != :book_id");
    $stmt->bindParam(':isbn', $isbn);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();
    $isbn_count = $stmt->fetchColumn();

    if ($isbn_count > 0) {
        // If ISBN is not unique, show an error message
        echo "<script>alert('The ISBN you entered already exists for another book. Please enter a unique ISBN.');window.location.href = 'edit_book.php?book_id=$book_id';</script>";
        exit();
    }
    // Fetch the current cover image
    $stmt = $pdo->prepare("SELECT cover_image FROM books WHERE book_id = :book_id");
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();
    $currentBook = $stmt->fetch(PDO::FETCH_ASSOC);

    // Default cover image path
    $cover_image = $currentBook['cover_image'] ?? null;

    // Handle the image upload
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        try {
            // Create a unique public_id using the book ID and current timestamp
            $timestamp = time();
            $public_id = 'book_' . $book_id . '_' . $timestamp; // Unique public_id
    
            // Upload the file to Cloudinary
            $uploadedFile = $cloudinary->uploadApi()->upload($_FILES['cover_image']['tmp_name'], [
                'folder' => 'pandieno_bookstore/cover_images',
                'public_id' => $public_id,
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

    // Update the book in the database
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
    $stmt->bindParam(':cover_image', $cover_image);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();

    header("Location: admin_dashboard.php");
    exit();
} else if (isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']);
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = :book_id");
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        header("Location: admin_dashboard.php");
        exit();
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Book</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" />
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/edit_book.css" />
    <script src="http://localhost:3000/scripts/previewImage.js" defer></script>
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
                        <a href="http://localhost:3000/php/admin_dashboard.php">Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <section class="inventory-section">
            <h2>Edit Book</h2>
            <form action="edit_book.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                <table>
                    <thead>
                        <tr>
                            <th><label for="title">Title</label></th>
                            <td><input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required><br></td>
                        </tr>
                        <tr>
                            <th><label for="category">Category</label></th>
                            <td>
                                <select name="category" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category); ?>" <?php echo ($book['category'] === $category) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars(ucfirst($category)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="author">Author</label></th>
                            <td><input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required><br></td>
                        </tr>
                        <tr>
                            <th><label for="isbn">ISBN</label></th>
                            <td><input type="text" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>" required><br></td>
                        </tr>
                        <tr>
                            <th><label for="price">Price</label></th>
                            <td><input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($book['price']); ?>" required><br></td>
                        </tr>
                        <tr>
                            <th><label for="quantity">Quantity</label></th>
                            <td><input type="number" name="quantity" value="<?php echo htmlspecialchars($book['quantity']); ?>" required><br></td>
                        </tr>
                        <tr>
                            <th><label for="description">Description</label></th>
                            <td><textarea name="description" required><?php echo htmlspecialchars($book['description']); ?></textarea><br></td>
                        </tr>
                        <tr>
                            <th><label for="cover_image">Cover Image</label></th>
                            <td>
                                <div class="book-input">
                                    <img id="cover-image-preview" src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover Image" width="100  onerror="handleImageError(this)">
                                    <div class="choose-file-container">
                                        <input id="file-upload-button" class="choose-file" type="file" name="cover_image" accept="image/*" onchange="previewImage(event)">
                                    </div>
                                    <button type="button" id="reset-image-button" onclick="resetImage()">Reset to Default</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="keywords">Keywords</label></th>
                            <td><input type="text" name="keywords" value="<?php echo htmlspecialchars($book['keywords']); ?>"><br></td>
                        </tr>
                        <tr>
                            <th><label for="publish_date">Publish Date</label></th>
                            <td><input type="date" name="publish_date" value="<?php echo htmlspecialchars($book['publish_date']); ?>" required><br></td>
                        </tr>
                    </thead>
                </table>

                <div class="form-buttons">
                    <button type="submit">Save Changes</button>
                    <a href="admin_dashboard.php" class="cancel-button">Cancel</a>
                </div>              
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Pandieño Bookstore. All Rights Reserved.</p>
    </footer>
    <script>
        let defaultImagePath = "<?php echo htmlspecialchars($book['cover_image']); ?>"; // Set default image path
    </script>
</body>
</html>
