<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function isAuthenticated() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function checkRole($requiredRole) {
    if (!isAuthenticated()) {
        header("Location: ../index.php?error=not_authenticated");
        exit();
    }

    if ($_SESSION['role'] !== $requiredRole) {
        header("Location: ../index.php?error=unauthorized");
        exit();
    }
}

function checkAdmin() {
    checkRole('admin');
}

function checkCoordinator() {
    checkRole('coordinator');
}

function checkDepartmentHead() {
    checkRole('department_head');
}

function checkStudent() {
    checkRole('student');
}

function login($user_id, $role) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $role;
}
?>
