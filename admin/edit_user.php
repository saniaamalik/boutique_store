<?php
// Session start - User ki jankari save karne ke liye
session_start();
require("../config/db.php");

// SIRF ADMIN USERS KO ACCESS - Dusre ko bahar nikal do
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

/* USER ID KO VALIDATE KARO */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID!");
}

// ID ko integer mein convert karo
$id = intval($_GET['id']);

/* DATABASE SE USER KI data LE LO - Sirf customers */
$stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='customer'");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Agar user nahi mila toh error
if (!$user) {
    die("User not found!");
}

// Error aur success messages ke liye
$error = "";
$success = "";

/* USER KO UPDATE KARNE KA KAAM */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Form se data le lo aur spaces hatao
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // Validation - Name, email, phone zaroori hain
    if ($name == "" || $email == "" || $phone == "") {
        $error = "All fields required!";
    } else {

        /* AGAR PASSWORD EMPTY HAI TIH PURAANA PASSWORD RAKHENGE */
        if ($password == "") {
            $hashedPassword = $user['password'];
        } else {
            // Naya password ko hash karo
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        }

        // Database mein user update karo
        $update = $conn->prepare("
            UPDATE users 
            SET name=?, email=?, phone=?, password=? 
            WHERE id=? AND role='customer'
        ");

        $update->bind_param("ssssi", $name, $email, $phone, $hashedPassword, $id);
        $update->execute();

        // Success message
        $success = "User Updated Successfully!";

        /* Updated user data ko dobara le lo */
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
/* Page ka background - Maroon color */
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#eda7a2;  /* Light pink */
}

/* Main container - Center mein content */
.container{
    width:95%;  /* Mobile ke liye side margins */
    max-width:1200px;
    margin:auto;
    text-align:center;
}

/* Heading - Bada title */
h2{
    color:#800000;  /* Maroon color */
    margin-top:25px;
    font-size: 1.8rem;
}

/* Form ka box - User edit karne ke liye */
.form-box{
    width: 90%;  /* Mobile par width 90% */
    max-width: 450px;  /* Desktop par 450px se upar nahi */
    margin: 40px auto;
    background: #ffe6f2;  /* Light pink background */
    padding: 25px;  /* Inner spacing */
    border-radius: 15px;  /* Round corners */
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);  /* Shadow effect */
    box-sizing: border-box;  /* Padding ko width ke andar rakhne ke liye */
}

/* Input field styling - Text likha jaye */
input{
    width: 100%;  /* Puri width */
    box-sizing: border-box;
    padding: 12px;  /* Inner spacing */
    margin: 10px 0;  /* Outer spacing */
    border-radius: 10px;  /* Round corners */
    border: 1px solid #ddd;  /* Light border */
    outline: none;  /* Focus pe outline nahi */
    font-size: 14px;
}

/* Submit button */
button{
    width: 100%;
    padding: 12px;  /* Button ko bada banao */
    background: #800000;  /* Maroon button */
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
    background: #660000;  /* Dark maroon */
}

/* Error message styling */
.error{
    color:red;  /* Red text */
    font-size: 14px;
}

/* Success message styling */
.success{
    color:green;  /* Green text */
    font-size: 14px;
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

    <!-- Customer edit karne ka heading -->
    <h2>Edit Customer</h2>

    <div class="form-box">

        <!-- Error message dikhao agar ho -->
        <?php if($error): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- Success message dikhao agar ho -->
        <?php if($success): ?>
            <p class="success"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <!-- User update form -->
        <form method="POST">

            <!-- Customer ka naam -->
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>

            <!-- Customer ka email -->
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

            <!-- Customer ka phone number -->
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>

            <!-- Naya password - optional (purana password rakhne ke liye empty chhodo) -->
            <input type="password" name="password" placeholder="New Password (optional)">

            <!-- Update button -->
            <button type="submit">Update User</button>

        </form>

    </div>

</div>

</body>
</html>