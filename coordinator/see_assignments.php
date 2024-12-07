<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>See Department Assignments</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .f{
            width: 100%;
            max-width: 100%;
            height: 100vh;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
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
    <div class="f">
    <h2>Student Department Assignments</h2>

    <?php
    include('../includes/db.php');
    $query = "
        SELECT 
            students.full_name AS student_name, 
            departments.department_name AS department_name 
        FROM 
            applications 
        JOIN 
            students ON applications.student_id = students.user_id 
        JOIN 
            departments ON applications.assigned_department_id = departments.id 
        WHERE 
            applications.status = 'Approved'
    ";

    $result = $conn->query($query);

    if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Assigned Department</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No assignments found.</p>
    <?php endif; ?>
    </div>
</div>
</body>
</html>
