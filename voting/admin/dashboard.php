<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../database/db.php';

$total_voters = 0;
$total_candidates = 0;
$total_votes = 0;

// Query counts
$res = $conn->query("SELECT COUNT(*) AS count FROM users WHERE type = 'voter'");
if ($res) {
    $row = $res->fetch_assoc();
    $total_voters = $row['count'];
}

$res = $conn->query("SELECT COUNT(*) AS count FROM candidates");
if ($res) {
    $row = $res->fetch_assoc();
    $total_candidates = $row['count'];
}

$res = $conn->query("SELECT COUNT(*) AS count FROM votes");
if ($res) {
    $row = $res->fetch_assoc();
    $total_votes = $row['count'];
}

$conn->close();
?>

<?php include 'dash_head.php'; ?>

<div class="card">
    <h2>Welcome Back, Administrator!</h2>
    <p style="color: var(--text-secondary); margin-top: 10px;">Here is the overview of the E-Voting System. Use the sidebar to manage voters, candidates, and review the live election counts.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Registered Voters</h3>
        <div class="number"><?php echo number_format($total_voters); ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Candidates</h3>
        <div class="number"><?php echo number_format($total_candidates); ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Votes Cast</h3>
        <div class="number"><?php echo number_format($total_votes); ?></div>
    </div>
</div>

<?php include 'dash_foot.php'; ?>