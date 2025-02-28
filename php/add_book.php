<?php
session_start();
include '../db.php';
include '../cloudinary_config.php';

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Ignore comments
        list($key, $value) = explode('=', $line, 2);
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

$serverIP = $_ENV['SERVER_IP'] ?? '127.0.0.1';

$pdo = getReadConnection();

$stmt = $pdo->prepare("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_NAME = 'books' AND COLUMN_NAME = 'category'");
$stmt->execute();
$column_type = $stmt->fetchColumn();

$categories = [];
if ($column_type) {
    $enum_values = str_replace("enum(", "", $column_type);
    $enum_values = str_replace(")", "", $enum_values);
    $categories = explode(",", $enum_values);
    $categories = array_map(function($value) {
        return trim($value, "'");
    }, $categories);
}

$title = '';
$category = '';
$author = '';
$isbn = '';
$price = '';
$quantity = '';
$description = '';
$keywords = '';
$publish_date = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $title = $_POST['title'];
    $category = strtolower($_POST['category']);
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'] ?? null;
    $keywords = $_POST['keywords'] ?? null;
    $publish_date = $_POST['publish_date'] ?? null;

    $isbn_check_stmt = $pdo->prepare("SELECT COUNT(*) FROM books WHERE isbn = ?");
    $isbn_check_stmt->execute([$isbn]);
    $isbn_exists = $isbn_check_stmt->fetchColumn();

    if ($isbn_exists > 0) {
        $error_message = "Error: A book with this ISBN already exists.";
    } else {
        if (!empty($_FILES['cover_image']['tmp_name'])) {
            try {
                $folder_path = 'pandieno_bookstore/cover_images/';
                $upload = $cloudinary->uploadApi()->upload($_FILES['cover_image']['tmp_name'], [
                    'folder' => $folder_path,
                ]);
                $cover_image_url = $upload['secure_url'];

                $stmt = $pdo->prepare("
                    INSERT INTO books (
                        title, 
                        category, 
                        author, 
                        isbn, 
                        price, 
                        quantity, 
                        cover_image, 
                        description, 
                        keywords, 
                        publish_date, 
                        avg_rating
                        ) VALUES (
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                        )
                    ");
                $stmt->execute([
                    $title, 
                    $category, 
                    $author, 
                    $isbn, 
                    $price, 
                    $quantity, 
                    $cover_image_url, 
                    $description, 
                    $keywords, 
                    $publish_date, 
                    0
                ]);

                $_SESSION['success_message'] = "Book added successfully!";
                header("Location: admin_dashboard.php");
                exit();
            } catch (Exception $e) {
                $error_message = "Error uploading cover image or adding book: " . $e->getMessage();
            }
        } else {
            $error_message = "Error: Cover image is required.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Book</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="http://<?php echo $serverIP; ?>/pandieno_bookstore/css/main.css" />
    <link rel="stylesheet" type="text/css" href="http://<?php echo $serverIP; ?>/pandieno_bookstore/css/edit_book.css" />
    <script src="http://<?php echo $serverIP; ?>/pandieno_bookstore/scripts/previewImage.js" defer></script>
</head>
<body>
<nav>
    <div class="nav-container">
            <div class="left-nav">
                <ul>
                    <li>
                    <a href="http://<?php echo $serverIP; ?>/pandieno_bookstore/index.php">
                        <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1727950923/edcd0988-0c42-466e-b5fd-76660fe9afeb_ktknm8-removebg-preview_mmikc5.png" alt="logo">
                    </a>
                    </li>
                    <li>
                        <a href="http://<?php echo $serverIP; ?>/pandieno_bookstore/index.php"><h2>Pandieño Bookstore</h2></a>
                    </li>
                    <li><h3>|&nbsp&nbspInventory</h3></li>
                </ul>
            </div>
            <div class="right-nav">
                <ul>
                    <li>
                        <a href="http://<?php echo $serverIP; ?>/pandieno_bookstore/php/admin_dashboard.php">Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <section class="inventory-section">
        <h2>Add New Book</h2>
            <form action="add_book.php" method="post" enctype="multipart/form-data">
                <?php if ($error_message): ?>
                    <label style="color: red; padding-left: 42%;"><?= htmlspecialchars($error_message) ?></label>
                    <div style="height: 20px;"></div>
                <?php endif; ?>
                <table>
                    <thead>
                        <tr>
                            <th> <label for="title">Title:</label></th>
                            <td><input type="text" name="title" id="title" value="<?= htmlspecialchars($title) ?>" required><br></td>
                        </tr>
                        <tr>
                            <th><label for="category">Category:</label></th>
                            <td>
                                <select name="category" id="category" required>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>" <?= $cat === $category ? 'selected' : '' ?>>
                                            <?php echo htmlspecialchars(ucfirst($cat)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="author">Author:</label></th>
                            <td><input type="text" name="author" id="author" value="<?= htmlspecialchars($author) ?>" required><br></td>
                        </tr>
                        <tr>
                            <th><label for="isbn">ISBN:</label></th>
                            <td><input type="text" name="isbn" id="isbn" value="<?= htmlspecialchars($isbn) ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="price">Price:</label></th>
                            <td><input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($price) ?>" required><br></td>
                        </tr>
                        <tr>
                            <th><label for="quantity">Quantity:</label></th>
                            <td><input type="number" name="quantity" id="quantity" value="<?= htmlspecialchars($quantity) ?>" required><br></td>
                        </tr>
                        <tr>
                            <th><label for="cover_image">Cover Image:</label></th>
                            <td>
                                <input type="file" name="cover_image" id="cover_image" accept="image/*" required onchange="previewImage(event)"><br>
                                <img id="cover-image-preview" src="#" alt="Cover Image Preview" width="100" style="display: none;">
                            </td>
                        </tr>
                        <tr>
                            <th><label for="description">Description:</label></th>
                            <td><textarea name="description" id="description"><?= htmlspecialchars($description) ?></textarea><br></td>
                        </tr>
                        <tr>
                            <th><label for="keywords">Keywords:</label></th>
                            <td><input type="text" name="keywords" id="keywords" value="<?= htmlspecialchars($keywords) ?>"><br></td>
                        </tr>
                        <tr>
                            <th><label for="publish_date">Publish Date:</label></th>
                            <td><input type="date" name="publish_date" id="publish_date" value="<?= htmlspecialchars($publish_date) ?>"><br></td>
                        </tr>
                    </thead>
                </table>
                <div class="form-buttons">
                    <button type="submit" name="submit">Add Book</button>
                </div>  
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Pandieño Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
