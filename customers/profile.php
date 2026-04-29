<?php
session_start();
include "../config/db.php";

/* LOGIN CHECK */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

/* GET ONLY CURRENT USER (SAFE FIX) */
$stmt = $conn->prepare("
    SELECT id, name, email, phone, address, role 
    FROM users 
    WHERE id=? AND role != 'admin'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$message = "";

/* UPDATE PROFILE */
if (isset($_POST['update_profile'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($name == "" || $email == "" || $phone == "") {
        $message = "⚠ Please fill required fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "⚠ Invalid email format";
    } else {

        /* CHECK DUPLICATE EMAIL (EXCEPT CURRENT USER) */
        $check = $conn->prepare("
            SELECT id FROM users 
            WHERE email=? AND id!=?
        ");
        $check->bind_param("si", $email, $user_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "⚠ Email already exists!";
        } else {

            /* UPDATE ONLY CURRENT USER */
            $stmt = $conn->prepare("
                UPDATE users 
                SET name=?, email=?, phone=?, address=? 
                WHERE id=? AND role != 'admin'
            ");

            $stmt->bind_param("ssssi", $name, $email, $phone, $address, $user_id);
            $stmt->execute();

            $_SESSION['email'] = $email;

            header("Location: profile.php?updated=1");
            exit();
        }
    }
}

/* SUCCESS MESSAGE */
if (isset($_GET['updated'])) {
    $message = "Profile updated successfully ✅";
}

/* AVATAR */
$emailVal = $user['email'] ?? '';
$firstLetter = $emailVal ? strtoupper(substr($emailVal, 0, 1)) : "U";
?>

<?php include "navbar.php"; ?>

<style>

body{
    margin:0;
    font-family:Arial;
    background:#e9d5ff;
}

/* CONTAINER */
.container{
    width:350px;
    margin:50px auto;
    background:white;
    padding:22px;
    border-radius:14px;
    text-align:center;
    box-shadow:0 8px 20px rgba(0,0,0,0.1);
}

/* AVATAR */
.avatar{
    width:70px;
    height:70px;
    background:#6a0dad;
    color:white;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:26px;
    margin:10px auto;
    font-weight:bold;
}

/* ALERTS */
.success{
    background:#e6d6ff;
    color:#4b0082;
    padding:8px;
    border-radius:6px;
    margin-bottom:10px;
    font-size:13px;
}

.error{
    background:#ffd6d6;
    color:#b30000;
    padding:8px;
    border-radius:6px;
    margin-bottom:10px;
    font-size:13px;
}

/* FORM */
label{
    display:block;
    text-align:left;
    margin-top:10px;
    font-weight:bold;
    font-size:13px;
}

input, textarea{
    width:100%;
    padding:9px;
    margin-top:4px;
    border:1px solid #ddd;
    border-radius:6px;
    box-sizing:border-box;
}

/* BUTTON */
button{
    margin-top:15px;
    width:100%;
    padding:10px;
    background:#6a0dad;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#520a8a;
}

/* ================= RESPONSIVE FIX ================= */

@media (max-width:768px){
    .container{
        width:85%;
    }
}

@media (max-width:480px){

    .container{
        width:92%;
        padding:18px;
        margin:25px auto;
    }

    h2{
        font-size:20px;
    }

    .avatar{
        width:60px;
        height:60px;
        font-size:22px;
    }

    input, textarea{
        font-size:14px;
    }

    button{
        font-size:14px;
    }
}

</style>

<div class="container">

    <h2>My Profile</h2>

    <div class="avatar">
        <?= $firstLetter ?>
    </div>

    <?php if ($message != ""): ?>
        <div class="<?= strpos($message, '⚠') !== false ? 'error' : 'success' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

        <label>Address</label>
        <textarea name="address"><?= htmlspecialchars($user['address']) ?></textarea>

        <button name="update_profile">Update Profile</button>

    </form>

</div>

<?php include "footer.php"; ?>