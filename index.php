<?php
session_start();
/*
✅ FIX: "Shop Now" button ab customers/dashboard.php pe jata hai
✅ Login required nahi hai dashboard dekhne ke liye
✅ Sirf Add to Cart ke waqt login check hoga (cart.php mein)
*/
?>

<!DOCTYPE html>
<html>
<head>
<title>Stylish Boutique</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body{
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', sans-serif;
    background: url('uploads/index1.jpeg') no-repeat center center fixed;
    background-size: cover;
}

/* LOGO */
.logo{
    position: absolute;
    top: 20px;
    left: 30px;
    font-size: 24px;
    font-weight: bold;
    background: linear-gradient(to right, #4b0082, #8e44ad);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* TOP BUTTONS */
.top-bar{
    position: absolute;
    top: 20px;
    right: 30px;
    display: flex;
    gap: 10px;
    align-items: center;
}

.top-bar a{
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: bold;
    font-family: 'Segoe UI', sans-serif;
    font-size: 14px;
}

.login-btn{
    background: #ffffff;
    color: #6a0dad;
}

.login-btn:hover{
    background: #f0e0ff;
}

.register-btn{
    background: #8e44ad;
    color: white;
}

.register-btn:hover{
    background: #6a0dad;
}

/* OVERLAY */
.overlay{
    width: 100%;
    height: 100vh;
    background: rgba(138, 43, 226, 0.35);
    display: flex;
    justify-content: center;
    align-items: center;
}

/* CONTAINER */
.container{
    text-align: center;
    background: rgba(255, 255, 255, 0.65);
    padding: 50px 70px;
    border-radius: 25px;
}

h1{
    font-size: 50px;
    color: #6a0dad;
}

p{
    font-size: 17px;
    color: #4b2c5e;
}

/* BUTTON */
.shop-btn{
    padding: 12px 30px;
    border: none;
    border-radius: 25px;
    background: #8e44ad;
    color: white;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    transition: background 0.3s;
}

.shop-btn:hover{
    background: #6a0dad;
}

/* Responsive */
@media (max-width: 600px){
    .container{
        padding: 30px 20px;
    }
    h1{ font-size: 32px; }
    .top-bar{ top: 12px; right: 12px; }
    .top-bar a{ padding: 8px 14px; font-size: 13px; }
}
</style>
</head>

<body>

<!-- LOGO -->
<div class="logo">🛍 Stylish Boutique</div>

<!-- TOP RIGHT: Login / Register buttons (hamesha dikhenge) -->
<div class="top-bar">
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Already logged in - dashboard link dikhao -->
        <a href="customers/dashboard.php" class="register-btn">My Dashboard</a>
        <a href="customers/logout.php" class="login-btn">Logout</a>
    <?php else: ?>
        <!-- Guest - login/register dikhao -->
        <a href="login.php" class="login-btn">Login</a>
        <a href="register.php" class="register-btn">Register</a>
    <?php endif; ?> 
</div>

<!-- MAIN CONTENT -->
<div class="overlay">
    <div class="container">

        <h1>Stylish Boutique</h1>

        <p>
            Welcome to Stylish Boutique — where elegance meets modern fashion.<br>
            Discover trendy, premium outfits designed just for you.
        </p>

        <!--
            ✅ FIX: Ab direct customers/dashboard.php pe jata hai
            Login ki zaroorat nahi sirf browsing ke liye
        -->
        <button class="shop-btn" onclick="window.location.href='customers/dashboard.php'">
            Shop Now
        </button>

    </div>
</div>

</body>
</html>