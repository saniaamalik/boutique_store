<?php
session_start();
require("../config/db.php");

// 🔐 ONLY ADMIN ACCESS
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

$id = $_SESSION['user_id'];

// 🔐 SAFE QUERY (prepared statement)
$stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='admin'");
$stmt->bind_param("i", $id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin) {
    die("Admin not found!");
}

$error = "";
$success = "";

/* UPDATE PROFILE */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($name == "" || $email == "") {
        $error = "Name and Email required!";
    } else {

        $update = $conn->prepare("
            UPDATE users 
            SET name=?, email=?, phone=?, address=? 
            WHERE id=? AND role='admin'
        ");

        $update->bind_param("ssssi", $name, $email, $phone, $address, $id);
        $update->execute();

        $success = "Profile Updated Successfully!";

        // refresh data
        $stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='admin'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
    }
}

$firstLetter = strtoupper(substr($admin['email'], 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Profile</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;
}

.container{
    width:95%; /* Mobile ke liye behtar spacing */
    max-width:1200px;
    margin:auto;
    text-align:center;
}

h2{
    color:#6a0dad;
    margin-top:25px;
    font-size: 1.8rem;
}

/* ⭐ RESPONSIVE PROFILE BOX */
.profile-box{
    width: 90%;           /* Mobile screens par flexible width */
    max-width: 450px;     /* Bari screens par fixed maximum width */
    margin: 40px auto;
    background: #f3e8ff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    box-sizing: border-box; /* Padding handling */
}

/* INPUTS */
input, textarea{
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 10px;
    border: 1px solid #ddd;
    box-sizing: border-box;
    outline: none;
    font-size: 14px;
}

textarea {
    height: 100px;
    resize: vertical;
}

/* BUTTON */
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

/* AVATAR */
.avatar{
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #6a0dad;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 30px;
    font-weight: bold;
    margin: 0 auto 15px auto;
    box-shadow: 0 2px 8px rgba(106, 13, 173, 0.2);
}

/* Tablet aur Mobile adjustment */
@media (max-width: 480px) {
    h2 {
        font-size: 1.5rem;
    }
    .profile-box {
        padding: 20px;
        margin: 20px auto;
    }
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <h2>Admin Profile</h2>

    <div class="profile-box">

        <div class="avatar">
            <?= htmlspecialchars($firstLetter); ?>
        </div>

        <?php if($error): ?>
            <p style="color:red; font-size:14px;"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if($success): ?>
            <p style="color:green; font-size:14px;"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="POST">

            <input type="text" name="name" value="<?= htmlspecialchars($admin['name']); ?>" placeholder="Name" required>

            <input type="email" name="email" value="<?= htmlspecialchars($admin['email']); ?>" placeholder="Email" required>

            <input type="text" name="phone" value="<?= htmlspecialchars($admin['phone']); ?>" placeholder="Phone">

            <textarea name="address" placeholder="Address"><?= htmlspecialchars($admin['address']); ?></textarea>

            <button type="submit">Update Profile</button>

        </form>

    </div>

</div>

</body>
</html>