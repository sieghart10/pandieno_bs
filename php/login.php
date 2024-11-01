<?php
// Include database connection
require_once '../db.php';

// Start session
session_start();

// Check if the user is already logged in
if (isset($_SESSION['email'])) {
    // Fetch the user from the database using the stored email
    $email = $_SESSION['email'];
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if the user's status is active
    if ($user && $user['status'] === 'active') {
        header("Location: http://localhost:3000/index.php");
        exit; // Ensure no further code is executed
    }
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Fetch the user from the database using the provided email
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists and the password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, log in the user
        $_SESSION['user_id'] = $user['user_id']; // Store user ID in session
        $_SESSION['email'] = $user['email']; // Store email in session
        $_SESSION['username'] = $username;

        // Update the user's status to active
        $sql_update_status = "UPDATE users SET status = 'active' WHERE user_id = :user_id";
        $stmt_update = $pdo->prepare($sql_update_status);
        $stmt_update->bindParam(':user_id', $user['user_id']);
        $stmt_update->execute();

        // Redirect to the logged-in page
        header("Location: http://localhost:3000/index.php");
        exit;
    } else {
        // Invalid credentials, display error
        $error = "Invalid email or password.";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
    <head>
    <title>Pandieño Bookstore | Log In</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width" />
    <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
    <link rel="stylesheet" href="http://localhost:3000/css/main.css" />
    <link rel="stylesheet" href="http://localhost:3000/css/login.css" />
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet"><!--im fell eng-->
    <script type="module" src="http://localhost:3000/script/script.js" defer></script>
</head>
    <body>
    <nav>
        <div class="nav-container">
        <div class="left-nav">
            <ul>
            <li>
                <img
                src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1727950923/edcd0988-0c42-466e-b5fd-76660fe9afeb_ktknm8-removebg-preview_mmikc5.png"
                alt="logo"
                />
            </li>
            <li><a href="http://localhost:3000/index.php"><h2>Pandieño Bookstore</h2></a></li>
            <li><h3>|&nbsp Log In</h3></li>
            </ul>
        </div>
        <div class="right-nav">
            <ul>
            <li><a href="http://localhost:3000/php/signup.php">Sign up</a></li>
            </ul>
        </div>
        </div>
    </nav>

    <main>
        <section>
        <div class="left">
        <div class="logo-container">
              <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1727950923/edcd0988-0c42-466e-b5fd-76660fe9afeb_ktknm8-removebg-preview_mmikc5.png"alt="logo"/>
            </div>
        </div>
        <div class="right">
            <div class="right-container">
                <h2 id="sign-up">Log In</h2>
                <form action="http://localhost:3000/php/login.php" method="POST">
                    <label class= "rc-label" for="email">Email</label><br>
                    <input type="email" id="email" name="email" required><br><br>
                    <label class= "rc-label" for="password">Password</label><br>
                    <input type="password" id="password" name="password" required><br><br>
                    
                    <input type="checkbox" onclick="showPassword()">Show Password<br><br>
                    
                    <input type="submit" value="Log in" class="log_in_btn">
                </form>
                <a href="http://localhost:3000/php/admin_login.php"><p>Log in as Admin</p></a>
            </div>
        </div>
        </div>
        </section>
    </main>
    </body>
    <footer>
    <p>© 2024 Pandieno Bookstore. All Rights Reserved.</p>
    </footer>
</html>