<?php
session_start(); // Start the session

include '../db.php';

// Initialize variables
$email = '';

// Check if the session variable is set
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize the user input from the form
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $first_name = filter_var($_POST['first-name'], FILTER_SANITIZE_STRING);
    $middle_name = isset($_POST['middle-name']) ? filter_var($_POST['middle-name'], FILTER_SANITIZE_STRING) : NULL;
    $last_name = filter_var($_POST['last-name'], FILTER_SANITIZE_STRING);
    $gender = filter_var($_POST['gender'], FILTER_SANITIZE_STRING);
    $barangay = filter_var($_POST['barangay'], FILTER_SANITIZE_STRING);
    $house_no = filter_var($_POST['house_no'], FILTER_SANITIZE_STRING);
    $street = filter_var($_POST['street'], FILTER_SANITIZE_STRING);
    $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
    $province = filter_var($_POST['province'], FILTER_SANITIZE_STRING);
    $birthday = $_POST['birthday'];

    // Validate and sanitize birthday input
    $date_format = 'Y-m-d';
    $d = DateTime::createFromFormat($date_format, $birthday);
    if ($d && $d->format($date_format) === $birthday) {
        $birthday = $d->format($date_format);
    } else {
        // Handle invalid date format if necessary
        $birthday = null;
    }

    // Check if password is set in session before using it
    $password = isset($_SESSION['password']) ? $_SESSION['password'] : null;
    $hashed_password = $password ? password_hash($password, PASSWORD_DEFAULT) : null;

    // Step 2: Insert the data into the user table
    $sql = "INSERT INTO users (username, first_name, middle_name, last_name, email, password, barangay, house_no, street, city, province, birthday, gender, status, date_created)
            VALUES (:username, :first_name, :middle_name, :last_name, :email, :password, :barangay, :house_no, :street, :city, :province, :birthday, :gender, 'active', current_timestamp())";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':middle_name', $middle_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':barangay', $barangay);
    $stmt->bindParam(':house_no', $house_no);
    $stmt->bindParam(':street', $street);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':province', $province);
    $stmt->bindParam(':birthday', $birthday);

    // Execute the user insertion
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $pdo->lastInsertId(); // Store user ID in session
        $_SESSION['username'] = $username; // Store username in session

        // Step 1: Insert a new cart entry for the user
        $cart_sql = "INSERT INTO carts (user_id) VALUES (:user_id)";
        $cart_stmt = $pdo->prepare($cart_sql);
        $cart_stmt->bindParam(':user_id', $_SESSION['user_id']);

        // Execute cart insertion
        if ($cart_stmt->execute()) {
            $cart_id = $pdo->lastInsertId(); // Get the newly created cart ID
            
            // Update the user's cart_id    
            $update_user_sql = "UPDATE users SET cart_id = :cart_id WHERE user_id = :user_id";
            $update_user_stmt = $pdo->prepare($update_user_sql);
            $update_user_stmt->bindParam(':cart_id', $cart_id);
            $update_user_stmt->bindParam(':user_id', $_SESSION['user_id']);

            // Execute user update
            if ($update_user_stmt->execute()) {
                // Redirect to the item selection page after successful signup
                header("Location: http://localhost:3000/index.php");
                exit();  
            } else {
                echo "Error updating user cart ID: " . $update_user_stmt->errorInfo()[2];
                exit(); // Stop further execution if cart creation fails
            }
        } else {
            echo "Error creating cart: " . $cart_stmt->errorInfo()[2];
            exit(); // Stop further execution if cart creation fails
        }
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign up</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="https://res.cloudinary.com/dvr0evn7t/image/upload/v1728904749/logo_vccjhc.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=IM+Fell+DW+Pica+SC&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=IM+FELL+English&display=swap" rel="stylesheet"><!--im fell eng-->
    <link rel="stylesheet" href="http://localhost:3000/css/main.css"/>
    <link rel="stylesheet" href="http://localhost:3000/css/usersignup.css"/>
    <script type="module" src="http://localhost:3000/script/script.js"></script>
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
                <li><a href="http://localhost:3000/php/login.php">Log In</a></li>
            </ul>
        </div>

        </nav>

        <main>
            <section id="signup">
                <h2 id="signup">Sign up</h2>
                <form action="usersignup.php" method="post">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="first-name">First Name:</label>
                        <input type="text" id="first-name" name="first-name" required>
                    </div>

                    <div class="form-group">
                        <label for="middle-name">Middle Name:</label>
                        <input type="text" id="middle-name" name="middle-name">
                    </div>

                    <div class="form-group">
                        <label for="last-name">Last Name:</label>
                        <input type="text" id="last-name" name="last-name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" value="<?php echo isset($_SESSION['password']) ? $_SESSION['password'] : ''; ?>" readonly>
                    </div>

                    <div class="form-group" id="showpassword">
                        <input type="checkbox" onclick="showPassword()">Show Password
                    </div>

                    <div class="form-group">
                        <label>Gender:</label>
                        <div>
                            <label>
                                <input type="radio" id="male" name="gender" value="male" required>
                                Male
                            </label>
                            <label>
                                <input type="radio" id="female" name="gender" value="female">
                                Female
                            </label>
                            <label>
                                <input type="radio" id="other" name="gender" value="other">
                                Other
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="barangay">Barangay:</label>
                        <input type="text" id="barangay" name="barangay" required>
                    </div>

                    <div class="form-group">
                        <label for="house_no">House No.:</label>
                        <input type="text" id="house_no" name="house_no" required>
                    </div>

                    <div class="form-group">
                        <label for="street">Street:</label>
                        <input type="text" id="street" name="street" required>
                    </div>

                    <div class="form-group">
                        <label for="city">City:</label>
                        <select id="city" name="city" required>
                            <option>Pandi</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="province">Province:</label>
                        <select id="province" name="province" required>
                            <option>Bulacan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="birthday">Birthday:</label>
                        <input type="date" id="birthday" name="birthday" required>
                    </div>

                    <input id="create-account-button" type="submit" value="Create Account">
                </form>
            </section>
        </main>
    </body>
    <footer>
        <p>© 2024 Pandieño Bookstore. All Rights Reserved.</p>
    </footer>
</body>
</html>
