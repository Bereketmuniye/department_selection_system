<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    if (empty($username) || empty($_POST['password']) || empty($role)) {
        $error_message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            $success_message = "Account created successfully.";
            header("Location: manage_accounts.php?success=" . urlencode($success_message));
            exit();
        } else {
            $error_message = "Error creating account: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
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
    max-height: 500px;
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
    <h2>Create New Account</h2>
    <?php if (isset($error_message)) { echo "<div class='error-message'>{$error_message}</div>"; } ?>
    <?php if (isset($success_message)) { echo "<div class='success-message'>{$success_message}</div>"; } ?>

    <form method="POST" class="account-form">
        <label for="username">Username:</label>
        <input type="text" name="username" placeholder="Enter username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <label for="role">Role:</label>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="coordinator">Coordinator</option>
            <option value="department_head">Department Head</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit" class="btn-submit">Create Account</button>
    </form>
</div>
</div>
</body>
</html>
