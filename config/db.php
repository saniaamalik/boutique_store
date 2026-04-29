<?php
// ===============================
// MAIN CONFIG FILE (PROJECT USE)
// ===============================

// START SESSION SAFELY
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "db_boutique");

// CHECK CONNECTION
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// CHARSET FIX
$conn->set_charset("utf8mb4");

// ===============================
// HELPER FUNCTIONS
// ===============================

// CLEAN OUTPUT
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// LOGIN CHECK
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// ADMIN CHECK
function isAdmin() {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

// REDIRECT FUNCTION
function redirect($page) {
    header("Location: $page");
    exit();
}
?>