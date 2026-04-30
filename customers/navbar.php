<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$email       = $_SESSION['email'] ?? '';
$user_id     = $_SESSION['user_id'] ?? '';
$firstLetter = $email ? strtoupper(substr($email, 0, 1)) : "U";
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
.navbar {
    height: 70px;
    background: #800000;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 999;
}

/* LOGO */
.logo {
    font-size: 20px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

/* CENTER MENU */
.menu-wrapper {
    display: flex;
    justify-content: center;
    flex: 2;
}

.menu {
    display: flex;
    gap: 25px;
    align-items: center;
}

.menu a {
    text-decoration: none;
    color: white;
    font-weight: 500;
    font-size: 15px;
    padding-bottom: 4px;
    border-bottom: 2px solid transparent;
    transition: 0.3s;
}

.menu a:hover, .menu a.active {
    color: #ffd700;
    border-bottom-color: #ffd700;
}

.menu a.admin-link {
    font-weight: 700;
    background: rgba(255,255,255,0.2);
    padding: 5px 14px;
    border-radius: 20px;
    border-bottom: none;
}

/* RIGHT SECTION */
.right-section {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-right: 10px;
}

/* PROFILE */
.profile {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #ffe6f2;
    color: #800000;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
}

/* AUTH BUTTONS */
.auth-buttons {
    display: flex;
    gap: 10px;
}

.nav-login-btn, .nav-register-btn {
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 13px;
    text-decoration: none;
}

.nav-login-btn {
    background: white;
    color: #800000;
}

.nav-register-btn {
    background: #eda7a2;
    color: #800000;
}

/* HAMBURGER */
.menu-toggle {
    display: none;
    font-size: 26px;
    color: white;
    cursor: pointer;
}

/* ================= MOBILE ================= */
@media (max-width: 768px) {

    .menu-toggle {
        display: block;
    }

    .menu-wrapper {
        position: absolute;
        top: 70px;
        left: 0;
        width: 100%;
        background: #800000;
        display: none;
        flex-direction: column;
        padding: 15px 0;
    }

    .menu-wrapper.active {
        display: flex;
    }

    .menu {
        flex-direction: column;
        gap: 15px;
    }

    .menu a {
        width: 100%;
        text-align: left;
        padding: 12px 20px;
    }

    /* ⭐ IMPORTANT FIX: PROFILE SHOW ON MOBILE */
    .right-section {
        position: absolute;
        right: 15px;
        top: 15px;
        display: flex;
    }

    .auth-buttons {
        display: none;
    }
}
</style>

<div class="navbar">

    <!-- LEFT -->
    <a href="../index.php" class="logo">🛍 Stylish Boutique</a>

    <!-- CENTER MENU -->
    <div class="menu-wrapper" id="menuWrapper">
        <div class="menu">

            <a href="dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">Home</a>

            <?php if (!empty($user_id)): ?>
                <a href="cart.php" class="<?= $currentPage == 'cart.php' ? 'active' : '' ?>">🛒 Cart</a>
                <a href="orders.php" class="<?= $currentPage == 'orders.php' ? 'active' : '' ?>">📦 Orders</a>
                <a href="logout.php">Logout</a>
            <?php endif; ?>

            <a href="../admin/admin_dashboard.php" class="admin-link">👨‍💼 Admin</a>

            <?php if (empty($user_id)): ?>
                <a href="../login.php">Login</a>
                <a href="../register.php">Register</a>
            <?php endif; ?>

        </div>
    </div>

    <!-- RIGHT -->
    <div class="right-section">

        <?php if (!empty($user_id)): ?>
            <a href="profile.php" class="profile">
                <?= htmlspecialchars($firstLetter) ?>
            </a>
        <?php else: ?>
            <div class="auth-buttons">
                <a href="../login.php" class="nav-login-btn">Login</a>
                <a href="../register.php" class="nav-register-btn">Register</a>
            </div>
        <?php endif; ?>

        <div class="menu-toggle" onclick="toggleMenu()">☰</div>

    </div>

</div>

<script>
function toggleMenu() {
    document.getElementById("menuWrapper").classList.toggle("active");
}
</script>