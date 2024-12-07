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

    $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->bind_param('i', $department_id);

    if ($stmt->execute()) {
        header("Location: manage_departments.php?message=Department deleted successfully");
        exit();
    } else {
        echo "Error deleting department.";
    }
} else {
    echo "Department ID is required.";
}
?>
