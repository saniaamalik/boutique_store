<?php
// ===============================
// DATABASE CONNECTION (LIVE / HOSTING)
// ===============================

$servername = "localhost"; //server name ha
$username   = "root";//db ka username
$password   = "";//password
$dbname     = "db_boutique";//db name

// CREATE CONNECTION
$conn = new mysqli($servername, $username, $password, $dbname);//mysql ka object ha jisse actual ma connection hota ha

// CHECK CONNECTION
if ($conn->connect_error) { //ye error check kerta ha agar connection fail ho jaye to
    die("Connection failed: " . $conn->connect_error);//scrpt ko stop kr k msg show kerta ha
}

// SET CHARSET (IMPORTANT)
$conn->set_charset("utf8mb4"); // utf8mb4 = full Unicode support
// is se Urdu, emojis, Arabic sab database me sahi save hota hai
?>