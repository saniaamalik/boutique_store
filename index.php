<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>

<title>Stylish Boutique</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body{
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', sans-serif;
    background: url('uploads/index1.jpg') no-repeat center center fixed;
    background-size: cover;
}

/* LOGO */
.logo{
    position: absolute;
    top: 20px;
    left: 30px;
    font-size: 22px;
    font-weight: bold;
    background: linear-gradient(to right, #800000, #ff69b4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* TOP BAR */
.top-bar{
    position: absolute;
    top: 20px;
    right: 30px;
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.top-bar a{
    text-decoration: none;
    padding: 10px 18px;
    border-radius: 25px;
    font-weight: bold;
    font-size: 14px;
    white-space: nowrap;
}

/* BUTTONS */
.login-btn{
    background: #fff0f5;
    color: #800000;
}

.login-btn:hover{
    background: #ffd6e8;
}

.register-btn{
    background: #800000;
    color: white;
}

.register-btn:hover{
    background: #660000;
}

/* OVERLAY */
.overlay{
    width: 100%;
    min-height: 100vh;
    background: rgba(255,182,193,0.45);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

/* CONTAINER */
.container{
    text-align: center;
    background: rgba(255, 250, 252, 0.6);
    padding: 50px 70px;
    border-radius: 25px;
    max-width: 500px;
    width: 100%;
    box-shadow: 0px 5px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 192, 203, 0.5);
}

/* TEXT */
h1{
    font-size: 48px;
    color: #800000;
}

p{
    font-size: 16px;
    color: #99004d;
    line-height: 1.5;
}

/* BUTTON */
.shop-btn{
    padding: 12px 28px;
    border: none;
    border-radius: 25px;
    background: #800000;
    color: white;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    transition: background 0.3s, transform 0.3s;
}

.shop-btn:hover{
    background: #660000;
    transform: translateY(-2px);
}

/* MOBILE */
@media (max-width: 768px){

    .logo{
        font-size: 18px;
        left: 15px;
        top: 15px;
    }

    .top-bar{
        right: 15px;
        top: 15px;
        gap: 6px;
    }

    .top-bar a{
        padding: 8px 12px;
        font-size: 12px;
    }

    .container{
        padding: 30px 20px;
    }

    h1{
        font-size: 32px;
    }

    p{
        font-size: 14px;
    }

    .shop-btn{
        font-size: 14px;
        padding: 10px 22px;
    }
}

/* EXTRA SMALL */
@media (max-width: 400px){

    .top-bar{
        flex-direction: column;
        align-items: flex-end;
    }

    .container{
        padding: 25px 15px;
    }

    h1{
        font-size: 26px;
    }
}

</style>
</head>

<body>

<div class="logo">🛍 Stylish Boutique</div>

<div class="top-bar">

<?php if (isset($_SESSION['user_id'])): ?>

    <a href="customers/dashboard.php" class="register-btn">My Dashboard</a>
    <a href="customers/logout.php" class="login-btn">Logout</a>

<?php else: ?>

    <a href="login.php" class="login-btn">Login</a>
    <a href="register.php" class="register-btn">Register</a>

<?php endif; ?>

</div>

<div class="overlay">

    <div class="container">

        <h1>Stylish Boutique</h1>

        <p>
            Welcome to Stylish Boutique — where elegance meets modern fashion.<br>
            Discover trendy, premium outfits designed just for you.
        </p>

        <button class="shop-btn" onclick="window.location.href='customers/dashboard.php'">
            Shop Now
        </button>

    </div>

</div>

</body>
</html>