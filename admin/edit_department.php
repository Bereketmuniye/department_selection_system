<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['id'])) {
    $department_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM departments WHERE id = ?");
    $stmt->bind_param('i', $department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $department = $result->fetch_assoc();

    if (!$department) {
        die('Department not found.');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $department_name = $_POST['department_name'];
        $capacity = $_POST['capacity'];
        $category = $_POST['category'];

        if (empty($department_name) || !is_numeric($capacity) || $capacity <= 0) {
            $error_message = "Invalid department data.";
        } else {
            $stmt = $conn->prepare("UPDATE departments SET department_name = ?, capacity = ?, category = ? WHERE id = ?");
            $stmt->bind_param('sisi', $department_name, $capacity, $category, $department_id);

            if ($stmt->execute()) {
                $success_message = "Department updated successfully.";
            } else {
                $error_message = "Error updating department.";
            }
        }
    }
} else {
    die('Department ID is required.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom, #a8e063, #56ab2f);
            padding: 10px;
        }
        .d-main {
    width: 100%; 
    max-width: 500px;
    max-height: 500px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-left: 300px;
    margin-top: 10px;
    animation: fadeIn 1.5s ease-in-out;
}

h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 20px;
}

.success-message, .error-message {
    padding: 10px;
    margin-bottom: 15px;
    text-align: center;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    width: 100%;
}

.success-message {
    background-color: #28a745;
    color: white;
}

.error-message {
    background-color: #dc3545;
    color: white;
}

form {
    max-width: 600px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

label {
    font-size: 16px;
    color: #333;
}

input[type="text"], input[type="number"], select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 100%;
}

input[type="text"]:focus, input[type="number"]:focus, select:focus {
    border-color: #007bff;
    outline: none;
}

button {
    background-color: #2ecc71;
    color: white;
    padding: 12px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #27ae60;
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

@media screen and (max-width: 768px) {
    .main {
        width: 90%;
    }
    
    form {
        width: 100%;
    }
}

    </style>
</head>
<body>
    <?php include '../dashboard.php'; ?>
    <div class="main">
    <div class="d-main">
        <h2>Edit Department</h2>

        <?php if (isset($success_message)) { echo "<div class='success-message'>{$success_message}</div>"; } ?>
        <?php if (isset($error_message)) { echo "<div class='error-message'>{$error_message}</div>"; } ?>

        <form method="post">
            <label for="department_name">Department Name:</label>
            <input type="text" id="department_name" name="department_name" value="<?php echo htmlspecialchars($department['department_name']); ?>" required>

            <label for="capacity">Capacity:</label>
            <input type="number" id="capacity" name="capacity" value="<?php echo htmlspecialchars($department['capacity']); ?>" required>

            <label for="category">Category:</label>
            <select name="category" required>
                <option value="Technology" <?php echo ($department['category'] == 'Technology') ? 'selected' : ''; ?>>Technology</option>
                <option value="Natural and Computational Science" <?php echo ($department['category'] == 'Natural and Computational Science') ? 'selected' : ''; ?>>Natural and Computational Science</option>
                <option value="Agriculture and Natural Resource" <?php echo ($department['category'] == 'Agriculture and Natural Resource') ? 'selected' : ''; ?>>Agriculture and Natural Resource</option>
                <option value="Business and Economics" <?php echo ($department['category'] == 'Business and Economics') ? 'selected' : ''; ?>>Business and Economics</option>
                <option value="Health Science" <?php echo ($department['category'] == 'Health Science') ? 'selected' : ''; ?>>Health Science</option>
                <option value="School of Law" <?php echo ($department['category'] == 'School of Law') ? 'selected' : ''; ?>>School of Law</option>
                <option value="Social Science and Humanities" <?php echo ($department['category'] == 'Social Science and Humanities') ? 'selected' : ''; ?>>Social Science and Humanities</option>
            </select>

            <button type="submit">Update Department</button>
        </form>
    </div>
</div>
</body>
</html>
