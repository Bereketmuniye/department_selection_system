<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_name = $_POST['department_name'];
    $capacity = $_POST['capacity'];
    $category = $_POST['category'];

   
    if (empty($department_name) || !is_numeric($capacity) || $capacity <= 0) {
        $error_message = "Invalid department data.";
    } else {
        $stmt = $conn->prepare("INSERT INTO departments (department_name, capacity, category) VALUES (?, ?, ?)");
        $stmt->bind_param('sis', $department_name, $capacity, $category); 

        if ($stmt->execute()) {
            $success_message = "Department created successfully.";
        } else {
            $error_message = "Error creating department.";
        }
    }
}

$result = $conn->query("SELECT * FROM departments ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>          
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom, #a8e063, #56ab2f);
            padding: 5px;
        }
        .m{
            animation: fadeIn 1.5s ease-in-out;
        }
        .fa-trash-alt{
            color: red;
        }
        .fa-edit{
            color: orange;
        }
        .dm-table-container{
            background-color: white;
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
        <div class="m">
        <h2 class="dm-header">Manage Departments</h2>

        <?php if (isset($success_message)) { echo "<div class='success-message'>{$success_message}</div>"; } ?>
        <?php if (isset($error_message)) { echo "<div class='error-message'>{$error_message}</div>"; } ?>

        <div class="dm-header-actions">
            <button class="dm-add-btn" onclick="toggleModal('createForm')"><i class="fas fa-plus"></i> ADD</button>
            
            <div class="dm-search-container">
                <input type="text" id="searchInput" placeholder="Search by department name..." onkeyup="filterTable()">
            </div>
        </div>
        <div class="dm-table-container">
            <table class="dm-department-table">
                <thead>
                    <tr>
                        <th class="dm-table-header">No</th>
                        <th class="dm-table-header">Department Name</th>
                        <th class="dm-table-header">Capacity</th>
                        <th class="dm-table-header">Category</th>
                        <th class="dm-table-header">Created At</th>
                        <th class="dm-table-header">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="dm-table-cell"><?php echo $count++; ?></td>
                        <td class="dm-table-cell department-name"><?php echo htmlspecialchars($row['department_name']); ?></td>
                        <td class="dm-table-cell"><?php echo htmlspecialchars($row['capacity']); ?></td>
                        <td class="dm-table-cell"><?php echo htmlspecialchars($row['category']); ?></td>
                        <td class="dm-table-cell"><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                        <td class="dm-actions">
                            <a href="edit_department.php?id=<?php echo $row['id']; ?>"><i class="fas fa-edit"></i></a>
                            <a href="delete_department.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this department?');"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
    <div id="createForm" class="dm-modal">
        <span class="close" onclick="closeModal('createForm')">&times;</span>
        <div class="dm-modal-content">
            <h3>Create New Department</h3>
            <form method="POST">
                <label for="department_name">Department Name:</label>
                <input type="text" id="department_name" name="department_name" required>

                <label for="capacity">Capacity:</label>
                <input type="number" id="capacity" name="capacity" required>

                <label for="category">Category:</label>
                <select name="category" required>
                    <option value="Technology">Technology</option>
                    <option value="Natural and Computational Science">Natural and Computational Science</option>
                    <option value="Agriculture and Natural Resource">Agriculture and Natural Resource</option>
                    <option value="Business and Economics">Business and Economics</option>
                    <option value="Health Science">Health Science</option>
                    <option value="School of Law">School of Law</option>
                    <option value="Social Science and Humanities">Social Science and Humanities</option>
                </select>

                <button type="submit">Create Department</button>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function filterTable() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const departmentName = row.querySelector('.department-name').textContent.toLowerCase();
                row.style.display = departmentName.includes(searchInput) ? '' : 'none';
            });
        }
    </script>
</body>
</html>
