<?php
include('../includes/db.php');

if (!isset($_GET['role']) || empty($_GET['role'])) {
    die("Error: No role specified.");
}

$role = $_GET['role'];
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'student';  

    // Check if the username already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error_message = "Username already exists. Please choose another.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $error_message = "Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);

        if ($stmt->execute()) {
            header('Location: ../login.php?role=' . urlencode($role));
            exit();
        } else {
            $error_message = "Error registering user. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Error message styling */
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="wrapper login">
        <div class="container">
            <div class="col-left">
                <div class="login-text">
                    <h2>Register Here!</h2>
                    <p>Already have an account?<br>Sign in now!</p>
                    <a href="../login.php?role=<?= htmlspecialchars($role) ?>" class="btn">Login</a>
                </div>
            </div>
            <div class="col-right">
                <div class="login-form">
                    <h2>Create an Account</h2>
                    <?php if (!empty($error_message)): ?>
                        <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <p>
                            <label for="username">Username<span>*</span></label>
                            <input type="text" id="username" name="username" placeholder="Username" required>
                        </p>
                        <p>
                            <label for="password">Password<span>*</span></label>
                            <input type="password" id="password" name="password" placeholder="Password" required>
                        </p>
                        <p>
                            <label for="confirm_password">Confirm Password<span>*</span></label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                        </p>
                        <p>
                            <input type="submit" value="Register">
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
