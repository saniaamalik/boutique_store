<?php
session_start();

//$conn = new mysqli("localhost", "root", "", "db_boutique");
include "config/db.php";


if ($conn->connect_error) {
    die("DB Error");
}

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF Token");
    }

    $name = trim($_POST["name"]);
    $password = trim($_POST["password"]);

    // VALIDATION
    if ($name == "" || $password == "") {
        $error = "All fields are required!";
    }
    elseif (!preg_match("/^[a-zA-Z0-9_]{3,30}$/", $name)) {
        $error = "Invalid username format!";
    }
    else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? LIMIT 1");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row["password"])) {

                session_regenerate_id(true);

                $_SESSION["user_id"]  = $row["id"];
                $_SESSION["username"] = $row["name"];
                $_SESSION["email"]    = $row["email"];
                $_SESSION["phone"]    = $row["phone"];
                $_SESSION["role"]     = $row["role"];

                if ($row["role"] === "admin") {
                    header("Location: admin/admin_dashboard.php");
                } else {
                    header("Location: customers/dashboard.php");
                }
                exit();

            } else {
                $error = "Incorrect password!";
            }

        } else {
            $error = "User not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

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

.container{
    width:380px;
    background: rgba(255,255,255,0.75);
    backdrop-filter: blur(15px);
    padding:40px;
    border-radius:25px;
    box-shadow:0 10px 40px rgba(0,0,0,0.25);
    text-align:center;
}

h2{
    color:#6a0dad;
}

/* LABEL STYLE */
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

/* REGISTER LINK */
.register-link{
    margin-top:15px;
    font-size:14px;
}
.register-link a{
    color:#6a0dad;
    font-weight:bold;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="container">

<h2>Login</h2>

<form method="POST">

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

<label>Username</label>
<input type="text" name="name" placeholder="Enter Username">

<label>Password</label>
<input type="password" name="password" placeholder="Enter Password">

<button type="submit">Login</button>

</form>

<!-- REGISTER LINK -->
<div class="register-link">
    Don’t have an account?
    <a href="register.php">Register</a>
</div>

<?php if($error): ?>
<p class="error"><?= htmlspecialchars($error); ?></p>
<?php endif; ?>

</div>

</body>
</html>