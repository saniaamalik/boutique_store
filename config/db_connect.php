<?php
// ===============================
// DATABASE CONNECTION (LIVE / HOSTING)
// ===============================

$servername = "localhost"; // InfinityFree me change hoga
$username   = "root";
$password   = "";
$dbname     = "db_boutique";

// CREATE CONNECTION
$conn = new mysqli($servername, $username, $password, $dbname);

// CHECK CONNECTION
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SET CHARSET (IMPORTANT)
$conn->set_charset("utf8mb4");
?>