<?php
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complaint_id'])) {
    $complaint_id = $_POST['complaint_id'];
    $status = $_POST['status'];
    $response = trim($_POST['response']);

    if ($status === 'Reviewed' && empty($response)) {
        $response = null;
    }

    $update_stmt = $conn->prepare("UPDATE complaints SET status = ?, response = ?, updated_at = NOW() WHERE id = ?");
    $update_stmt->bind_param("ssi", $status, $response, $complaint_id);

    if ($update_stmt->execute()) {
        echo "<p class='success-message'>Complaint updated successfully.</p>";
    } else {
        echo "<p class='error-message'>Error updating complaint: " . $update_stmt->error . "</p>";
    }
}

$sql = "SELECT complaints.*, students.full_name 
        FROM complaints 
        JOIN students ON complaints.student_id = students.user_id 
        ORDER BY complaints.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Complaints</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            animation: fadeIn 1.5s ease-in-out;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #299B63;
            color: white;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        select, textarea, button {
            font-size: 14px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:disabled {
            background-color: #aaa;
            cursor: not-allowed;
        }

        button:hover:enabled {
            background-color: #0056b3;
        }

        .success-message, .error-message {
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <?php include "../dashboard.php"; ?>
    <div class="main">
        <h1>Student Complaints</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Complaint</th>
                        <th>Status</th>
                        <th>Response</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['full_name']); ?></td>
                            <td><?= htmlspecialchars($row['complaint']); ?></td>
                            <td><?= htmlspecialchars($row['status']); ?></td>
                            <td><?= htmlspecialchars($row['response']); ?></td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="complaint_id" value="<?= htmlspecialchars($row['id']); ?>">
                                    <select name="status" onchange="toggleResponse(this, 'button-<?= $row['id'] ?>')">
                                        <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Reviewed" <?= $row['status'] === 'Reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                    </select>
                                    <textarea name="response" rows="2" placeholder="Enter your response..."><?= htmlspecialchars($row['response']); ?></textarea>
                                    <button type="submit" id="button-<?= $row['id'] ?>" <?= $row['status'] !== 'Reviewed' ? 'disabled' : ''; ?>>Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No complaints found.</p>
        <?php endif; ?>
    </div>

    <script>
        function toggleResponse(selectElement, buttonId) {
            const button = document.getElementById(buttonId);
            button.disabled = selectElement.value !== 'Reviewed';
        }
    </script>
</body>
</html>
