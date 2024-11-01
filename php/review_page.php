<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if user is not logged in
    header('Location: login.php');
    exit;
}

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;

// Fetch reviews for the specific book
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE book_id = :book_id");
$stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reviews</title>
    <meta charset="UTF-8">
    <!-- Other head elements -->
</head>
<body>
    <h1>Reviews for Book ID: <?php echo htmlspecialchars($book_id); ?></h1>
    <div class="reviews">
        <?php if (empty($reviews)): ?>
            <p>No reviews found for this book.</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <p><strong><?php echo htmlspecialchars($review['username']); ?>:</strong> <?php echo htmlspecialchars($review['content']); ?></p>
                    <p>Rating: <?php echo htmlspecialchars($review['rating']); ?> / 5</p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
