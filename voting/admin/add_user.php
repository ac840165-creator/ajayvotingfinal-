<?php
session_start();

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../database/db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $dob        = $_POST['dob'];
    $user_type  = $_POST['user_type'];
    $password   = $_POST['password'];

    // Validate type
    $allowed_types = ['admin', 'voter'];
    if (!in_array($user_type, $allowed_types)) {
        $error = "Invalid user type selected.";
    }

    if (empty($error)) {
        // Check email
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email address already registered.";
        }
        $check->close();
    }

    if (empty($error) && strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    }

    if (empty($error)) {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, date_of_birth, type, password_hash) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            $error = "Database prepare failure: " . $conn->error;
        } else {
            $stmt->bind_param("ssssss", $first_name, $last_name, $email, $dob, $user_type, $password_hash);

            if ($stmt->execute()) {
                $success = "User added successfully!";
            } else {
                $error = "Failed to add user: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<?php include 'dash_head.php'; ?>

<form action="add_user.php" method="post" style="margin: 20px auto; max-width: 550px;">
    <h2>Add New User</h2>
    <p style="color: var(--text-secondary); text-align: center; margin-bottom: 25px;">Create a new voter or administrator account.</p>

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

    <label>First Name:</label>
    <input type="text" name="first_name" placeholder="Enter first name" required>

    <label>Last Name:</label>
    <input type="text" name="last_name" placeholder="Enter last name" required>

    <label>Email Address:</label>
    <input type="email" name="email" placeholder="Enter email address" required>

    <label>Date of Birth:</label>
    <input type="date" name="dob" required>

    <label>User Type:</label>
    <select name="user_type" required>
        <option value="voter">Voter</option>
        <option value="admin">Admin</option>
    </select>

    <label>Password:</label>
    <input type="password" name="password" placeholder="Min. 6 characters" required>

    <input type="submit" value="Add User">
</form>

<?php include 'dash_foot.php'; ?>