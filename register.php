<?php
session_start();

//$conn = new mysqli("localhost", "root", "", "db_boutique");
include "config/db.php";

if ($conn->connect_error) {
    die("DB Error");
}

// CSRF code gernate kerwana
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
// compare kerna session token ko with form
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF");
    }

    $username = trim($_POST["username"]);
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $phone    = trim($_POST["phone_no"]);

    // ✅ VALIDATION
    if ($username == "" || $email == "" || $password == "" || $phone == "") {
        $error = "All fields are required!";
    }
    elseif (!preg_match("/^[a-zA-Z ]{3,30}$/", $username)) {
        $error = "Username must be 3-30 letters only!";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    }
    elseif (!preg_match("/^[0-9]{11}$/", $phone)) {
        $error = "Phone must be exactly 11 digits!";
    }
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    }
    else {

        // duplicate email check
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $error = "Email already exists!";
        } else {
             //convert password into hash
            $hashed = password_hash($password, PASSWORD_DEFAULT);
               //insrt into db
            $stmt = $conn->prepare("INSERT INTO users (name,email,password,phone,role) VALUES (?,?,?,?, 'customer')");
            $stmt->bind_param("ssss", $username, $email, $hashed, $phone);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #6a0dad, #8e44ad, #c084fc);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* CONTAINER */
.container{
    width:380px;
    background: rgba(255,255,255,0.75);
    backdrop-filter: blur(15px);
    padding:40px;
    border-radius:25px;
    box-shadow:0 10px 40px rgba(0,0,0,0.25);
    text-align:center;
}

/* TITLE */
h2{
    color:#6a0dad;
}

/* LABEL */
label{
    display:block;
    margin-top:10px;
    margin-bottom:5px;
    text-align:left;
    margin-left:5px;
   
    font-size:14px;
    color:#4b2c5e;
}

/* INPUT */
input{
    width:93%;
    padding:12px;
    border-radius:25px;
    border:none;
    background:#f3e8ff;
    outline:none;
}

/* BUTTON */
button{
    width:100%;
    margin-top:20px;
    padding:12px;
    border:none;
    border-radius:25px;
    background:#6a0dad;
    color:white;
    cursor:pointer;
    font-weight:bold;
}

/* ERROR */
.error{
    color:red;
    margin-top:10px;
    font-size:14px;
}

/* LOGIN LINK */
.login-link{
    margin-top:15px;
    font-size:14px;
}
.login-link a{
    color:#6a0dad;
    font-weight:bold;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="container">

<h2>Register</h2>

<form method="POST">
<!--csrf token submit with form -->
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

<label>Username</label>
<input type="text" name="username" placeholder="Enter Username">

<label>Email</label>
<input type="email" name="email" placeholder="Enter Email">

<label>Password</label>
<input type="password" name="password" placeholder="Enter Password">

<label>Phone</label>
<input type="text" name="phone_no" placeholder="Enter Phone (11 digits)">

<button type="submit">Register</button>

</form>

<!-- LOGIN LINK -->
<div class="login-link">
    Already registered?
    <a href="login.php">Login</a>
</div>

<?php if($error): ?>
<p class="error"><?= htmlspecialchars($error); ?></p>//tags ko text ma convert kerna
<?php endif; ?>

</div>

</body>
</html>