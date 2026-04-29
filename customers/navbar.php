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
    justify-content: space-between;
    align-items: center;
    padding: 0 30px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 999;
}

.navbar .logo {
    font-size: 22px;
    font-weight: bold;
    color: white;
    text-decoration: none;
    white-space: nowrap;
}

.menu {
    display: flex;
    gap: 20px;
    align-items: center;
    flex-wrap: wrap;
}

.menu a {
    text-decoration: none;
    color: white;
    font-weight: 500;
    font-size: 15px;
    padding-bottom: 4px;
    border-bottom: 2px solid transparent;
    transition: 0.3s;
    white-space: nowrap;
}

.menu a:hover {
    color: #ffd700;
    border-bottom-color: #ffd700;
}

.menu a.active {
    color: #ffd700;
    border-bottom-color: #ffd700;
}

.menu a.admin-link {
    color: white;
    font-weight: 700;
    background: rgba(255,255,255,0.2);
    padding: 5px 14px;
    border-radius: 20px;
    border-bottom: none;
    font-size: 14px;
    transition: 0.3s;
}

.menu a.admin-link:hover {
    background: white;
    color: #800000;
}

.auth-buttons {
    display: flex;
    gap: 10px;
    align-items: center;
}

.nav-login-btn {
    padding: 8px 18px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
    text-decoration: none;
    background: white;
    color: #800000;
    border: 2px solid white;
    transition: 0.3s;
}

.nav-login-btn:hover {
    background: transparent;
    color: white;
    border-color: white;
}

.nav-register-btn {
    padding: 8px 18px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
    text-decoration: none;
    background:  #eda7a2;
    color: #800000;
    border: 2px solid #800000;
    transition: 0.3s;
}

.nav-register-btn:hover {
    background: #ffe6f2;
    border-color: #ffe6f2;
}

.profile {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #ffe6f2;
    color: #800000;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
    font-size: 16px;
    transition: 0.3s;
    flex-shrink: 0;
}

.profile:hover {
    transform: scale(1.1);
    background: #ffccdd;
}

@media (max-width: 768px) {
    .navbar {
        height: auto;
        padding: 12px 16px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .menu {
        width: 100%;
        justify-content: center;
        gap: 12px;
    }
    .auth-buttons {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .navbar .logo  { font-size: 18px; }
    .menu a        { font-size: 13px; }
    .nav-login-btn,
    .nav-register-btn { padding: 7px 14px; font-size: 13px; }
}

</style>

<div class="navbar">

    <a href="../index.php" class="logo">🛍 Stylish Boutique</a>

    <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">

        <div class="menu">

            <a href="dashboard.php"
               class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
                Home
            </a>

            <?php if (!empty($user_id)): ?>

                <a href="cart.php"
                   class="<?= $currentPage == 'cart.php' ? 'active' : '' ?>">
                    🛒 Cart
                </a>

                <a href="orders.php"
                   class="<?= $currentPage == 'orders.php' ? 'active' : '' ?>">
                    📦 Orders
                </a>

                <a href="logout.php">Logout</a>

            <?php endif; ?>

            <!-- ✅ Admin always visible -->
            <a href="../admin/admin_dashboard.php" class="admin-link">
                👨‍💼 Admin
            </a>

        </div>

        <?php if (!empty($user_id)): ?>
            <a href="profile.php" class="profile" title="My Profile">
                <?= htmlspecialchars($firstLetter) ?>
            </a>
        <?php else: ?>
            <div class="auth-buttons">
                <a href="../login.php" class="nav-login-btn">Login</a>
                <a href="../register.php" class="nav-register-btn">Register</a>
            </div>
        <?php endif; ?>

    </div>

</div>