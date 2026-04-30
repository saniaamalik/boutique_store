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
    /* justify-content: space-between ko hata kar center kiya hai */
    justify-content: space-between; 
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
    /* Logo ko left side par fix rakhne ke liye */
    flex: 1; 
}

/* Menu Wrapper ko center karne ke liye */
.menu-wrapper {
    display: flex;
    justify-content: center;
    flex: 2; /* Ye beech ka hissa zayda space lega taake center ho sakay */
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
    white-space: nowrap;
}

.menu a:hover, .menu a.active {
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
}

.menu a.admin-link:hover {
    background: white;
    color: #800000;
}

/* Right buttons ko right side par push karne ke liye */
.right-section {
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: flex-end;
    flex: 1; 
}

.auth-buttons {
    display: flex;
    gap: 10px;
}

.nav-login-btn, .nav-register-btn {
    padding: 8px 18px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
    text-decoration: none;
    transition: 0.3s;
}

.nav-login-btn {
    background: white;
    color: #800000;
    border: 2px solid white;
}

.nav-register-btn {
    background: #eda7a2;
    color: #800000;
    border: 2px solid #800000;
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
}

@media (max-width: 992px) {
    .navbar { height: auto; padding: 15px; flex-direction: column; gap: 15px; }
    .logo, .menu-wrapper, .right-section { flex: none; width: 100%; justify-content: center; text-align: center; }
}
</style>

<div class="navbar">

    <a href="../index.php" class="logo">🛍 Stylish Boutique</a>

    <div class="menu-wrapper">
        <div class="menu">
            <a href="dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">Home</a>

            <?php if (!empty($user_id)): ?>
                <a href="cart.php" class="<?= $currentPage == 'cart.php' ? 'active' : '' ?>">🛒 Cart</a>
                <a href="orders.php" class="<?= $currentPage == 'orders.php' ? 'active' : '' ?>">📦 Orders</a>
                <a href="logout.php">Logout</a>
            <?php endif; ?>

            <a href="../admin/admin_dashboard.php" class="admin-link">👨‍💼 Admin</a>
        </div>
    </div>

    <div class="right-section">
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