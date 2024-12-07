<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$student_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaint_id'], $_POST['status'])) {
    $complaint_id = $_POST['complaint_id'];
    $status = $_POST['status'];
    $response = trim($_POST['response']) ?? null;

    if ($status === 'Resolved') {
        $update_query = "UPDATE complaints SET status = 'Resolved', updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $complaint_id);
        $stmt->execute();
    } elseif ($status === 'Not Resolved' && $response) {
        $update_query = "UPDATE complaints SET complaint = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $response, $complaint_id);
        $stmt->execute();
    }
}

$sql = "SELECT * FROM complaints WHERE student_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Complaints</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .main {
            padding: 20px;
            max-width: 1400px;
            margin: auto;
        }

        .m {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            animation: fadeIn 1.5s ease-in-out;
        }

        h1 {
            text-align: center;
            color: #299B63;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #299B63;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e9f9ec;
        }

        .response-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        select, textarea, button {
            font-size: 14px;
            padding: 10px;
            border: 1px solid #ddd;
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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
<?php include '../dashboard.php'; ?>
<div class="main">
    <div class="m">
    <h1>Your Complaints</h1>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Complaint</th>
                    <th>Status</th>
                    <th>Response</th>
                    <th>Submitted At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['complaint']); ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                        <td><?= htmlspecialchars($row['response']); ?></td>
                        <td><?= htmlspecialchars($row['created_at']); ?></td>
                        <td><?= htmlspecialchars($row['updated_at']); ?></td>
                        <td>
                            <form method="POST" class="response-form">
                                <input type="hidden" name="complaint_id" value="<?= $row['id']; ?>">
                                <?php if ($row['status'] === 'Resolved'): ?>
                                    <button type="button" disabled>Resolved</button>
                                <?php else: ?>
                                    <select name="status" onchange="toggleFields(this, 'textarea-<?= $row['id']; ?>', 'submit-<?= $row['id']; ?>')">
                                        <option value="Not Resolved">Not Resolved</option>
                                        <option value="Resolved">Resolved</option>
                                    </select>
                                    <textarea id="textarea-<?= $row['id']; ?>" name="response" placeholder="Enter your response..." required></textarea>
                                    <button type="submit" id="submit-<?= $row['id']; ?>">Submit</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have not submitted any complaints.</p>
    <?php endif; ?>
    </div>
</div>
<script>
    function toggleFields(selectElement, textareaId, buttonId) {
        const textarea = document.getElementById(textareaId);
        const button = document.getElementById(buttonId);

        if (selectElement.value === "Resolved") {
            textarea.style.display = "none";
            button.disabled = true;
            button.form.submit();
        } else {
            textarea.style.display = "block";
            button.disabled = false;
        }
    }
</script>
</body>
</html>
