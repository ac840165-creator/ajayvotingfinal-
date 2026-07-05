<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../database/db.php';

// Fetch users from database
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, date_of_birth, type, phone FROM users");
if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<?php include 'dash_head.php'; ?>

<div class="card" style="margin: 0 auto; max-width: 1000px; width: 100%;">
    <h2>Registered Users</h2>
    <p style="color: var(--text-secondary); margin-bottom: 20px;">List of all voters and administrators currently registered in the database.</p>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Date of Birth</th>
                    <th>User Type</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
                        <td>
                            <span style="padding: 4px 8px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; 
                                background: <?php echo $row['type'] === 'admin' ? 'rgba(239, 68, 68, 0.15)' : 'rgba(59, 130, 246, 0.15)'; ?>;
                                color: <?php echo $row['type'] === 'admin' ? '#f87171' : '#60a5fa'; ?>;">
                                <?php echo ucfirst(htmlspecialchars($row['type'])); ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'dash_foot.php'; ?>