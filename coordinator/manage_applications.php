<?php
include('../includes/db.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $application_id = $_POST['application_id'];
    $student_id = $_POST['student_id'];
    $preferences = explode(',', $_POST['department_preferences']);

    // Fetch student details including gender and disability
    $student_query = $conn->prepare("SELECT user_id, cgpa, disability, gender FROM students WHERE user_id = ?");
    $student_query->bind_param("i", $student_id);
    $student_query->execute();
    $student_result = $student_query->get_result();

    if ($student_result->num_rows > 0) {
        $student = $student_result->fetch_assoc();
        $student_cgpa = $student['cgpa'];
        $is_disabled = $student['disability'];
        $gender = $student['gender'];

        // Convert CGPA to Percentage
        $student_percentage = $student_cgpa * 25; // Convert CGPA to percentage

        // Adjust percentage based on disability and gender
        $additional_percentage = 0;

        if ($is_disabled) {
            $additional_percentage += 5; // Add 5% for disability
        }
        if (strtolower($gender) == 'female') {
            $additional_percentage += 5; // Add 5% for being female
        }

        $student_percentage += $additional_percentage; // Apply total adjustments
        $student_percentage = min($student_percentage, 100); // Ensure percentage does not exceed 100%

        $assigned_department = null;

        foreach ($preferences as $preference) {
            $req_query = $conn->prepare("SELECT minimum_cgpa, capacity FROM department_requirements 
                                         JOIN departments ON department_requirements.department_id = departments.id 
                                         WHERE department_id = ?");
            $req_query->bind_param("i", $preference);
            $req_query->execute();
            $req_result = $req_query->get_result();

            if ($req_result->num_rows > 0) {
                $dept_req = $req_result->fetch_assoc();
                // Convert department minimum CGPA to percentage for comparison
                $minimum_percentage = $dept_req['minimum_cgpa'] * 25;

                if ($student_percentage >= $minimum_percentage && $dept_req['capacity'] > 0) {
                    $assigned_department = $preference;
                    break;
                }
            }
        }

        if ($assigned_department) {
            $update_stmt = $conn->prepare("UPDATE applications SET status = 'Approved', assigned_department_id = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $assigned_department, $application_id);

            if ($update_stmt->execute()) {
                $capacity_stmt = $conn->prepare("UPDATE departments SET capacity = capacity - 1 WHERE id = ?");
                $capacity_stmt->bind_param("i", $assigned_department);
                $capacity_stmt->execute();

                $dept_name_query = $conn->prepare("SELECT department_name FROM departments WHERE id = ?");
                $dept_name_query->bind_param("i", $assigned_department);
                $dept_name_query->execute();
                $dept_name_result = $dept_name_query->get_result();

                if ($dept_name_result->num_rows > 0) {
                    $dept_name_row = $dept_name_result->fetch_assoc();
                    $assigned_department_name = $dept_name_row['department_name'];

                    $update_student_dept = $conn->prepare("UPDATE students SET department = ? WHERE user_id = ?");
                    $update_student_dept->bind_param("si", $assigned_department_name, $student_id);
                    $update_student_dept->execute();
                }

                $success_message = "Application status updated and department assigned successfully.";
            } else {
                $error_message = "Error updating status: " . $update_stmt->error;
            }
        } else {
            $error_message = "Student does not meet CGPA criteria or capacity limit for selected departments.";
        }
    } else {
        $error_message = "Student not found in the database.";
    }
}

$applications_query = "SELECT * FROM applications";
$applications_result = $conn->query($applications_query);

$departments_query = "SELECT * FROM departments";
$departments_result = $conn->query($departments_query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Applications</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .success-message {
            color: green;
            font-weight: bold;
            margin: 10px 0;
        }
        .error-message {
            color: red;
            font-weight: bold;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .scrollable-table {
            max-height: 200px;
            overflow-y: auto;
        }
        .assign-button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .assign-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .ma{
            width: 100%;
            max-width: 100%;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            height: 100vh;
        }
     .ma input[type="number"], button[type="submit"], select {
    width: 50%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    margin-bottom: 10px;
}
.f{
    display: flex;
    flex-direction: horizontal;
}
.ma {
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
    <div class="ma">
    <h2>Manage Applications</h2>

    <?php if (!empty($success_message)): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
    <?php elseif (!empty($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>

        <h3 style="padding-top: 20px; color: #2ecc71;">Set Department CGPA Requirements</h3>
    <form method="POST" class="f">
        <select name="department_id" required>
            <option value="">Select Department</option>
            <?php while ($dept = $departments_result->fetch_assoc()): ?>
                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
            <?php endwhile; ?>
        </select>
        <input type="number" step="0.01" name="cgpa" placeholder="Minimum CGPA" required>
        <button type="submit" name="set_requirements">Set Requirement</button>
    </form>

    <div>
        <h3 style="padding-top: 20px; color: #2ecc71;">Unassigned Student</h3>
        <div class="scrollable-table">
            <table>
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Student ID</th>
                        <th>Preferences</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $applications_result->fetch_assoc()): ?>
                        <?php if ($row['status'] == 'Pending'): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['department_preferences']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="application_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
                                        <input type="hidden" name="department_preferences" value="<?php echo $row['department_preferences']; ?>">
                                        <button type="submit" name="update_status" class="assign-button">Assign</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h3 style="padding-top: 20px; color: #2ecc71;">Assigned Students</h3>
        <div class="scrollable-table">
            <table>
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Student ID</th>
                        <th>Preferences</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $applications_result->data_seek(0);
                    while ($row = $applications_result->fetch_assoc()): ?>
                        <?php if ($row['status'] == 'Approved'): ?>

                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <?php $id = htmlspecialchars($row['student_id']); ?>
                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                <td>                                <?php 
                                    $prefs = explode(',', $row['department_preferences']);
                                    foreach ($prefs as $pref) {
                                        $dept_query = $conn->prepare("SELECT department_name FROM departments WHERE id = ?");
                                        $dept_query->bind_param("i", $pref);
                                        $dept_query->execute();
                                        $dept_result = $dept_query->get_result();
                                        if ($dept_result->num_rows > 0) {
                                            $dept = $dept_result->fetch_assoc();
                                            echo htmlspecialchars($dept['department_name']) . '<br>';
                                        }
                                    }
                                ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <button class="assign-button" disabled>Assigned</button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
</body>
</html>
