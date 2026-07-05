<?php
session_start();
require_once 'database/db.php';
require 'send_otp.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password_hash, type FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $password_hash, $type);
        $stmt->fetch();

        if (password_verify($password, $password_hash)) {

            $otp = rand(100000, 999999);

            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;
            $_SESSION['user_id_temp'] = $user_id;
            $_SESSION['user_type_temp'] = $type; 

            sendOTP($email, $otp);

            header("Location: verify_otp.php");
            exit();
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "Email address not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voting System - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
        <a href="login.php" class="active">Login</a>
        <a href="register.php">Register</a>
    </nav>
    
    <form action="login.php" method="post">
        <h2>Login</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span>❌ <?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        
        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
        
        <input type="submit" value="Login">
    </form>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> E-Voting System. All rights reserved.</p>
    </footer>
</body>
</html>