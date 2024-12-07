<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$head_id = $_SESSION['user_id'];

$report_query = "SELECT students.full_name, students.cgpa, applications.status 
                 FROM applications 
                 JOIN students ON applications.student_id = students.user_id 
                 JOIN head_assignment ON applications.assigned_department_id = head_assignment.department_id 
                 WHERE head_assignment.head_id = ?";
$stmt = $conn->prepare($report_query);
$stmt->bind_param("i", $head_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Reports</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom, #a8e063, #56ab2f);
            padding: 10px;
        }
        .content {
            max-width: 1400px;
            height: 100vh;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 20px;
            animation: fadeIn 1.5s ease-in-out;
            color: #333;
        }
        .page-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 2.5em;
            color: #185a9d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #299B63;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e9f9ec;
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
<?php include "../dashboard.php"  ?>
    <div class="main">
<div class="content">
    <div class="page-header">
        <h1>Department Reports</h1>
        <p>Monitor student assignments and performance in your department.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>CGPA</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['full_name']); ?></td>
                        <td><?= htmlspecialchars($row['cgpa']); ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
</body>
</html>
