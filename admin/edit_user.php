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
    die("Invalid user ID!");
}

$id = intval($_GET['id']);

/* GET USER SAFELY */

$stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='customer'");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("User not found!");
}

$error = "";
$success = "";

/* UPDATE USER */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    if ($name == "" || $email == "" || $phone == "") {
        $error = "All fields required!";
    } else {

        /* IF PASSWORD EMPTY → KEEP OLD */
        if ($password == "") {
            $hashedPassword = $user['password'];
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        }

        $update = $conn->prepare("
            UPDATE users 
            SET name=?, email=?, phone=?, password=? 
            WHERE id=? AND role='customer'
        ");

        $update->bind_param("ssssi", $name, $email, $phone, $hashedPassword, $id);
        $update->execute();

        $success = "User Updated Successfully!";

        /* refresh data safely */
        $stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='customer'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit User</title>

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
    max-width: 450px;     /* Desktop par 450px se bada na ho */
    margin: 40px auto;
    background: #f3e8ff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    box-sizing: border-box; /* Padding handling */
}

input{
    width: 100%;
    box-sizing: border-box;
    padding: 12px;
    margin: 10px 0;
    border-radius: 10px;
    border: 1px solid #ddd;
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

.error{
    color:red;
    font-size: 14px;
}

.success{
    color:green;
    font-size: 14px;
}

/* Tablet aur Mobile adjustment */
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

    <h2>Edit Customer</h2>

    <div class="form-box">

        <?php if($error): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if($success): ?>
            <p class="success"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="POST">

            <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>

            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>

            <input type="password" name="password" placeholder="New Password (optional)">

            <button type="submit">Update User</button>

        </form>

    </div>

</div>

</body>
</html>