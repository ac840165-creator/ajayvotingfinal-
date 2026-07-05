<?php
// show candidate name with party_affiliation and with photo

session_start();

// safe check (no warning)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'voter') {
    header("Location: ../login.php");
    exit();
}

require_once '../database/db.php';

$user_id = $_SESSION['user_id'];

// Fetch voting history
$voting_history = [];
$stmt = $conn->prepare("SELECT candidates.first_name, candidates.last_name, candidates.party_affiliation, candidates.photo_url, votes.voted_at 
FROM votes 
JOIN candidates ON votes.candidate_id = candidates.candidate_id 
WHERE votes.user_id = ?");

if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $voting_history[] = $row;
}

$stmt->close();
$conn->close();
?>

<?php include 'dash_head.php'; ?>

<div class="content" style="max-width: 900px; margin: 0 auto; width: 100%;">
    <div class="card">
        <h2>Your Voting History</h2>
        <p style="color: var(--text-secondary); margin-bottom: 25px;">A log of all votes you have cast in this election system.</p>

        <?php if (count($voting_history) > 0): ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px; text-align: center;">Photo</th>
                            <th>Candidate Name</th>
                            <th>Party Affiliation</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($voting_history as $vote):
                            $photo = htmlspecialchars($vote['photo_url'] ?? 'uploads/default.png');
                            if (!file_exists('../' . $photo) || is_dir('../' . $photo)) {
                                $photo = 'uploads/default.png'; // Fallback if no file exists
                            }
                        ?>
                            <tr>
                                <td style="text-align: center;">
                                    <img src="../<?php echo $photo; ?>" alt="Candidate" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid var(--glass-border);">
                                </td>
                                <td style="font-weight: 600;">
                                    <?php echo htmlspecialchars($vote['first_name'] . ' ' . $vote['last_name']); ?>
                                </td>
                                <td style="color: var(--text-secondary);">
                                    <?php echo htmlspecialchars($vote['party_affiliation'] ?? 'Independent'); ?>
                                </td>
                                <td style="color: var(--text-secondary); font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($vote['voted_at']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; color: var(--text-secondary); padding: 40px 0;">
                You have not cast any votes yet.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'dash_foot.php'; ?>