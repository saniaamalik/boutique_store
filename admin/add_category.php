<?php
session_start();
require("../config/db.php");

// ONLY ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

$error = "";
$success = "";

/* ADD CATEGORY */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);

    // 🔴 VALIDATION
    if ($name == "") {
        $error = "Category name required!";
    }
    else {

        // 🔵 CHECK DUPLICATE (case-insensitive)
        $check = $conn->prepare("SELECT id FROM categories WHERE LOWER(name)=LOWER(?)");
        $check->bind_param("s", $name);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Category already exists!";
        }
        else {

            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);

            if ($stmt->execute()) {
                $success = "Category Added Successfully!";
            } else {
                $error = "Failed to add category!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Category</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;
}

.container{
    width:95%; /* Mobile par side margins kam rakhne ke liye */
    max-width:1200px;
    margin:auto;
    text-align:center;
}

h2{
    color:#6a0dad;
    margin-top:25px;
    font-size: 1.8rem;
}

/* ⭐ RESPONSIVE FORM BOX */
.form-box{
    width: 90%;          /* Mobile screens par 90% width */
    max-width: 450px;    /* Bari screen par 450px se upar nahi jayega */
    margin: 40px auto;
    background: #f3e8ff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1); /* Thora shadow depth ke liye */
    box-sizing: border-box; /* Padding ko width ke andar rakhne ke liye */
}

input{
    width:100%;
    box-sizing:border-box;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    border:1px solid #ddd;
    font-size:14px;
    outline: none;
}

button{
    width:100%;
    padding:12px;
    background:#6a0dad;
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight: bold;
    font-size: 16px;
    transition: 0.3s;
}

button:hover{
    background:#4b0082;
}

/* Tablet aur Mobile ke liye adjustments */
@media (max-width: 480px) {
    h2 {
        font-size: 1.5rem;
    }
    .form-box {
        padding: 20px;
        margin: 20px auto;
    }
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <h2>Add New Category</h2>

    <div class="form-box">

        <?php if($error) echo "<p style='color:red; font-size:14px;'>$error</p>"; ?>
        <?php if($success) echo "<p style='color:green; font-size:14px;'>$success</p>"; ?>

        <form method="POST">

            <input type="text" name="name" placeholder="Category Name" required>

            <button type="submit">Add Category</button>

        </form>

    </div>

</div>

</body>
</html>