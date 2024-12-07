<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fetchedUsername);
$stmt->fetch();
$stmt->close();

$dashboardUsername = $fetchedUsername ? htmlspecialchars($fetchedUsername, ENT_QUOTES, 'UTF-8') : 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k6pqA6Q90SOw9N4RZNRuXb8ZbJ4WqMYbUI6J6Zb0UpOOWIWYZiJG7j0+ikYdJ57rYklxvA9+yI2xg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .main {
            width: 100%;
            max-width: 1350px;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out;
        }

        .main h2 {
            font-size: 2.5em;
            margin-bottom: 20px;
            text-align: center;
            color: #299B63;
            animation: slideDown 1.5s ease;
        }

        .main p {
            font-size: 1.2em;
            line-height: 1.8;
            color: #555;
            text-align: center;
            margin-bottom: 40px;
        }

        .dashboard-links {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }

        .dashboard-link {
            text-decoration: none;
            width: 250px;
            height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #2ecc71;
            color: white;
            font-weight: bold;
            border-radius: 20px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }

        .dashboard-link i {
            font-size: 3em;
            margin-bottom: 15px;
        }

        .dashboard-link:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            background: linear-gradient(120deg, #66a6ff, #89f7fe);
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

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
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
        <h2>Welcome, <?php echo $dashboardUsername; ?>!</h2>
        <p>Explore the dashboard to select your department, view assignments, submit complaints, and update your profile.</p>
        <div class="dashboard-links">
            <a href="select_department.php" class="dashboard-link">
                <i class="fas fa-university"></i>
                Select Department
            </a>
            <a href="view_assignment.php" class="dashboard-link">
                <i class="fas fa-tasks"></i>
                View Assignment
            </a>
            <a href="view_complains_result.php" class="dashboard-link">
                <i class="fas fa-comments"></i>
                View Complaint Result
            </a>
            <a href="profile.php" class="dashboard-link">
                <i class="fas fa-user-circle"></i>
                Profile
            </a>
        </div>
    </div>
</body>
</html>
