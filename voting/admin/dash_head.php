<?php
// admin/dash_head.php

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: protect page (only logged-in admin)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Safe values
$user_id   = $_SESSION['user_id'] ?? '';
$user_type = $_SESSION['user_type'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Voting</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Admin Area</h2>
            
            <div class="user-info">
                <h3>System Admin</h3>
                <p>ID: <?php echo htmlspecialchars((string)$user_id); ?></p>
                <p>Role: Administrator</p>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="add_user.php" class="<?php echo $current_page === 'add_user.php' ? 'active' : ''; ?>">Add User</a></li>
                <li><a href="view_users.php" class="<?php echo $current_page === 'view_users.php' ? 'active' : ''; ?>">View Users</a></li>
                <li><a href="add_candidate.php" class="<?php echo $current_page === 'add_candidate.php' ? 'active' : ''; ?>">Add Candidate</a></li>
                <li><a href="view_candidates.php" class="<?php echo $current_page === 'view_candidates.php' ? 'active' : ''; ?>">View Candidates</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Admin Control Panel</h1>
                <a href="../logout.php" class="logout">Logout</a>
            </div>
            
            <div class="content">