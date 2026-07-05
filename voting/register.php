<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'database/db.php';

    // Set proper character set to avoid bind issues
    $conn->set_charset("utf8mb4");

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $dob = $_POST['dob'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, date_of_birth, password_hash, phone) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        $save_txt = "<div class='alert alert-error'>❌ Prepare failed: " . htmlspecialchars($conn->error) . "</div>";
    } else {
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $dob, $password, $phone);
        
        if ($stmt->execute()) {
            $save_txt = "<div class='alert alert-success'>✅ Registration successful. <a href='login.php' style='color:inherit;font-weight:600;'>Login here</a>.</div>";
        } else {
            $save_txt = "<div class='alert alert-error'>❌ Registration failed. " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voting System - Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php" class="active">Register</a>
    </nav>
    
    <form action="register.php" method="post">
        <h2>Register</h2>
        
        <?php
        if (isset($save_txt)) {
            echo $save_txt;
        }
        ?>
        
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" placeholder="Enter first name" required>
        
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" placeholder="Enter last name" required>
        
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" placeholder="Enter phone number" required>
        
        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" placeholder="Enter email address" required>
        
        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Create a secure password" required>
        
        <input type="submit" value="Register">
    </form>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> E-Voting System. All rights reserved.</p>
    </footer>
</body>
</html>
