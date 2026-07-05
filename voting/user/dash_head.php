<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// protect page (only logged-in voter)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'voter') {
    header("Location: ../login.php");
    exit();
}

$user_id   = $_SESSION['user_id'] ?? '';
$user_type = $_SESSION['user_type'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Dashboard - E-Voting</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Voter Portal</h2>
            
            <div class="user-info">
                <h3>Welcome!</h3>
                <p>Voter ID: <?php echo htmlspecialchars((string)$user_id); ?></p>
                <p>Role: Registered Voter</p>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">Cast Vote</a></li>
                <li><a href="voting_history.php" class="<?php echo $current_page === 'voting_history.php' ? 'active' : ''; ?>">Voting History</a></li>
                <li><a href="results.php" class="<?php echo $current_page === 'results.php' ? 'active' : ''; ?>">Election Results</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Voter Dashboard</h1>
                <a href="../logout.php" class="logout">Logout</a>
            </div>