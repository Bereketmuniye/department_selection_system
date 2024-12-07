<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$student_id = $_SESSION['user_id'];

$sql = "SELECT applications.*, 
               GROUP_CONCAT(departments.department_name ORDER BY departments.id SEPARATOR ', ') AS department_names,
               (SELECT department_name FROM departments WHERE departments.id = applications.assigned_department_id) AS assigned_department_name
        FROM applications 
        LEFT JOIN departments ON FIND_IN_SET(departments.id, applications.department_preferences)
        WHERE applications.student_id = ? 
        GROUP BY applications.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Assigned Department</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>

        .content {
            max-width: 80%;
            margin: 0 auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1.5s ease-in-out;
        }

        p {
            font-size: 1.2em;
            margin: 15px 0;
            color: #555;
        }

        strong {
            color: #333;
            font-weight: bold;
        }

        .complaint-form {
            margin-top: 30px;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
            resize: vertical;
            margin-bottom: 10px;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 12px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.2em;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
        }

        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
        }

        p, button, textarea, h2 {
            animation: slideDown 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <?php include '../dashboard.php'; ?>
    <div class="main">
        <div class="content">
            <h1 style="text-align: center;color: #007BFF;font-size: 2.5em;margin-bottom: 20px;animation: slideDown 1s ease-in-out;">Your Assigned Department</h1>
            <?php if ($result->num_rows > 0): ?>
                <?php $row = $result->fetch_assoc(); ?>
                <p><strong>Your Preferences:</strong> 
                <?php echo $row['department_names'] ? htmlspecialchars($row['department_names']) : 'Not assigned yet'; ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                <p><strong>Assigned Department:</strong> 
                <?php echo $row['assigned_department_name'] ? htmlspecialchars($row['assigned_department_name']) : 'Not Assigned Yet'; ?></p>
                
                <h2>Submit a Complaint</h2>
                <form method="POST" class="complaint-form">
                    <textarea name="complaint" rows="4" placeholder="Enter your complaint here..." required></textarea>
                    <button type="submit">Submit Complaint</button>
                </form>

                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['complaint'])) {
                    $complaint = trim($_POST['complaint']);
                    $complaint_stmt = $conn->prepare("INSERT INTO complaints (student_id, complaint, status, created_at) 
                                                      VALUES (?, ?, 'Pending', NOW())");
                    $complaint_stmt->bind_param("is", $student_id, $complaint);

                    if ($complaint_stmt->execute()) {
                        echo "<p class='success-message'>Complaint submitted successfully.</p>";
                    } else {
                        echo "<p class='error-message'>Error submitting complaint: " . htmlspecialchars($complaint_stmt->error) . "</p>";
                    }

                    $complaint_stmt->close();
                }
                ?>
            <?php else: ?>
                <p>No assignment found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
