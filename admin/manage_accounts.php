<?php
include('../includes/db.php');
$count = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    if (empty($username) || empty($_POST['password']) || empty($role)) {
        $error_message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            $success_message = "Account created successfully.";
        } else {
            $error_message = "Error creating account: " . $stmt->error;
        }
    }
}

if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $success_message = "User deleted successfully.";
    } else {
        $error_message = "Failed to delete user.";
    }
}

$result = $conn->query("SELECT id, username, role FROM users ORDER BY role, username");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
    <script>
    function filterTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('table tr');
        rows.forEach(row => {
            const username = row.querySelector('.username');
            if (username) {
                const usernameText = username.textContent.toLowerCase();
                row.style.display = usernameText.includes(searchInput) ? '' : 'none';
            }
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('searchInput').addEventListener('keyup', filterTable);
    });
    </script>
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
        .scrollable-table {
            max-height: 500px;
            overflow-y: auto;
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
    <div class="card">
        <h2 class="text-center">Manage User Accounts</h2>
    </div>

    <?php if (isset($success_message)) { echo "<div class='success-message'>{$success_message}</div>"; } ?>
    <?php if (isset($error_message)) { echo "<div class='error-message'>{$error_message}</div>"; } ?>

    <div class="actions">
        <button onclick="location.href='create_account.php'"><i class="fas fa-plus"></i> ADD</button>
        <div class="search-container">
            <input type="text" placeholder="Search by username..." id="searchInput">
        </div>
    </div>

    <div class="table-container">
        <h3>All Users</h3>
        <div class="scrollable-table">
        <table>
            <tr>
                <th>NO</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php while ($user = $result->fetch_assoc()) { ?>
                <?php $count++; ?>
                <tr>
                    <td class="no"><?php echo $count; ?></td>
                    <td class="username"><?php echo htmlspecialchars($user['username']); ?></td>
                    <td class="role"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
                    <td>
                        <a href="edit_account.php?id=<?php echo $user['id']; ?>"><i class="fas fa-edit"></i></a>
                        <a href="manage_accounts.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    </div>
</div>
</div>
</body>
</html>
