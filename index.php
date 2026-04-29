<?php
// PHP session start karta hai — user login data ya cart data store karne ke liye zaroori hota hai
session_start();

/*
Yeh multi-line comment hai:
✔ Shop Now button dashboard par le jata hai
✔ Login zaroori nahi homepage dekhne ke liye
✔ Login sirf cart/add to cart ke waqt check hota hai
*/
?>

<!DOCTYPE html>
<!-- HTML5 document start -->

<html>
<head>
<!-- Page ka title browser tab mein show hota hai -->
<title>Stylish Boutique</title>

<!-- Mobile responsive banane ke liye -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
/* BODY styling: poori website ka background aur font set karta hai */
body{
    margin: 0; /* default spacing remove */
    padding: 0; /* default padding remove */
    font-family: 'Segoe UI', sans-serif; /* clean modern font */
    background: url('uploads/index1.jpeg') no-repeat center center fixed;
    /* background image set kar raha hai, center aur fixed rakhta hai */
    background-size: cover; /* image full screen cover kare */
}

/* LOGO styling */
.logo{
    position: absolute; /* screen ke fixed position pe rakhta hai */
    top: 20px; /* upar se 20px gap */
    left: 30px; /* left se 30px gap */
    font-size: 24px; /* text size */
    font-weight: bold; /* bold text */
    background: linear-gradient(to right, #4b0082, #8e44ad);
    /* gradient color effect */
    -webkit-background-clip: text; /* text ke andar gradient apply */
    -webkit-text-fill-color: transparent; /* text transparent taake gradient dikhe */
}

/* TOP BAR (login/register buttons container) */
.top-bar{
    position: absolute; /* fixed top right */
    top: 20px;
    right: 30px;
    display: flex; /* buttons side by side */
    gap: 10px; /* buttons ke beech space */
    align-items: center; /* vertical alignment */
}

/* TOP BAR ke links (buttons) */
.top-bar a{
    text-decoration: none; /* underline remove */
    padding: 10px 20px; /* button size */
    border-radius: 25px; /* round corners */
    font-weight: bold;
    font-family: 'Segoe UI', sans-serif;
    font-size: 14px;
}

/* LOGIN button style */
.login-btn{
    background: #ffffff; /* white background */
    color: #6a0dad; /* purple text */
}

.login-btn:hover{
    background: #f0e0ff; /* hover effect */
}

/* REGISTER button style */
.register-btn{
    background: #8e44ad; /* purple background */
    color: white;
}

.register-btn:hover{
    background: #6a0dad; /* darker purple on hover */
}

/* FULL SCREEN OVERLAY */
.overlay{
    width: 100%;
    height: 100vh; /* full screen height */
    background: rgba(138, 43, 226, 0.35); /* purple transparent layer */
    display: flex; /* center content */
    justify-content: center; /* horizontal center */
    align-items: center; /* vertical center */
}

/* MAIN CONTENT BOX */
.container{
    text-align: center;
    background: rgba(255, 255, 255, 0.65); /* semi transparent white box */
    padding: 50px 70px; /* spacing inside box */
    border-radius: 25px; /* rounded corners */
}

/* MAIN TITLE */
h1{
    font-size: 50px;
    color: #6a0dad; /* purple color */
}

/* PARAGRAPH TEXT */
p{
    font-size: 17px;
    color: #4b2c5e;
}

/* SHOP BUTTON */
.shop-btn{
    padding: 12px 30px;
    border: none;
    border-radius: 25px;
    background: #8e44ad;
    color: white;
    cursor: pointer; /* mouse pointer hand ban jata hai */
    font-weight: bold;
    font-size: 16px;
    transition: background 0.3s; /* smooth hover animation */
}

.shop-btn:hover{
    background: #6a0dad;
}

/* RESPONSIVE DESIGN (mobile screens ke liye) */
@media (max-width: 600px){

    .container{
        padding: 30px 20px; /* mobile pe chhota padding */
    }

    h1{
        font-size: 32px; /* chhota heading mobile ke liye */
    }

    .top-bar{
        top: 12px;
        right: 12px;
    }

    .top-bar a{
        padding: 8px 14px;
        font-size: 13px;
    }
}
</style>
</head>

<body>

<!-- LOGO show hota hai top left -->
<div class="logo">🛍 Stylish Boutique</div>

<!-- TOP BAR: login/register ya dashboard buttons -->
<div class="top-bar">

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Agar user login hai -->

    <!-- Dashboard button -->
    <a href="customers/dashboard.php" class="register-btn">My Dashboard</a>

    <!-- Logout button -->
    <a href="customers/logout.php" class="login-btn">Logout</a>

<?php else: ?>
    <!-- Agar user login nahi hai -->

    <!-- Login page link -->
    <a href="login.php" class="login-btn">Login</a>

    <!-- Register page link -->
    <a href="register.php" class="register-btn">Register</a>

<?php endif; ?> 

</div>

<!-- MAIN SCREEN AREA -->
<div class="overlay">

    <div class="container">

        <!-- Website heading -->
        <h1>Stylish Boutique</h1>

        <!-- Description text -->
        <p>
            Welcome to Stylish Boutique — where elegance meets modern fashion.<br>
            Discover trendy, premium outfits designed just for you.
        </p>

        <!-- Shop Now button -->
        <!-- onclick = click par dashboard page open -->
        <button class="shop-btn" onclick="window.location.href='customers/dashboard.php'">
            Shop Now
        </button>

    </div>
</div>

</body>
</html>