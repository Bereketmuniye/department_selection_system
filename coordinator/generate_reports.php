<?php
include('../includes/db.php');

$total_students = $total_departments = $total_applications = 0;
$pending_apps = $approved_apps = $rejected_apps = 0;

$query = "SELECT 
    (SELECT COUNT(*) FROM students) AS total_students,
    (SELECT COUNT(*) FROM departments) AS total_departments,
    (SELECT COUNT(*) FROM applications) AS total_applications,
    (SELECT COUNT(*) FROM applications WHERE status='Pending') AS pending_apps,
    (SELECT COUNT(*) FROM applications WHERE status='Approved') AS approved_apps,
    (SELECT COUNT(*) FROM applications WHERE status='Rejected') AS rejected_apps";
$result = $conn->query($query);

if ($result && $row = $result->fetch_assoc()) {
    $total_students = $row['total_students'];
    $total_departments = $row['total_departments'];
    $total_applications = $row['total_applications'];
    $pending_apps = $row['pending_apps'];
    $approved_apps = $row['approved_apps'];
    $rejected_apps = $row['rejected_apps'];
}

$detailed_query = "
    SELECT 
        students.full_name,
        students.cgpa,
        applications.department_preferences,
        applications.status,
        departments.department_name AS assigned_department
    FROM students
    LEFT JOIN applications ON students.user_id = applications.student_id
    LEFT JOIN departments ON applications.assigned_department_id = departments.id";
$detailed_result = $conn->query($detailed_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Reports</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .main {
            padding: 40px;
            background-color: #f9f9f9;
            min-height: 100vh;
            box-sizing: border-box;
            overflow: hidden;
        }

        .page-title {
            font-size: 2.5em;
            font-weight: bold;
            text-align: center;
            color: #444;
            margin-bottom: 20px;
            animation: fadeIn 2s ease-in-out;
        }

        .summary-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-bottom: 40px;
            animation: slideIn 2s ease-in-out;
        }

        .summary-box {
            background-color: #fff;
            color: #299B63;
            border-radius: 8px;
            padding: 20px;
            width: 200px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .summary-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .summary-box h2 {
            font-size: 2.5em;
            margin: 0;
        }

        .summary-box p {
            font-size: 1.1em;
            margin: 10px 0 0;
        }

        .summary-box .icon {
            font-size: 30px;
            color: #299B63;
            margin-bottom: 10px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 2.5s ease-in-out;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #ddd;
            padding: 10px;
            
        }

        .report-table th {
            background-color: #299B63;
            color: white;
        }

        .report-table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .report-table tbody tr:hover {
            background-color: #eaeaea;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(30px);
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
    <div class="page-title">Coordinator Reports</div>

    <div class="summary-container">
        <div class="summary-box">
            <i class="icon fas fa-users"></i>
            <h2><?php echo $total_students; ?></h2>
            <p>Total Students</p>
        </div>
        <div class="summary-box">
            <i class="icon fas fa-university"></i>
            <h2><?php echo $total_departments; ?></h2>
            <p>Total Departments</p>
        </div>
        <div class="summary-box">
            <i class="icon fas fa-file-alt"></i>
            <h2><?php echo $total_applications; ?></h2>
            <p>Total Applications</p>
        </div>
        <div class="summary-box">
            <i class="icon fas fa-clock"></i>
            <h2><?php echo $pending_apps; ?></h2>
            <p>Pending Applications</p>
        </div>
        <div class="summary-box">
            <i class="icon fas fa-check-circle"></i>
            <h2><?php echo $approved_apps; ?></h2>
            <p>Approved Applications</p>
        </div>
        <div class="summary-box">
            <i class="icon fas fa-times-circle"></i>
            <h2><?php echo $rejected_apps; ?></h2>
            <p>Rejected Applications</p>
        </div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Department Preferences</th>
                <th>Status</th>
                <th>Assigned Department</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($detailed_result && $detailed_result->num_rows > 0): ?>
                <?php while ($row = $detailed_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td>
                            <?php 
                            $preferences = explode(',', $row['department_preferences']);
                            foreach ($preferences as $index => $pref) {
                                $dept_query = $conn->prepare("SELECT department_name FROM departments WHERE id = ?");
                                $dept_query->bind_param("i", $pref);
                                $dept_query->execute();
                                $dept_result = $dept_query->get_result();
                                if ($dept_result->num_rows > 0) {
                                    $dept_name = $dept_result->fetch_assoc()['department_name'];
                                    echo ($index + 1) . '. ' . htmlspecialchars($dept_name) . '<br>';
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['assigned_department'] ?? 'N/A'); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No data available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
