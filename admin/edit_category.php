<?php
session_start();
require("../config/db.php");

// ONLY ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

/* VALIDATE ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid category ID!");
}

$id = intval($_GET['id']);

/* GET CATEGORY (SAFE) */
$stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

if (!$category) {
    die("Category not found!");
}

$error = "";
$success = "";

/* UPDATE CATEGORY */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);

    if ($name == "") {
        $error = "Category name required!";
    } else {

        $update = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
        $update->bind_param("si", $name, $id);
        $update->execute();

        $success = "Category Updated Successfully!";

        // refresh data safely
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
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;
}

.container{
    width:95%; /* Mobile ke liye side margins */
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
    width: 90%;           /* Mobile par width automatic 90% ho jaye */
    max-width: 400px;     /* Desktop par 400px se bada na ho */
    margin: 40px auto;
    background: #f3e8ff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    box-sizing: border-box; /* Padding ko width ke andar rakhne ke liye */
}

input{
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 10px;
    border: 1px solid #ddd;
    box-sizing: border-box;
    outline: none;
    font-size: 14px;
}

button{
    width: 100%;
    padding: 12px;
    background: #6a0dad;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    transition: 0.3s;
}

button:hover{
    background: #4b0082;
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

    <h2>Edit Category</h2>

    <div class="form-box">

        <?php if($error): ?>
            <p style="color:red; font-size:14px;"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if($success): ?>
            <p style="color:green; font-size:14px;"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="POST">

            <input type="text" name="name" value="<?= htmlspecialchars($category['name']); ?>" required>

            <button type="submit">Update Category</button>

        </form>

    </div>

</div>

</body>
</html>