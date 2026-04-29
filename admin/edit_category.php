<?php
// Session start - 
session_start();
require("../config/db.php");

// SIRF ADMIN USERS KO ACCESS - Dusre ko bahar nikal do
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

/* ID KO VALIDATE KARO */
// Check karo ke URL me id exist karti hai aur number hai
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid category ID!");
}

// ID ko integer mein convert karo
$id = intval($_GET['id']);

/* DATABASE SE CATEGORY k data  LE LO - Safe query */
$stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

// Agar category nahi mila toh error
if (!$category) {
    die("Category not found!");
}

// Error aur success messages ke liye
$error = "";
$success = "";

/* CATEGORY KO UPDATE KARNE KA KAAM */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Form se category name le lo aur spaces hatao
    $name = trim($_POST['name']);

    // Check karo ke name empty toh nahi hai
    if ($name == "") {
        $error = "Category name required!";
    } else {

        // Database mein category update karo
        $update = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
        $update->bind_param("si", $name, $id);
        $update->execute();

        // Success message
        $success = "Category Updated Successfully!";

        // Updated data ko dobara le lo
        $stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $category = $stmt->get_result()->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Category</title>

<style>
/* Page ka background - Purple color */
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;  /* Light purple */
}

/* Main container - Center mein content */
.container{
    width:95%;
    max-width:1200px;
    margin:auto;
    text-align:center;
}

/* Heading - Bada title */
h2{
    color:#6a0dad;  /* Purple color */
    margin-top:25px;
    font-size: 1.8rem;
}

/* Form ka box - Category edit karne ke liye */
.form-box{
    width: 90%;  /* Mobile par width 90% */
    max-width: 400px;  /* Desktop par 400px se upar nahi */
    margin: 40px auto;
    background: #f3e8ff;  /* Light purple background */
    padding: 25px;  /* Inner spacing */
    border-radius: 15px;  /* Round corners */
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);  /* Shadow effect */
    box-sizing: border-box;  /* Padding ko width ke andar rakhne ke liye */
}

/* Input field styling - Text likha jaye */
input{
    width: 100%;  /* Puri width */
    padding: 12px;  /* Inner spacing */
    margin: 10px 0;  /* Outer spacing */
    border-radius: 10px;  /* Round corners */
    border: 1px solid #ddd;  /* Light border */
    box-sizing: border-box;
    outline: none;  /* Focus pe outline nahi */
    font-size: 14px;
}

/* Submit button */
button{
    width: 100%;
    padding: 12px;  /* Button ko bada banao */
    background: #6a0dad;  /* Purple button */
    color: white;  /* White text */
    border: none;
    border-radius: 10px;  /* Round button */
    cursor: pointer;  /* Cursor change */
    font-weight: bold;  /* Bold text */
    font-size: 16px;
    transition: 0.3s;  /* Smooth animation */
}

/* Jab mouse pe hoover karo */
button:hover{
    background: #4b0082;  /* Dark purple */
}

/* Mobile screens ke liye responsive design */
@media (max-width: 480px) {
    h2 {  /* Heading ko chhota banao */
        font-size: 1.5rem;
    }
    .form-box {  /* Form box ko adjust karo */
        padding: 20px;
        margin: 20px auto;
    }
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <!-- Category edit karne ka heading -->
    <h2>Edit Category</h2>

    <div class="form-box">

        <!-- Error message dikhao agar ho -->
        <?php if($error): ?>
            <p style="color:red; font-size:14px;"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- Success message dikhao agar ho -->
        <?php if($success): ?>
            <p style="color:green; font-size:14px;"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <!-- Category update form -->
        <form method="POST">

            <!-- Category name input - Pehly se likha howa name dikha do -->
            <input type="text" name="name" value="<?= htmlspecialchars($category['name']); ?>" required>

            <!-- Update button -->
            <button type="submit">Update Category</button>

        </form>

    </div>

</div>

</body>
</html>