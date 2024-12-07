<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "../includes/db.php";
$count = 0;
$user_id = $_SESSION['user_id'];

$query_dept = "SELECT department_id FROM head_assignment WHERE head_id = '$user_id'";
$result_dept = mysqli_query($conn, $query_dept);

if (mysqli_num_rows($result_dept) > 0) {
    $row_dept = mysqli_fetch_assoc($result_dept);
    $department_id = $row_dept['department_id'];

    $query_students = "
        SELECT s.full_name 
        FROM students s 
        INNER JOIN applications a ON a.student_id = s.user_id 
        WHERE a.assigned_department_id = '$department_id'";
    $result_students = mysqli_query($conn, $query_students);
} else {
    echo "You are not assigned as a department head.";
    exit;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Students</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom, #a8e063, #56ab2f);
            padding: 10px;
        }
        .content {
            
            animation: fadeIn 1.5s ease-in-out;
        }

        h2 {
            background-color: white;
            color: white;
            padding: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .no-students {
            text-align: center;
            color: #888;
        }

        th {
            background-color: #299B63;
            color: white;
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
<?php include "../dashboard.php" ?>
<div class="main">
    <h2>Students in Your Department</h2>

    <div class="content">
        <?php if (mysqli_num_rows($result_students) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Student Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row_student = mysqli_fetch_assoc($result_students)): ?>
                        <?php $count++; ?>
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo htmlspecialchars($row_student['full_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-students">No students assigned to your department yet.</p>
        <?php endif; ?>
    </div>
    </div>
</body>
</html>
