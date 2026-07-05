<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voting System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <a href="index.php" class="active">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>
    
    <div class="welcome-container">
        <h1>Secure E-Voting System</h1>
        <p>Welcome to the next generation of democratic participation. Our system features cryptographically secure voting, multi-factor authentication, and a fully transparent process to ensure your vote is securely cast and counted.</p>
        
        <div class="cta-buttons">
            <a href="login.php" class="btn">Login to Vote</a>
            <a href="register.php" class="btn btn-secondary">Register Account</a>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> E-Voting System. All rights reserved.</p>
    </footer>
</body>
</html>