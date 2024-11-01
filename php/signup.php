<?php
session_start();
require '../db.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input values
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm-password']));

    // Validate input
    if ($password === $confirm_password) {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            echo "Email already in use.";
            // Redisplay the form
            displayForm();
        } else {
            // Proceed with storing the user data
            session_regenerate_id(true);
            $_SESSION['email'] = $email; // Store email in session
            $_SESSION['password'] = $password; 
            
            // Hash the password for security

            header("Location: http://localhost:3000/php/usersignup.php");
            exit; // Always use exit after header redirection
        }
    } else {
        echo "Passwords do not match.";
        // Redisplay the form
        displayForm();
    }
} else {
    // Display the form when the request method is GET
    displayForm();
}

function displayForm() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
      <head>
        <title>Pandieño Bookstore | Sign up</title>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width" />
        <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
        <link rel="stylesheet" href="http://localhost:3000/css/main.css" />
        <link rel="stylesheet" href="http://localhost:3000/css/signup.css" />
        <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
        <script type="module" src="../script/script.js" defer></script>
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
                <li><h3>|&nbsp Sign Up</h3></li>
              </ul>
            </div>
            <div class="right-nav">
              <ul>
                <li id="search-bar">
                  <input
                    type="text"
                    placeholder="Search item..."
                    class="search-bar"
                  />
                </li>
                <li><a href="http://localhost:3000/php/login.php">Log In</a></li>
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
                <h2 id="sign-up">Sign up</h2>
                <form action="http://localhost:3000/php/signup.php" method="POST">
    
                  <label for="email">Email</label><br>
                  <input type="email" id="email" name="email" required><br><br>
                 
                  <label for="password">Password</label><br>
                  <input type="password" id="password" name="password" required><br><br>             
                
                  <label for="confirm-password">Confirm Password</label><br>
                  <input type="password" id="confirm-password" name="confirm-password" required><br>
                  <input type="checkbox" onclick="showPassword()">Show Password<br><br><br>
                  <input type="submit" value="Sign up" class="sign_up_btn">
              </form>
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
    <?php
}
?>