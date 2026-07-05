<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../database/db.php';
?>
<?php include 'dash_head.php'; ?>

<div class="card" style="margin: 0 auto; max-width: 900px; width: 100%;">
    <h2>Registered Candidates</h2>
    <p style="color: var(--text-secondary); margin-bottom: 20px;">List of all running candidates registered in the voting database.</p>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 100px; text-align: center;">Photo</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Party Affiliation</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("SELECT * FROM candidates");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $photo = htmlspecialchars($row['photo_url'] ?? 'uploads/default.png');
                    if (!file_exists('../' . $photo) || is_dir('../' . $photo)) {
                        $photo = 'uploads/default.png'; // Fallback if no file exists
                    }
                    echo "<tr>";
                    echo "<td style='text-align: center;'><img src='../" . $photo . "' alt='Candidate' style='width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--glass-border);'></td>";
                    echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['party_affiliation']) . "</td>";
                    echo "</tr>";
                }
                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'dash_foot.php'; ?>