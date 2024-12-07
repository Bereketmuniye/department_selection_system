<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit();
}

$resultTotalUsers = $conn->query("SELECT COUNT(*) as total_users FROM users");
$totalUsers = $resultTotalUsers->fetch_assoc()['total_users'];

$resultStudents = $conn->query("SELECT COUNT(*) as total_students FROM users WHERE role = 'student'");
$totalStudents = $resultStudents->fetch_assoc()['total_students'];

$resultDepartments = $conn->query("SELECT COUNT(*) as total_departments FROM departments");
$totalDepartments = $resultDepartments->fetch_assoc()['total_departments'];

$resultUserRoles = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
$userRoleData = [];
while ($row = $resultUserRoles->fetch_assoc()) {
    $userRoleData[] = $row;
}

$userRoleDataJson = json_encode($userRoleData);

$resultDepartmentCategories = $conn->query("SELECT category, COUNT(*) as count FROM departments GROUP BY category");
$departmentCategoryData = [];
while ($row = $resultDepartmentCategories->fetch_assoc()) {
    $departmentCategoryData[] = $row;
}

$departmentCategoryDataJson = json_encode($departmentCategoryData);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
<style>
    
    body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom, #a8e063, #56ab2f);
            padding: 10px;
        }
</style>
</head>
<body>
<?php include '../dashboard.php'; ?>
    <div class="main">
        <div class="cards">
            <div class="card">
                <div class="card-content">
                    <div class="number"><?php echo $totalUsers; ?></div>
                    <div class="card-name">Total Users</div>
                </div>
                <div class="icon-box"><i class="fas fa-users"></i></div>
            </div>
            <div class="card">
                <div class="card-content">
                    <div class="number"><?php echo $totalStudents; ?></div>
                    <div class="card-name">Students</div>
                </div>
                <div class="icon-box"><i class="fas fa-user-graduate"></i></div>
            </div>
            <div class="card">
                <div class="card-content">
                    <div class="number"><?php echo $totalDepartments; ?></div>
                    <div class="card-name">Departments</div>
                </div>
                <div class="icon-box"><i class="fas fa-building"></i></div>
            </div>
        </div>
        <div class="charts">
            <div class="chart">
              <h2>User Role Distribution</h2>
                <div><canvas id="barChart"></canvas></div>
            </div>
            <div class="chart doughnut-chart">
              <h2>Departments Overview</h2>
              <div><canvas id="doughnut"></canvas></div>
            </div>
        </div>
    </div>
    <script>
        var userRoleData = <?php echo $userRoleDataJson; ?>;
        var departmentCategoryData = <?php echo $departmentCategoryDataJson; ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
    <script src="chart1.js"></script>
    <script src="chart2.js"></script>
</body>
</html>
