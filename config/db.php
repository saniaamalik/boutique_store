<?php
// ===============================
// MAIN CONFIG FILE (PROJECT USE)
// ===============================
// ye file pory project ki basic setting ker rahi ha

// START SESSION SAFELY
if (session_status() === PHP_SESSION_NONE) {//seesion already start ha ya ni check kerta ha
    session_start();//  naya session start kerta ha
}

// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "db_boutique");// db connection

// CHECK CONNECTION
if ($conn->connect_error) {//error check if connection fail
    die("Database connection failed: " . $conn->connect_error);
}

// CHARSET FIX
$conn->set_charset("utf8mb4");//her charchter ko store kerta ha

// ===============================
// HELPER FUNCTIONS
// ===============================

// CLEAN OUTPUT
// xss attack se bacahata ha
//safe input leta ha or html tags ko simple or safe text ma kerdeta ha
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');//trim remove extra spaces
}

// LOGIN CHECK
function isLoggedIn() {//user login ha ya ni
    return isset($_SESSION['user_id']);//user id set in session return true
}

// ADMIN CHECK
function isAdmin() {//user admin ha ya ni
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');//admin ha to true
}

// REDIRECT FUNCTION
function redirect($page) {
    header("Location: $page");//new page open kerna 
    exit();//code agy execute na ho
}
?>