<?php
if (!isset($_SESSION)) {
    session_start();
}

$email = $_SESSION['email'] ?? 'A';
$firstLetter = strtoupper(substr($email, 0, 1));
?>

<style>
/* ===== NAVBAR ===== */
.navbar {
    height: 70px;
    background: #800000;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    position: relative;
}

/* LOGO */
.logo {
    color: white;
    font-size: 18px;
    font-weight: bold;
}

/* CENTER MENU (ABSOLUTE CENTER FIX) */
.menu {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 20px;
    align-items: center;
}

/* LINKS */
.menu a {
    text-decoration: none;
    color: #ffd700;
    font-size: 14px;
    padding: 6px 8px;
}

/* DROPDOWN */
.menu-item {
    position: relative;
}

.dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: #800000;
    min-width: 160px;
    display: none;
    border-radius: 6px;
}

.menu-item:hover .dropdown {
    display: block;
}

.dropdown a {
    display: block;
    padding: 10px;
    color: white;
}

.dropdown a:hover {
    background: rgba(255,255,255,0.1);
}

/* RIGHT */
.right-section {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* PROFILE */
.profile {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ffe6f2;
    color: #800000;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
}

/* HAMBURGER */
.menu-toggle {
    display: none;
    font-size: 24px;
    color: white;
    cursor: pointer;
}

/* ================= MOBILE ================= */
@media (max-width: 768px) {

    .menu-toggle {
        display: block;
    }

    /* HIDE CENTER MENU */
    .menu {
        position: absolute;
        top: 70px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        background: #800000;
        flex-direction: column;
        display: none;
        border-radius: 10px;
        padding: 10px 0;
        text-align: center;
    }

    .menu.active {
        display: flex;
    }

    .menu a {
        width: 100%;
        padding: 12px;
    }

    /* MOBILE DROPDOWN */
    .dropdown {
        position: static;
        display: none;
        width: 100%;
    }

    .menu-item.active .dropdown {
        display: block;
    }
}
</style>

<div class="navbar">

    <!-- LEFT -->
    <div class="logo">🛍 Stylish Boutique Admin</div>

    <!-- CENTER MENU -->
    <div class="menu" id="menu">

        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_users.php">Customers</a>

        <div class="menu-item">
            <a href="#" onclick="toggleDropdown(event, this)">Products ▾</a>
            <div class="dropdown">
                <a href="add_product.php">Add Product</a>
                <a href="manage_products.php">View Products</a>
            </div>
        </div>

        <div class="menu-item">
            <a href="#" onclick="toggleDropdown(event, this)">Categories ▾</a>
            <div class="dropdown">
                <a href="add_category.php">Add Category</a>
                <a href="manage_categories.php">View Categories</a>
            </div>
        </div>

        <a href="manage_orders.php">Orders</a>
        <a href="sales.php">Sales</a>
        <a href="logout.php">Logout</a>

    </div>

    <!-- RIGHT -->
    <div class="right-section">

        <a href="admin_profile.php" class="profile">
            <?= htmlspecialchars($firstLetter); ?>
        </a>

        <div class="menu-toggle" onclick="toggleMenu()">☰</div>

    </div>

</div>

<script>
function toggleMenu() {
    document.getElementById("menu").classList.toggle("active");
}

function toggleDropdown(e, el) {
    if (window.innerWidth <= 768) {
        e.preventDefault();
        el.parentElement.classList.toggle("active");
    }
}
</script>