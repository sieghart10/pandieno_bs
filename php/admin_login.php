<?php
session_start();
require_once '../db.php';
$db = getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM admins WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        // Set session variables
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['status'] = 'active';

        header('Location: http://localhost:3000/php/admin_dashboard.php');
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pandieño Bookstore | Log In Admin</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width" />
    <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
    <link rel="stylesheet" href="http://localhost:3000/css/main.css" />
    <link rel="stylesheet" href="http://localhost:3000/css/login.css" />
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+DW+Pica+SC&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap">
    <script type="module" src="../script/script.js" defer></script>
</head>
<body>
    <nav>
        <div class="nav-container">
            <div class="left-nav">
                <ul>
                    <li><img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1727950923/edcd0988-0c42-466e-b5fd-76660fe9afeb_ktknm8-removebg-preview_mmikc5.png" alt="logo"></li>
                    <li><a href="http://localhost:3000/index.php"><h2>Pandieño Bookstore</h2></a></li>
                    <li><h3>|&nbsp Log In</h3></li>
                </ul>
            </div>
            <div class="right-nav">
                <ul>
                    <li><a href="http://localhost:3000/php/login.php">Log in (User)</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <section>
            <div class="left">
                <div class="logo-container">
                    <img src="https://res.cloudinary.com/dvr0evn7t/image/upload/v1727950923/edcd0988-0c42-466e-b5fd-76660fe9afeb_ktknm8-removebg-preview_mmikc5.png" alt="logo" />
                </div>
            </div>
            <div class="right">
                <div class="right-container">
                    <h2 id="sign-up">Log In</h2>
                    <?php
                    if (isset($error)) {
                        echo "<p style='color:red;'>$error</p>";
                    }
                    ?>
                    <form action="admin_login.php" method="POST">
                        <label class="rc-label" for="username">Username</label><br>
                        <input type="text" id="username" name="username" required><br><br>
                        <label class="rc-label" for="password">Password</label><br>
                        <input type="password" id="password" name="password" required><br><br>
                        <input type="checkbox" onclick="showPassword()">Show Password<br><br>
                        <input type="submit" value="Log in" class="log_in_btn"><br><br>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <p>© 2024 Pandieno Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
