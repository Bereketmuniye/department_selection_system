<?php
include 'includes/auth.php';
include 'includes/db.php';

$role = $_SESSION['role'] ?? '';
function showNav($role, $allowedRoles) {
    return in_array($role, $allowedRoles);
}
$user_id = $_SESSION['user_id'] ?? 0;

$unread_complaints_query = "SELECT COUNT(*) AS unread_count FROM complaints WHERE status = 'Pending'";
$unread_result = $conn->query($unread_complaints_query);
$unread_complaints = $unread_result->fetch_assoc()['unread_count'] ?? 0;

$user_query = "SELECT profile FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();

$profile_picture = "img/default_profile.png"; // Default profile picture
if ($user_data && isset($user_data['profile']) && $user_data['profile'] !== 'default') {
    $profile_picture = "uploads/{$user_data['profile']}";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <style>
        .topbar {
            position: fixed;
            background-color: #fff;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.08);
            width: 100%;
            padding: 0 20px;
            height: 60px;
            display: grid;
            grid-template-columns: 1fr 6fr 1fr 1fr;
            align-items: center;
            z-index: 1;
        }

        .logo img {
            width: 60px;
            height: 50px;
            object-fit: cover;
        }

        .university_name {
            text-align: center;
            color: #2ecc71; 
            font-size: 30px;
        }

        .fa-bell {
            font-size: 20px;
            position: fixed;
            top: 20px;
            right: 150px;
            cursor: pointer;
        }

        .fa-bell .notification-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: red;
            color: white;
            font-size: 12px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            text-align: center;
            line-height: 18px;
            font-weight: bold;
        }

        .user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        @media (max-width:1115px) {
            .university_name {
                font-size: 25px;
            }
        }

        @media (max-width:880px) {
            .topbar {
                grid-template-columns: 1.6fr 6fr 0.4fr 1fr;
            } 
            .university_name {
                font-size: 20px;
            }
        }

        @media (max-width:500px) {
            .university_name {
                font-size: 15px;
            }
            .fa-bell {
                position: relative;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <div class="logo">
                <img src="../img/logo.jfif" alt="LOGO">
            </div>
            <div class="university_name">
                DEBRE MARKOS UNIVERSITY DMU
            </div>
            <?php if (showNav($role, ['coordinator'])): ?>
            <div class="notifications">
            <a href="notifications.php">
                <i class="fas fa-bell">
                    <?php if ($unread_complaints > 0): ?>
                        <span class="notification-count"><?= $unread_complaints ?></span>
                    <?php endif; ?>
                </i>
                </a>
            </div>
            <?php endif; ?>
            <div class="user">
                <img src="<?= htmlspecialchars($profile_picture) ?>" alt="User Profile">
            </div>
        </div>
        <div class="sidebar">
            <ul>
                <li><a href="home.php"><i class="fas fa-th-large"></i><div>Dashboard</div></a></li>
                <?php if (showNav($role, ['admin'])): ?>
                    <li><a href="manage_accounts.php"><i class="fas fa-users-cog"></i><div>Manage User Accounts</div></a></li>
                    <li><a href="manage_departments.php"><i class="fas fa-building"></i><div>Manage Departments</div></a></li>
                    <li><a href="assign_head.php"><i class="fas fa-user-tie"></i><div>Assign Department Heads</div></a></li>
                <?php endif; ?>
                <?php if (showNav($role, ['student'])): ?>
                    <li><a href="select_department.php"><i class="fas fa-university"></i><div>Select Department</div></a></li>
                    <li><a href="view_assignment.php"><i class="fas fa-tasks"></i><div>View Department Assignment</div></a></li>
                    <li><a href="view_complains_result.php"><i class="fas fa-clipboard-check"></i><div>View Complains Result</div></a></li>
                <?php endif; ?>
                <?php if (showNav($role, ['coordinator'])): ?>
                    <li><a href="manage_applications.php"><i class="fas fa-file-signature"></i><div>Manage Student Applications</div></a></li>
                    <li><a href="generate_reports.php"><i class="fas fa-chart-line"></i><div>View Reports</div></a></li>
                    <li><a href="see_assignments.php"><i class="fas fa-list-alt"></i><div>See Department Assignments</div></a></li>
                <?php endif; ?>
                <?php if (showNav($role, ['department_head'])): ?>
                    <li><a href="view_students.php"><i class="fas fa-user-graduate"></i><div>View Students</div></a></li>
                    <li><a href="view_reports.php"><i class="fas fa-file-alt"></i><div>View Reports</div></a></li>
                <?php endif; ?>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i><div>Profile</div></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i><div>Logout</div></a></li>
            </ul>
        </div>
    </div>
</body>
</html>