<?php
// Result page

session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'voter') {
    header("Location: ../login.php");
    exit();
}

require_once '../database/db.php';

// Get ALL candidates with vote count
$vote_counts = [];

$stmt = $conn->prepare("
    SELECT 
        candidates.candidate_id,
        candidates.first_name,
        candidates.last_name,
        candidates.party_affiliation,
        candidates.photo_url,
        COUNT(votes.vote_id) AS vote_count
    FROM candidates
    LEFT JOIN votes ON votes.candidate_id = candidates.candidate_id
    GROUP BY candidates.candidate_id
    ORDER BY vote_count DESC
");

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $vote_counts[] = $row;
}

$stmt->close();
$conn->close();

// Winner & loser (safe handling)
$winner = $vote_counts[0] ?? null;
?>

<?php include 'dash_head.php'; ?>

<div class="content" style="max-width: 900px; margin: 0 auto; width: 100%;">
    <!-- Leader / Winner banner -->
    <?php if ($winner && $winner['vote_count'] > 0): ?>
        <div class="card" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(251, 191, 36, 0.05) 100%); border-color: rgba(245, 158, 11, 0.3); text-align: center; padding: 40px 20px;">
            <div style="font-size: 3rem; margin-bottom: 10px;">🏆</div>
            <h2 style="color: #fbbf24; font-size: 2rem; margin-bottom: 5px;">Current Election Leader</h2>
            <p style="font-size: 1.25rem; font-weight: 600; margin-bottom: 5px;">
                <?php echo htmlspecialchars($winner['first_name'] . ' ' . $winner['last_name']); ?>
            </p>
            <p style="color: var(--accent); font-weight: 500; font-size: 0.95rem; margin-bottom: 15px;">
                <?php echo htmlspecialchars($winner['party_affiliation'] ?? 'Independent'); ?>
            </p>
            <div style="display: inline-block; background: rgba(251, 191, 36, 0.2); color: #fbbf24; padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 1.1rem;">
                <?php echo (int)$winner['vote_count']; ?> Votes
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Live Standings</h2>
        <p style="color: var(--text-secondary); margin-bottom: 25px;">Live vote tallies for all candidates running in the current election.</p>

        <?php if (!empty($vote_counts)): ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px; text-align: center;">Photo</th>
                            <th>Candidate</th>
                            <th>Party Affiliation</th>
                            <th style="text-align: right; width: 150px;">Votes Tally</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vote_counts as $candidate):
                            $photo = htmlspecialchars($candidate['photo_url'] ?? 'uploads/default.png');
                            if (!file_exists('../' . $photo) || is_dir('../' . $photo)) {
                                $photo = 'uploads/default.png'; // Fallback if no file exists
                            }
                        ?>
                            <tr>
                                <td style="text-align: center;">
                                    <img src="../<?php echo $photo; ?>" alt="Candidate" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid var(--glass-border);">
                                </td>
                                <td style="font-weight: 600;">
                                    <?php echo htmlspecialchars($candidate['first_name'] . ' ' . $candidate['last_name']); ?>
                                </td>
                                <td style="color: var(--text-secondary);">
                                    <?php echo htmlspecialchars($candidate['party_affiliation'] ?? 'Independent'); ?>
                                </td>
                                <td style="text-align: right; font-weight: 700; color: var(--accent); font-size: 1.1rem;">
                                    <?php echo (int)($candidate['vote_count'] ?? 0); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; color: var(--text-secondary); padding: 40px 0;">
                No election results available.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'dash_foot.php'; ?>