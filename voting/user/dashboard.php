<?php
session_start();

// SAFE SESSION CHECK
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'voter') {
    header("Location: ../login.php");
    exit();
}

require_once '../database/db.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle vote action
if (isset($_GET['candidate_id'])) {
    $candidate_id = intval($_GET['candidate_id']);

    // Check if already voted for this candidate
    $check_vote = $conn->prepare("SELECT vote_id FROM votes WHERE user_id = ? AND candidate_id = ?");
    if (!$check_vote) {
        $error = "SQL prepare failed: " . $conn->error;
    } else {
        $check_vote->bind_param("ii", $user_id, $candidate_id);
        $check_vote->execute();
        $check_vote->store_result();

        if ($check_vote->num_rows == 0) {
            // Insert vote
            $insert_vote = $conn->prepare("INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)");
            $insert_vote->bind_param("ii", $user_id, $candidate_id);
            if ($insert_vote->execute()) {
                $success = "Your vote has been cast successfully!";
            } else {
                $error = "Failed to submit vote: " . $insert_vote->error;
            }
            $insert_vote->close();
        } else {
            $error = "You have already voted for this candidate.";
        }
        $check_vote->close();
    }
}

// Fetch candidates with full columns
$candidates = [];
$stmt = $conn->prepare("SELECT candidate_id, first_name, last_name, party_affiliation, photo_url FROM candidates");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $candidates[] = $row;
}

// Fetch voted candidates
$voted_candidates = [];
$vote_stmt = $conn->prepare("SELECT candidate_id FROM votes WHERE user_id = ?");
$vote_stmt->bind_param("i", $user_id);
$vote_stmt->execute();
$vote_result = $vote_stmt->get_result();

while ($vote = $vote_result->fetch_assoc()) {
    $voted_candidates[] = $vote['candidate_id'];
}

$stmt->close();
$vote_stmt->close();
$conn->close();
?>

<?php include 'dash_head.php'; ?>

<div class="content">
    <div class="card">
        <h2>Available Candidates</h2>
        <p style="color: var(--text-secondary); margin-bottom: 25px;">Please review the candidates below and cast your vote by clicking "Cast Vote". You may vote for each candidate once.</p>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <span>✅ <?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span>❌ <?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <div class="candidates-grid">
            <?php if (count($candidates) > 0): ?>
                <?php foreach ($candidates as $candidate):
                    $candidate_id = $candidate['candidate_id'];
                    $name = htmlspecialchars($candidate['first_name'] . ' ' . $candidate['last_name']);
                    $party = htmlspecialchars($candidate['party_affiliation'] ?? 'Independent');
                    $photo = htmlspecialchars($candidate['photo_url'] ?? 'uploads/default.png');
                    if (!file_exists('../' . $photo) || is_dir('../' . $photo)) {
                        $photo = 'uploads/default.png'; // Fallback if no file exists
                    }
                    $has_voted = in_array($candidate_id, $voted_candidates);
                ?>
                    <div class="candidate-card">
                        <img src="../<?php echo $photo; ?>" alt="<?php echo $name; ?>" class="candidate-photo">
                        <div class="candidate-name"><?php echo $name; ?></div>
                        <div class="candidate-party"><?php echo $party; ?></div>

                        <?php if ($has_voted): ?>
                            <button class="vote-btn disabled" disabled>Voted</button>
                        <?php else: ?>
                            <a href="dashboard.php?candidate_id=<?php echo $candidate_id; ?>" class="vote-btn">Cast Vote</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; color: var(--text-secondary); padding: 40px 0;">
                    No candidates are currently registered.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'dash_foot.php'; ?>