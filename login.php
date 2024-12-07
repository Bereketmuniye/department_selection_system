<?php
include('includes/db.php');
include('includes/auth.php');

if (!isset($_GET['role']) || empty($_GET['role'])) {
    die("Error: No role specified.");
}

$role = $_GET['role'];

if (empty($role)) {
    
    $role = "student";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND role='$role'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $role;

            switch ($role) {
                case 'student':
                    header("Location: student/home.php");
                    break;
                case 'admin':
                    header("Location: admin/home.php");
                    break;
                case 'coordinator':
                    header("Location: coordinator/home.php");
                    break;
                case 'department_head':
                    header("Location: department_head/home.php");
                    break;
                default:
                    echo "Invalid role.";
            }
        } else {
            $error_message = "Invalid credentials.";
        }
    } else {
        $error_message = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
                .error {
            color: red;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="wrapper login">
        <div class="container">
            <div class="col-left">
                <div class="login-text">
                    <h2>Welcome!</h2>
                    <p>Create your account.<br>For Free!</p>
                    <a href="student/register.php?role=<?= htmlspecialchars($role) ?>" class="btn">Sign Up</a>
                </div>
            </div>
            <div class="col-right">
                <div class="login-form">
                <h2>Login as <?= htmlspecialchars(ucwords(str_replace('_', ' ', $role))) ?></h2>
                <?php if (!empty($error_message)): ?>
                    <div class="error"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                    <form action="" method="POST">
                        <p>
                            <label for="username">Username<span>*</span></label>
                            <input type="text" id="username" name="username" placeholder="Username or Email" required>
                        </p>
                        <p>
                            <label for="password">Password<span>*</span></label>
                            <input type="password" id="password" name="password" placeholder="Password" required>
                        </p>
                        <p>
                            <input type="submit" value="Sign In">
                        </p>
                        <p><a href="#">Forgot password?</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
