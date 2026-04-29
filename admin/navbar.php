<?php
if (!isset($_SESSION)) {
    session_start();
}

/* 🔐 SAFETY CHECK */
$email = $_SESSION['email'] ?? 'A';
$firstLetter = strtoupper(substr($email, 0, 1));
?>

<style>
/* ===== NAVBAR BASE ===== */
.navbar {
    min-height: 80px; /* Fixed height ki jagah min-height use ki hai */
    background: #800000;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 30px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
    flex-wrap: wrap; /* Taake mobile par items niche aa saken */
}

.logo {
    font-size: 20px;
    font-weight: bold;
    color: white;
}

.menu {
    display: flex;
    gap: 15px;
    align-items: center;
}

.menu a {
    text-decoration: none;
    color: white;
    font-weight: 500;
    padding: 8px 10px;
    font-size: 14px;
    transition: 0.3s;
}

.menu a:hover {
    color: #ffd700;
}

/* DROPDOWN */
.menu-item {
    position: relative;
}

.dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    background: #800000;
    color: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    border-radius: 8px;
    min-width: 160px;
    display: none;
    z-index: 999;
}

.menu-item:hover .dropdown {
    display: block;
}

.dropdown a {
    display: block;
    padding: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    color: white;
    text-decoration: none;
}

.dropdown a:hover {
    background: rgba(255,255,255,0.1);
}

/* PROFILE */
.profile {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #ffe6f2;
    color: #800000;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    text-decoration: none;
    transition: 0.3s;
}

.profile:hover {
    background: #ffccdd;
    transform: scale(1.1);
}

/* ===================================================
   ⭐ MEDIA QUERIES (RESPONSIVE FIX)
   =================================================== */

/* Jab screen 992px se choti ho (Tablets/Large Phones) */
@media (max-width: 992px) {
    .navbar {
        flex-direction: column; /* Logo, Menu aur Profile vertical ho jayenge */
        padding: 15px;
    }

    .logo {
        margin-bottom: 15px;
    }

    .menu {
        justify-content: center;
        flex-wrap: wrap; /* Links multiple lines mein aa saken ge */
        margin-bottom: 15px;
        width: 100%;
    }

    .menu a {
        font-size: 13px;
        padding: 5px 8px;
    }

    /* Mobile par dropdown position theek karne ke liye */
    .dropdown {
        position: static; 
        box-shadow: none;
        border: 1px solid #ffe6f2;
        width: 100%;
    }
}

/* Jab screen 480px se choti ho (Small Mobile) */
@media (max-width: 480px) {
    .menu {
        display: grid;
        grid-template-columns: 1fr 1fr; /* 2 columns ka grid ban jayega */
        gap: 5px;
    }

    .menu a {
        text-align: center;
        background: #f8f0ff;
        border-radius: 5px;
    }

    .logo {
        font-size: 18px;
    }
}
</style>

<div class="navbar">

    <div class="logo">🛍 Stylish Boutique Admin</div>

    <div class="menu">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_users.php">Customers</a>

        <div class="menu-item">
            <a href="#">Products ▾</a>
            <div class="dropdown">
                <a href="add_product.php">Add Product</a>
                <a href="manage_products.php">View Products</a>
            </div>
        </div>

        <div class="menu-item">
            <a href="#">Categories ▾</a>
            <div class="dropdown">
                <a href="add_category.php">Add Category</a>
                <a href="manage_categories.php">View Categories</a>
            </div>
        </div>

        <a href="manage_orders.php">Orders</a>
        <a href="sales.php">Sales</a>
        <a href="logout.php">Logout</a>
    </div>

    <a href="admin_profile.php" class="profile">
        <?= htmlspecialchars($firstLetter); ?>
    </a>

</div>