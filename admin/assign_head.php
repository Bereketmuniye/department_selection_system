<?php
include "../includes/db.php";

$query_dept = "SELECT id, department_name FROM departments WHERE id NOT IN (SELECT department_id FROM head_assignment)";
$result_dept = mysqli_query($conn, $query_dept);

$query_heads = "SELECT id, username FROM users WHERE (role = 'head' OR role = 'department_head') AND id NOT IN (SELECT head_id FROM head_assignment)";
$result_heads = mysqli_query($conn, $query_heads);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_id = $_POST['department_id'];
    $head_id = $_POST['head_id'];

    if ($department_id && $head_id) {
        $query_insert = "INSERT INTO head_assignment (department_id, head_id) VALUES ('$department_id', '$head_id')";
        if (mysqli_query($conn, $query_insert)) {
            $success_message = "Department head assigned successfully!";
        } else {
            $error_message = "Error assigning department head: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Please select both department and head.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Department Head</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom, #a8e063, #56ab2f);
            padding: 10px;
        }
        .m{
            margin-top: 50px;
            margin-left: 200px;
            width: 90%;
            max-width: 900px;
            background-color: white;
            animation: fadeIn 1.5s ease-in-out;
        }
        .f{
            padding: 20px;
        }
        .error_message{
            color: red;
        }
        .success-message{
            color: green;
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
        <div class="m">
            <div class="f">
    <h2>Assign Department Head</h2>

    <?php if (isset($error_message)) { echo "<div class='error-message'>{$error_message}</div>"; } ?>
    <?php if (isset($success_message)) { echo "<div class='success-message'>{$success_message}</div>"; } ?>

    <form method="POST" action="">
        <label for="department_id">Select Department:</label>
        <select name="department_id" id="department_id" required>
            <option value="">--Select Department--</option>
            <?php
            // Populate department options
            while ($row_dept = mysqli_fetch_assoc($result_dept)) {
                echo "<option value='" . $row_dept['id'] . "'>" . $row_dept['department_name'] . "</option>";
            }
            ?>
        </select>

        <br><br>

        <label for="head_id">Select Department Head:</label>
        <select name="head_id" id="head_id" required>
            <option value="">--Select Head--</option>
            <?php
            // Populate head options
            while ($row_head = mysqli_fetch_assoc($result_heads)) {
                echo "<option value='" . $row_head['id'] . "'>" . $row_head['username'] . "</option>";
            }
            ?>
        </select>

        <br><br>

        <button type="submit">Assign</button>
    </form>
    </div>
    </div>
    </div>
</body>
</html>
