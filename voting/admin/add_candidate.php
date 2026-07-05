<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../database/db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $party_affiliation = trim($_POST['party_affiliation']);

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        // Fix target dir to project root
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        // Sanitize file name to avoid security issues
        $filename = time() . '_' . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_url = 'uploads/' . $filename;

            // Insert candidate into database
            $stmt = $conn->prepare("INSERT INTO candidates (first_name, last_name, party_affiliation, photo_url) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                $error = "SQL prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param("ssss", $first_name, $last_name, $party_affiliation, $photo_url);
                if ($stmt->execute()) {
                    $success = "Candidate added successfully!";
                } else {
                    $error = "Execution failed: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            $error = "Failed to save uploaded photo.";
        }
    } else {
        $error = "Please upload a valid photo file.";
    }
}
?>

<?php include 'dash_head.php'; ?>

<form action="add_candidate.php" method="POST" enctype="multipart/form-data" style="margin: 20px auto; max-width: 550px;">
    <h2>Add Candidate</h2>
    <p style="color: var(--text-secondary); text-align: center; margin-bottom: 25px;">Register a candidate for the election.</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <span>❌ <?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <span>✅ <?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>

    <label for="first_name">First Name:</label>
    <input type="text" id="first_name" name="first_name" placeholder="Enter first name" required>

    <label for="last_name">Last Name:</label>
    <input type="text" id="last_name" name="last_name" placeholder="Enter last name" required>

    <label for="party_affiliation">Party Affiliation:</label>
    <input type="text" id="party_affiliation" name="party_affiliation" placeholder="e.g. Democratic Party, Independent" required>

    <label for="photo">Candidate Photo:</label>
    <input type="file" id="photo" name="photo" accept="image/*" required>

    <input type="submit" value="Add Candidate">
</form>

<?php include 'dash_foot.php'; ?>