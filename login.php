<?php
session_start();
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, first_name, email, new_password FROM logindata WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $first_name, $email, $hashed_password);
        $stmt->fetch();
        
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start a session
            $_SESSION['username'] = $first_name;
            $_SESSION['email'] = $email;

            header("Location: dashboard.php");
            exit();
        } else {
            // Invalid password
            $error = "Invalid email or password.";
        }
    } else {
        // Invalid email
        $error = "Invalid email or password.";
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoomCloud Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <img src="asset_logo.png" alt="RoomCloud Logo">
                <span>Room|Cloud</span>
            </div>
            <div class="auth-links">
                <a href="login.php">Login</a>
                <a href="register.html" class="signup">Sign Up</a>
            </div>
        </header>
        <main>
            <div class="welcome-text">
                <h1>Welcome to</h1>
                <h2>Room|Cloud</h2>
            </div>
            <div class="login-box">
                <h3>Login</h3>
                <?php
                if (isset($error)) {
                    echo "<p style='color: red;'>$error</p>";
                }
                ?>
                <form method="POST" action="login.php">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    
                    <div class="keep-logged-in">
                        <input type="checkbox" id="keep-logged-in" name="keep-logged-in">
                        <label for="keep-logged-in">Keep me logged in</label>
                    </div>
                    
                    <button type="submit">Login</button>
                </form>
                <a href="resetpass.html" class="forgot-password">Forgot Password?</a>
            </div>
        </main>
        <footer>
            <a href="contactus.html">Contact Us</a>
            <a href="privacy.html">Privacy Policy</a>
            <a href="#">Help Center</a>
            <p>All rights reserved© 2024</p>
        </footer>
    </div>
</body>
</html>
