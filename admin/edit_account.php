<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit();
}
if (!isset($_GET['id'])) {
    header("Location: manage_accounts.php");
    exit();
}

$user_id = $_GET['id'];
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_accounts.php");
    exit();
}

$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    if (empty($username) || empty($role)) {
        $error_message = "All fields are required.";
    } else {
        $update_stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $update_stmt->bind_param("ssi", $username, $role, $user_id);

        if ($update_stmt->execute()) {
            $success_message = "User updated successfully.";
            header("Location: manage_accounts.php?success=" . urlencode($success_message));
            exit();
        } else {
            $error_message = "Error updating user: " . $update_stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
    body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom, #a8e063, #56ab2f);
            padding: 10px;
    }
    .ac-main {
    width: 100%;
    max-width: 500px; 
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
    margin-left: 300px;
    margin-top: 10px;
    animation: fadeIn 1.5s ease-in-out;
}
@keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<?php include '../dashboard.php'; ?>
<div class="main">
<div class="ac-main">
    <h2>Edit User Account</h2>
    <?php if (isset($error_message)) { echo "<div class='error-message'>{$error_message}</div>"; } ?>
    <?php if (isset($success_message)) { echo "<div class='success-message'>{$success_message}</div>"; } ?>

    <form method="POST" class="account-form">
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="role">Role:</label>
        <select name="role" required>
            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
            <option value="student" <?php echo $user['role'] == 'student' ? 'selected' : ''; ?>>Student</option>
            <option value="coordinator" <?php echo $user['role'] == 'coordinator' ? 'selected' : ''; ?>>Coordinator</option>
            <option value="department_head" <?php echo $user['role'] == 'department_head' ? 'selected' : ''; ?>>Department Head</option>
        </select>

        <button type="submit" class="btn-submit">Save Changes</button>
    </form>
</div>
</div>
</body>
</html>
