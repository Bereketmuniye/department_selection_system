<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .main {
            padding: 40px;
            background-color: #f4f8fb;
            min-height: 100vh;
            box-sizing: border-box;
            text-align: center;
            overflow: hidden;
        }

        .welcome-text {
            font-size: 2.5em;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            animation: fadeIn 2s ease-in-out;
        }

        .subtitle {
            font-size: 1.2em;
            color: #555;
            margin-bottom: 40px;
            animation: slideIn 2.5s ease-in-out;
        }

        .decorative {
            height: 4px;
            width: 80%;
            margin: 0 auto 20px;
            background: linear-gradient(90deg, #007bff, #28a745, #ffc107, #dc3545);
            background-size: 400% 400%;
            animation: gradientShift 6s infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
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

        .action-buttons {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .action-buttons a {
            text-decoration: none;
            font-size: 1em;
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .action-buttons a:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .welcome-text {
                font-size: 2em;
            }

            .subtitle {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>

<?php include '../dashboard.php'; ?>

<div class="main">
    <div class="welcome-text">
        Welcome, Coordinator
    </div>
    <div class="subtitle">
        Manage department assignments, manage student applications, and generate reports efficiently from here.
    </div>
    <div class="decorative"></div>

    <div class="action-buttons">
        <a href="manage_applications.php">Assign Departments</a>
        <a href="see_assignments.php">View Assignments</a>
        <a href="generate_reports.php">View Reports</a>
    </div>
</div>

</body>
</html>
