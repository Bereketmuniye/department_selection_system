<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

function fetch_departments($conn, $category) {
    $stmt = $conn->prepare("SELECT id, department_name FROM departments WHERE category = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    return $stmt->get_result();
}

function fetch_student_stream($conn, $user_id) {
    $stmt = $conn->prepare("SELECT stream FROM students WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $data['stream'] ?? null;
}

function has_submitted_preferences($conn, $student_id) {
    $count = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM applications WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    return $count > 0;
}

function save_application($conn, $student_id, $preferences) {
    $stmt = $conn->prepare("INSERT INTO applications (student_id, department_preferences, status, created_at) VALUES (?, ?, 'Pending', NOW())");
    $preferences_str = implode(',', $preferences);
    $stmt->bind_param("is", $student_id, $preferences_str);
    return $stmt->execute();
}

$departments = [];
$message = "";
$message_color = "red";
$selected_preferences = [];

if (has_submitted_preferences($conn, $user_id)) {
    $message = "You have already submitted your department preferences.";
    $message_color = "orange";
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['year']) && isset($_POST['semester'])) {
            $year = $_POST['year'];
            $semester = $_POST['semester'];
            $student_stream = fetch_student_stream($conn, $user_id);

            if (!$student_stream) {
                $message = "Invalid student or stream information not found.";
            } else {
                if ($student_stream == 'natural' && $year == '1' && $semester == '2') {
                    $departments['Technology'] = fetch_departments($conn, 'Technology');
                    $departments['Health Science'] = fetch_departments($conn, 'Health Science');
                    $departments['Natural and Computational Science'] = fetch_departments($conn, 'Natural and Computational Science');
                    $departments['Agriculture and Natural Resource'] = fetch_departments($conn, 'Agriculture and Natural Resource');
                } elseif ($student_stream == 'social' && $year == '1' && $semester == '2') {
                    $departments['Business and Economics'] = fetch_departments($conn, 'Business and Economics');
                    $departments['School of Law'] = fetch_departments($conn, 'School of Law');
                    $departments['Social Science and Humanities'] = fetch_departments($conn, 'Social Science and Humanities');
                } elseif ($student_stream == 'natural' && $year == '2' && $semester == '1') {
                    $departments['Health Science'] = fetch_departments($conn, 'Health Science');
                    $departments['Natural and Computational Science'] = fetch_departments($conn, 'Natural and Computational Science');
                } elseif ($student_stream == 'natural' && $year == '2' && $semester == '2') {
                    $departments['Engineering'] = [
                        ['id' => 1, 'department_name' => 'Software Engineering'],
                        ['id' => 2, 'department_name' => 'Mechanical Engineering'],
                        ['id' => 3, 'department_name' => 'Bio-Medical Engineering'],
                        ['id' => 4, 'department_name' => 'Architecture Engineering']
                    ];
                } else {
                    $message = ($student_stream == 'social') ? 
                        "You are a social science student, this selection is not applicable to you." : 
                        "This selection is not applicable to your current year and semester.";
                }
            }
        } elseif (isset($_POST['preferences'])) {
            $selectedOrder = explode(',', $_POST['order']);
            $preferences = array_intersect($selectedOrder, $_POST['preferences']);
            if (count($preferences) > 0 && save_application($conn, $user_id, $preferences)) {
                $message = "Your department preferences have been saved successfully!";
                $message_color = "green";
            } else {
                $message = "Failed to save your preferences. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Department</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>

    .content {
            animation: fadeIn 1.5s ease-in-out;
    }
        h1 {
            text-align: center;
            color: #0056b3;
        }
        form label, form select, form button {
            display: block;
            margin: 10px 0;
            width: 100%;
        }
        form button {
            background: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }
        form button:hover {
            background: #0056b3;
        }
        .message {
            font-weight: bold;
            text-align: center;
            color: <?php echo $message_color; ?>;
            background-color: #fff;
        }
        .departments { margin-top: 20px; }
        .departments ul { list-style: none; padding: 0; }
        .departments ul li { display: flex; align-items: center; margin-bottom: 10px; }
        .departments ul li label { flex: 1; }
        .selected-preferences { margin-top: 20px; }
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
<div class="content">
    <form method="POST" style=" width: 90%;
            max-width: 80%;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-left: 10%;">         
    <h1>Select Your Department</h1>
    <div class="message"><?= htmlspecialchars($message) ?></div>
        <label for="year">Select Year:</label>
        <select name="year" id="year" required>
            <option value="" disabled selected>Select Year</option>
            <option value="1">First Year</option>
            <option value="2">Second Year</option>
        </select>

        <label for="semester">Select Semester:</label>
        <select name="semester" id="semester" required>
            <option value="" disabled selected>Select Semester</option>
            <option value="1">First Semester</option>
            <option value="2">Second Semester</option>
        </select>

        <button type="submit">Submit</button>
    </form>

    <?php if ($departments): ?>
        <form method="POST" id="preferencesForm" style="            width: 90%;
            max-width: 80%;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-left: 10%;">
            <h2>Select Departments in Order of Preference</h2>
            <ul id="departmentList">
                <?php foreach ($departments as $category => $depts): ?>
                    <h3><?= htmlspecialchars($category) ?></h3>
                    <?php foreach ($depts as $dept): ?>
                        <li>
                            <label>
                                <input type="checkbox" name="preferences[]" value="<?= htmlspecialchars($dept['id']) ?>" onchange="updateOrder(this)">
                                <?= htmlspecialchars($dept['department_name']) ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
            <h3>Your Selected Preferences</h3>
            <ul id="selectedPreferences" class="selected-preferences"></ul>
            <input type="hidden" name="order" id="orderInput">
            <button type="submit">Save Preferences</button>
        </form>
    <?php endif; ?>
</div>
</div>
<script>
    let selectedOrder = [];

    function updateOrder(checkbox) {
        const selectedPreferences = document.getElementById('selectedPreferences');
        const checkboxValue = checkbox.value;

        if (checkbox.checked) {
            selectedOrder.push(checkboxValue);
        } else {
            selectedOrder = selectedOrder.filter(item => item !== checkboxValue);
        }

        selectedPreferences.innerHTML = '';
        selectedOrder.forEach(value => {
            const listItem = document.createElement('li');
            listItem.textContent = document.querySelector(`input[value="${value}"]`).parentNode.textContent.trim();
            selectedPreferences.appendChild(listItem);
        });

        document.getElementById('orderInput').value = selectedOrder.join(',');
    }

    document.getElementById('preferencesForm').addEventListener('submit', function(event) {
        const checkboxes = Array.from(document.querySelectorAll('input[name="preferences[]"]:checked'));
        if (checkboxes.length === 0) {
            alert('Please select at least one department.');
            event.preventDefault();
        }
    });
</script>
</body>
</html>