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

// Handle file upload and update user details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $profile = $_FILES['profile']['name'];

    if ($profile) {
        $target_dir = __DIR__ . "/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($profile);
        move_uploaded_file($_FILES["profile"]["tmp_name"], $target_file);
    }

    $update_query = "UPDATE users SET username = ?, profile = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssi", $username, $profile, $user_id);
    $stmt->execute();
    header("Refresh:0");
}

// Fetch user details
$query = "SELECT username, profile FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Profile picture logic
$profile_picture = file_exists(__DIR__ . "/uploads/" . $user['profile']) 
                   ? "uploads/" . $user['profile'] 
                   : "img/default_profile.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>

        .profile-container {
            margin-left: 50px;
            margin-top: 50px;
            max-width: 800px;
            width: 90%;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-picture {
            flex: 1 1 200px;
            text-align: center;
        }

        .profile-picture img {
            margin-left: 80px;
            margin-top: 80px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #299B63;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-form {
            flex: 2 1 300px;
            padding: 20px;
        }

        .profile-form h2 {
            margin-bottom: 20px;
            color: #299B63;
            font-size: 20px;
            text-align: center;
        }

        .profile-form label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: bold;
        }

        .profile-form input[type="text"],
        .profile-form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .profile-form button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #299B63;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .profile-form button:hover {
            background-color: #218c57;
        }

        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }

            .profile-picture, .profile-form {
                flex: 1 1 auto;
                width: 100%;
                text-align: center;
            }

            .profile-form {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include "../dashboard.php"; ?>
    <div class="main">
    <div class="profile-container">
        <div class="profile-picture">
            <img src="<?= $profile_picture ?>" alt="Profile Picture">
        </div>

        <div class="profile-form">
            <h2>Edit Profile</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

                <label for="profile">Upload Profile Picture</label>
                <input type="file" id="profile" name="profile">

                <button type="submit">Save Changes</button>
            </form>
        </div>
        </div>
    </div>
</body>
</html>
