<?php
session_start();

include '../db.php';

$email = $_SESSION['email'] ?? '';
$password = $_SESSION['password'] ?? null;

if (!$email || !$password) {
    echo "Error: Email or password is missing from the session.";
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $first_name = htmlspecialchars($_POST['first-name']);
    $middle_name = !empty($_POST['middle-name']) ? htmlspecialchars($_POST['middle-name']) : null;
    $last_name = htmlspecialchars($_POST['last-name']);
    $gender = htmlspecialchars($_POST['gender']);
    $barangay = htmlspecialchars($_POST['barangay']);
    $house_no = htmlspecialchars($_POST['house_no']);
    $street = htmlspecialchars($_POST['street']);
    $city = htmlspecialchars($_POST['city']);
    $province = htmlspecialchars($_POST['province']);

    $birthday = $_POST['birthday'];
    $date_format = 'Y-m-d';
    $d = DateTime::createFromFormat($date_format, $birthday);
    $birthday = ($d && $d->format($date_format) === $birthday) ? $birthday : null;

    try {
        $pdo->beginTransaction();

        $address_sql = "INSERT INTO addresses (barangay, house_no, street, city, province)
                        VALUES (:barangay, :house_no, :street, :city, :province)";
        $address_stmt = $pdo->prepare($address_sql);
        $address_stmt->execute([
            ':barangay' => $barangay,
            ':house_no' => $house_no,
            ':street' => $street,
            ':city' => $city,
            ':province' => $province
        ]);

        $address_id = $pdo->lastInsertId();

        $user_sql = "INSERT INTO users (username, first_name, middle_name, last_name, email, password, birthday, gender, status, date_created, address_id)
                     VALUES (:username, :first_name, :middle_name, :last_name, :email, :password, :birthday, :gender, 'active', current_timestamp(), :address_id)";
        
        $user_stmt = $pdo->prepare($user_sql);
        $user_stmt->execute([
            ':username' => $username,
            ':first_name' => $first_name,
            ':middle_name' => $middle_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':password' => $hashed_password,
            ':birthday' => $birthday,
            ':gender' => $gender,
            ':address_id' => $address_id
        ]);

        $_SESSION['user_id'] = $pdo->lastInsertId(); 
        $_SESSION['username'] = $username;

        $cart_sql = "INSERT INTO carts (user_id) VALUES (:user_id)";
        $cart_stmt = $pdo->prepare($cart_sql);
        $cart_stmt->execute([':user_id' => $_SESSION['user_id']]);

        $cart_id = $pdo->lastInsertId();
        $update_user_sql = "UPDATE users SET cart_id = :cart_id WHERE user_id = :user_id";
        $update_user_stmt = $pdo->prepare($update_user_sql);
        $update_user_stmt->execute([
            ':cart_id' => $cart_id,
            ':user_id' => $_SESSION['user_id']
        ]);

        $pdo->commit();

        header("Location: http://localhost:3000/index.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
        exit();
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
    <script type="module" src="http://localhost:3000/scripts/showPassword.js" defer></script>
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
                        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
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
