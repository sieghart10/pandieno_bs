<?php
session_start(); // Start the session

include '../db.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: http://localhost:3000/php/login.php'); // Redirect to login if not logged in
    exit();
}

// Initialize variables
$email = $_SESSION['email']; // Get email from session

// Fetch user data from the database
$sql = "SELECT username, first_name, middle_name, last_name, email, gender, birthday FROM users WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" /> -->
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" /><!--font IM Fell DW Pica SC-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" /><!--font inter -->
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet"><!--im fell eng-->
    <link rel="stylesheet" type="text/css" href="http://localhost:3000/css/main.css" />
    <link rel="stylesheet" href="http://localhost:3000/css/profile.css" />
    <script type="module" src="script.js"></script>
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
                    <li><h3>|&nbsp&nbspProfile</h3></li>
                </ul>
            </div>
            <div class="right-nav">
                <ul>
                    <!-- <li>
                        <input type="text" placeholder="Search item..." class="search-bar">
                    </li> -->
                    <li class="cart">
                        <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728372447/shopping-cart_1_v3hyar.png" alt="cart">
                        <span class="cart-count">0</span>
                    </li>
                    <li>
                        <a href="http://localhost:3000/php/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <section class="profile-burger">
            <h3>My&nbspAccount</h3>
            <a href="#" class="change-address">Profile</a>
            <a href="#" class="change-address">Address</a>
        </section>
        <section class="profile-section">
            <div>
                <h1>My&nbspProfile</h1>
            </div>
            <div class="right-container">
                <div class="user-info">
                    <form>
                        <div class="form-container">
                                <p><strong>Username:</strong><?php echo htmlspecialchars($user['username']); ?></p>
                                <p><strong>Name:</strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']); ?></p>
                                <p><strong>Email:</strong><?php echo htmlspecialchars($user['email']); ?></p>
                                <p><strong>Gender:</strong><?php echo htmlspecialchars(ucfirst($user['gender'])); ?></p>
                                <!-- <div class="radio-group">
                                    <label>
                                        <input type="radio" name="option" value="1"> Female
                                    </label>
                                    <label>
                                        <input type="radio" name="option" value="2"> Male
                                    </label>
                                    <label>
                                        <input type="radio" name="option" value="3"> Other
                                    </label>
                                </div> -->
                                <p><strong>Date of Birth:</strong><?php echo htmlspecialchars(date('F j, Y', strtotime($user['birthday']))); ?></p>
                                <!-- <input type="date" id="date-of-birth" name="date-of-birth"><br><br>
                                <input type="submit" value="Save"> -->

                        </div>
                        <!-- <label for="image" class="file-label">Select Image</label>
                        <input type="file" id="image" name="image"> -->
                    </form>
                </div>
                <div class="image-section">
                    <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728921898/profile_evrssf.png" alt="Profile Image" /> 
                </div>
            </div>
            
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Pandiemo Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
