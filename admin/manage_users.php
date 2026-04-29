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

/* ADD CUSTOMER */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    if ($name == "" || $email == "" || $phone == "" || $password == "") {
        $error = "All fields required!";
    }
    else {
        // check duplicate email (SAFE)
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            // 🔐 HASH PASSWORD
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO users (name, email, password, phone, role)
                VALUES (?, ?, ?, ?, 'customer')
            ");

            $stmt->bind_param("ssss", $name, $email, $hashed, $phone);
            $stmt->execute();

            $success = "Customer Added!";
        }
    }
}

/* DELETE CUSTOMER (FIXED SAFE) */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role='customer'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

/* SEARCH (FIXED SAFE) */
$search = "";
if (isset($_GET['search']) && $_GET['search'] != "") {
    $search = "%".$_GET['search']."%";
    $stmt = $conn->prepare("
        SELECT * FROM users 
        WHERE role='customer' 
        AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)
    ");
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $customers = $stmt->get_result();
} else {
    $customers = $conn->query("SELECT * FROM users WHERE role='customer'");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;
}

.container{
    width:95%; /* Mobile ke liye behtar spacing */
    max-width: 1200px;
    margin:auto;
    text-align:center;
    padding-bottom: 50px;
}

h2{
    color:#6a0dad;
    text-align:center;
    margin-top:25px;
    font-size: 1.8rem;
}

/* ⭐ RESPONSIVE FORM BOX */
.form-box{
    width: 90%;
    max-width: 500px;
    margin: 40px auto;
    background: #f3e8ff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    box-sizing: border-box;
}

input{
    width: 100%; /* Full width within container */
    padding: 12px;
    margin: 10px 0;
    border-radius: 10px;
    border: 1px solid #ddd;
    outline: none;
    box-sizing: border-box;
}

button{
    background: #6a0dad;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    width: 100%; /* Mobile friendly large button */
    transition: 0.3s;
}

button:hover{
    background: #4b0082;
}

/* SEARCH BOX RESPONSIVE */
.search-box{
    margin: 20px auto;
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.search-box input{
    width: 300px;
    max-width: 100%;
    margin: 0;
}

.search-box button {
    width: auto;
    padding: 10px 20px;
}

/* ⭐ TABLE RESPONSIVE WRAPPER */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

table{
    width: 100%;
    min-width: 600px; /* Data overlap na ho mobile par */
    border-collapse: collapse;
}

th{
    background: #6a0dad;
    color: white;
    padding: 15px 12px;
    text-align: left;
}

td{
    padding: 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

tr:nth-child(even){
    background: #f3e8ff;
}

.edit{color: blue; font-weight: bold; text-decoration: none;}
.delete{color: red; font-weight: bold; text-decoration: none;}

/* 📱 MEDIA QUERIES */
@media (max-width: 600px) {
    h2 { font-size: 1.5rem; }
    
    .search-box {
        flex-direction: column;
        align-items: center;
    }
    
    .search-box input, .search-box button {
        width: 100%;
    }
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

<h2>Add New Customer</h2>

<div class="form-box">
    <?php if($error) echo "<p style='color:red; font-size:14px;'>$error</p>"; ?>
    <?php if($success) echo "<p style='color:green; font-size:14px;'>$success</p>"; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Customer Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="password" name="password" placeholder="Password" required>

        <button name="add">Add Customer</button>
    </form>
</div>

<h2>Customer Details</h2>

<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search customer..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button>Search</button>
</form>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $customers->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['phone']); ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $row['id']; ?>" class="edit">Edit</a> |
                    <a href="?delete=<?= $row['id']; ?>" class="delete" onclick="return confirm('Delete user?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>

</body>
</html>