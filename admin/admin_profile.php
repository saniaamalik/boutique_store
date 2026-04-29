<?php
// Session start - User ki jankari save karne ke liye
session_start();
require("../config/db.php");

// SIRF ADMIN USERS KO ACCESS - Dusre ko bahar nikal do
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

// Admin ka user ID le lo
$id = $_SESSION['user_id'];

// DATABASE SE ADMIN k data LE LO - Secure query with prepared statement
$stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='admin'");
$stmt->bind_param("i", $id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

// Agar admin nahi mila toh error
if (!$admin) {
    die("Admin not found!");
}

// Error aur success messages ke liye
$error = "";
$success = "";

/* ADMIN KA PROFILE UPDATE KARNE KA KAAM */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Form se data le lo aur spaces hatao
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Validation - Name aur email zaroori hain
    if ($name == "" || $email == "") {
        $error = "Name and Email required!";
    } else {

        // Database mein profile update karo
        $update = $conn->prepare("
            UPDATE users 
            SET name=?, email=?, phone=?, address=? 
            WHERE id=? AND role='admin'
        ");

        $update->bind_param("ssssi", $name, $email, $phone, $address, $id);
        $update->execute();

        // Success message
        $success = "Profile Updated Successfully!";

        // Updated data ko dobara le lo
        $stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='admin'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
    }
}

// Avatar ke liye email ka pehla letter
$firstLetter = strtoupper(substr($admin['email'], 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Profile</title>

<style>
/* Page ka background - Maroon color */
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#eda7a2;  /* Light pink */
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
    color:#800000;  /* Maroon color */
    margin-top:25px;
    font-size: 1.8rem;
}

/* Profile info ka box */
.profile-box{
    width: 90%;  /* Mobile screens par flexible */
    max-width: 450px;  /* Bari screens par fixed max width */
    margin: 40px auto;
    background: #ffe6f2;  /* Light pink background */
    padding: 25px;  /* Inner spacing */
    border-radius: 15px;  /* Round corners */
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);  /* Shadow effect */
    box-sizing: border-box;  /* Padding ko width ke andar rakhne ke liye */
}

/* Input fields - Form elements */
input, textarea{
    width: 100%;
    padding: 12px;  /* Inner spacing */
    margin: 10px 0;  /* Outer spacing */
    border-radius: 10px;  /* Round corners */
    border: 1px solid #ddd;  /* Light border */
    box-sizing: border-box;
    outline: none;  /* Focus pe outline nahi */
    font-size: 14px;
}

/* Textarea ko bada banao */
textarea {
    height: 100px;
    resize: vertical;  /* Sirf vertical resize */
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

/* Avatar - Profile picture */
.avatar{
    width: 80px;  /* 80x80 pixels */
    height: 80px;
    border-radius: 50%;  /* Round circle */
    background: #800000;  /* Maroon background */
    color: white;  /* White text */
    display: flex;  /* Center content */
    justify-content: center;  /* Horizontal center */
    align-items: center;  /* Vertical center */
    font-size: 30px;  /* Bada letter */
    font-weight: bold;  /* Bold letter */
    margin: 0 auto 15px auto;  /* Center aur neeche space */
    box-shadow: 0 2px 8px rgba(106, 13, 173, 0.2);  /* Shadow */
}

/* Mobile screens ke liye responsive */
@media (max-width: 480px) {
    h2 {  /* Heading ko chhota banao */
        font-size: 1.5rem;
    }
    .profile-box {  /* Profile box ko adjust karo */
        padding: 20px;
        margin: 20px auto;
    }
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <!-- Admin ka profile heading -->
    <h2>Admin Profile</h2>

    <div class="profile-box">

        <!-- Avatar - Email ka pehla letter -->
        <div class="avatar">
            <?= htmlspecialchars($firstLetter); ?>
        </div>

        <!-- Error message dikhao agar ho -->
        <?php if($error): ?>
            <p style="color:red; font-size:14px;"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- Success message dikhao agar ho -->
        <?php if($success): ?>
            <p style="color:green; font-size:14px;"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <!-- Profile update form -->
        <form method="POST">

            <!-- Admin ka naam -->
            <input type="text" name="name" value="<?= htmlspecialchars($admin['name']); ?>" placeholder="Name" required>

            <!-- Admin ka email -->
            <input type="email" name="email" value="<?= htmlspecialchars($admin['email']); ?>" placeholder="Email" required>

            <!-- Admin ka phone number -->
            <input type="text" name="phone" value="<?= htmlspecialchars($admin['phone']); ?>" placeholder="Phone">

            <!-- Admin ka address -->
            <textarea name="address" placeholder="Address"><?= htmlspecialchars($admin['address']); ?></textarea>

            <!-- Submit button - Profile update karne ke liye -->
            <button type="submit">Update Profile</button>

        </form>

    </div>

</div>

</body>
</html>