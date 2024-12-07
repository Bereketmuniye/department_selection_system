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

$department_query = "SELECT departments.department_name, departments.capacity 
                     FROM head_assignment 
                     JOIN departments ON head_assignment.department_id = departments.id 
                     WHERE head_assignment.head_id = ?";
$stmt = $conn->prepare($department_query);
$stmt->bind_param("i", $head_id);
$stmt->execute();
$department_result = $stmt->get_result();
$department = $department_result->fetch_assoc();

$department_name = $department['department_name'] ?? 'N/A';
$department_capacity = $department['capacity'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Head Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k6pqA6Q90SOw9N4RZNRuXb8ZbJ4WqMYbUI6J6Zb0UpOOWIWYZiJG7j0+ikYdJ57rYklxvA9+yI2xg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom, #a8e063, #56ab2f);
            padding: 10px;
        }

        .content {
            max-width: 1200px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 20px;
            animation: fadeIn 1.5s ease-in-out;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .dashboard-header h1 {
            font-size: 2.5em;
            color: #299B63;
        }

        .dashboard-header p {
            font-size: 1.2em;
            color: #555;
        }

        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
            justify-content: space-around;
        }

        .card {
            flex: 1;
            min-width: 250px;
            max-width: 300px;
            background-color: #299B63;
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .card i {
            font-size: 3em;
            margin-bottom: 10px;
            color: #fff;
        }

        .card h2 {
            font-size: 2.2em;
            margin: 10px 0 0;
        }

        .card p {
            margin-top: 5px;
            font-size: 1.1em;
        }

        a.card-link {
            text-decoration: none;
            color: white;
            font-weight: bold;
            font-size: 1.2em;
            display: block;
            margin-top: 10px;
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
        <div class="content">
            <div class="dashboard-header">
                <h1>Welcome, Department Head</h1>
                <p>Manage your department and monitor performance with ease.</p>
            </div>

            <div class="cards-container">
                <div class="card">
                    <i class="fas fa-building"></i>
                    <h2><?= htmlspecialchars($department_name); ?></h2>
                    <p>Department Name</p>
                </div>
                <div class="card">
                    <i class="fas fa-users"></i>
                    <h2><?= htmlspecialchars($department_capacity); ?></h2>
                    <p>Department Capacity</p>
                </div>
                <div class="card">
                    <i class="fas fa-chart-line"></i>
                    <a href="reports.php" class="card-link">View Reports</a>
                    <p>Monitor Department Progress</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
