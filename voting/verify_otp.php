<?php
session_start();

// Session check
if (!isset($_SESSION['otp'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$local_otp_helper = '';

// Check if SMTP failed or if the email is a local testing email (e.g. ending in @voting.com or matching the admin test email)
$email = $_SESSION['email'] ?? '';
$is_testing_email = (strpos($email, '@voting.com') !== false || strpos($email, 'test') !== false || strpos($email, 'ac840165') !== false);

if (isset($_SESSION['otp']) && (isset($_SESSION['otp_status']) && $_SESSION['otp_status'] === 'failed_local' || $is_testing_email)) {
    $local_otp_helper = "⚠️ Local Testing Mode: For testing account (<strong>" . htmlspecialchars($email) . "</strong>), your OTP is: <strong>" . $_SESSION['otp'] . "</strong>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp']);

    if ($entered_otp == $_SESSION['otp']) {
        $_SESSION['user_id'] = $_SESSION['user_id_temp'];
        $_SESSION['user_type'] = $_SESSION['user_type_temp'];

        unset($_SESSION['otp']);
        unset($_SESSION['otp_status']);
        unset($_SESSION['user_id_temp']);
        unset($_SESSION['user_type_temp']);

        if (strtolower($_SESSION['user_type']) === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid OTP. Please check and try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voting System - Verify OTP</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>
    
    <form method="post">
        <h2>Verify OTP</h2>
        
        <?php if (!empty($local_otp_helper)): ?>
            <div class="alert alert-info">
                <span><?php echo $local_otp_helper; ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span>❌ <?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <label for="otp">Enter Verification Code:</label>
        <input type="text" id="otp" name="otp" placeholder="Enter 6-digit OTP" required autocomplete="off">
        
        <button type="submit">Verify Code</button>
    </form>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> E-Voting System. All rights reserved.</p>
    </footer>
</body>
</html>