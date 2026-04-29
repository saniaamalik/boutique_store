<?php
session_start();
require("../config/db.php");

$cat_id = $_GET['category'] ?? "";
$sort   = $_GET['sort']     ?? "";
$search = $_GET['search']   ?? "";

$query = "SELECT id, name, price, image, stock, category_id FROM products WHERE 1=1";

if (!empty($search)) {
    $safe_search = "%" . $conn->real_escape_string($search) . "%";
    $query .= " AND name LIKE '$safe_search'";
}

if (!empty($cat_id)) {
    $query .= " AND category_id = " . intval($cat_id);
}

if ($sort == "low") {
    $query .= " ORDER BY price ASC";
} elseif ($sort == "high") {
    $query .= " ORDER BY price DESC";
} else {
    $query .= " ORDER BY id DESC";
}

$products     = $conn->query($query);
$is_logged_in = !empty($_SESSION['user_id']) ? 'true' : 'false';
?>
<!DOCTYPE html>
<html>
<head>
<title>Stylish Boutique - Shop</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>

/* ===== RESET ===== */
*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', sans-serif;
    background: #eda7a2;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* ===== CATEGORY TABS ===== */
.categories {
    display: flex;
    gap: 10px;
    padding: 14px 20px;
    justify-content: center;
    flex-wrap: wrap;
    background:  #eda7a2;
    border-bottom: 2px solid #ffe6f2;
}

.categories a {
    padding: 8px 20px;
    background: #ffe6f2;
    color: #800000;
    border-radius: 20px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: 0.25s;
}

.categories a.active,
.categories a:hover {
    background: #800000;
    color: white;
}

/* ===== FILTER BAR ===== */
.filter-bar {
    display: flex;
    justify-content: center;
    gap: 14px;
    flex-wrap: wrap;
    padding: 14px 20px;
    background:  #eda7a2;
    border-bottom: 1px solid  #eda7a2;
}

.filter-bar form {
    display: flex;
    gap: 8px;
    align-items: center;
    background: white;
    padding: 8px 14px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(128,0,0,0.08);
}

.filter-bar input[type="text"],
.filter-bar select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    min-width: 180px;
    font-family: 'Segoe UI', sans-serif;
    color: #333;
}

.filter-bar input[type="text"]:focus,
.filter-bar select:focus {
    border-color: #c32148;
}

.filter-bar button {
    padding: 8px 20px;
    background: #800000;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Segoe UI', sans-serif;
    transition: 0.25s;
    white-space: nowrap;
}

.filter-bar button:hover {
    background: #660000;
}

/* ===== PRODUCTS SECTION ===== */
.products-section {
    flex: 1;
    padding: 28px 24px;
}

/* ===== GRID: auto-fill, each card same width ===== */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 22px;
    max-width: 1300px;
    margin: 0 auto;
    /* align-items: stretch makes all rows same height */
    align-items: stretch;
}

/* ===== CARD ===== */
.card {
    background: white;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(128,0,0,0.09);
    overflow: hidden;
    /* flex column so card-body fills remaining height */
    display: flex;
    flex-direction: column;
    transition: transform 0.25s, box-shadow 0.25s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 26px rgba(128,0,0,0.18);
}

/* ===== IMAGE: fixed height, same across all cards ===== */
.card-img {
    width: 100%;
    height: 240px;          /* fixed height - all images same */
    object-fit: cover;      /* fills box, crops neatly */
    object-position: center top;
    background: #fff0f5;
    display: block;
    flex-shrink: 0;
}

/* ===== CARD BODY: flex-1 fills remaining space ===== */
.card-body {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex: 1;               /* fill remaining card height */
    align-items: center;
    text-align: center;
    gap: 6px;
}

/* NAME: min-height keeps all name rows same height */
.card-body h3 {
    font-size: 15px;
    color: #8b0000;
    font-weight: 600;
    line-height: 1.4;
    min-height: 42px;      /* 2-line reserve */
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.price {
    color: #800000;
    font-weight: 700;
    font-size: 17px;
}

.stock {
    font-size: 12px;
    font-weight: 600;
    padding: 3px 12px;
    border-radius: 12px;
}

.stock.ok  { color: #1a7a1a; background: #e6f9e6; }
.stock.low { color: #b30000; background: #ffe6e6; }

/* SPACER pushes qty+button to bottom of every card */
.card-spacer { flex: 1; }

/* QTY ROW */
.qty-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    justify-content: center;
    margin-top: 6px;
}

.qty-label {
    font-size: 13px;
    color: #666;
    flex-shrink: 0;
}

.qty {
    width: 64px;
    padding: 6px;
    border-radius: 8px;
    border: 1.5px solid #ccc;
    text-align: center;
    font-size: 14px;
    outline: none;
    font-family: 'Segoe UI', sans-serif;
    transition: border-color 0.2s;
}

.qty:focus { border-color: #800000; }

/* ADD TO CART BUTTON */
.add-cart-btn {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 8px;
    background: #800000;
    color: white;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Segoe UI', sans-serif;
    transition: background 0.25s;
    margin-top: 8px;
}

.add-cart-btn:hover { background: #660000; }

.add-cart-btn:disabled,
.add-cart-btn.disabled-btn {
    background: #bbb;
    cursor: not-allowed;
}

/* form takes full width inside card */
.cart-form { width: 100%; }

/* ===== EMPTY STATE ===== */
.no-products {
    text-align: center;
    padding: 60px 20px;
    color: #888;
    font-size: 16px;
    grid-column: 1 / -1;
}

.no-products .no-icon {
    font-size: 52px;
    display: block;
    margin-bottom: 14px;
}

/* ===== LOGIN POPUP ===== */
#loginPrompt {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

#loginPrompt.show { display: flex; }

.prompt-box {
    background: white;
    padding: 40px 36px;
    border-radius: 20px;
    text-align: center;
    max-width: 350px;
    width: 90%;
    box-shadow: 0 16px 48px rgba(0,0,0,0.22);
    animation: popIn 0.22s ease;
}

@keyframes popIn {
    from { transform: scale(0.87); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}

.prompt-icon  { font-size: 46px; margin-bottom: 12px; }

.prompt-box h3 {
    color: #800000;
    font-size: 21px;
    margin-bottom: 8px;
}

.prompt-box p {
    color: #666;
    font-size: 14px;
    margin-bottom: 26px;
    line-height: 1.7;
}

.prompt-btns {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.prompt-login {
    padding: 11px 28px;
    border-radius: 22px;
    font-weight: bold;
    font-size: 14px;
    text-decoration: none;
    background: #800000;
    color: white;
    transition: 0.25s;
    border: none;
    cursor: pointer;
    font-family: 'Segoe UI', sans-serif;
}

.prompt-login:hover { background: #660000; }

.prompt-cancel {
    padding: 11px 22px;
    border-radius: 22px;
    font-weight: bold;
    font-size: 14px;
    background: #ffe6f2;
    color: #800000;
    border: none;
    cursor: pointer;
    font-family: 'Segoe UI', sans-serif;
    transition: 0.25s;
}

.prompt-cancel:hover { background: #ffccdd; }

/* ===== RESPONSIVE ===== */
@media (max-width: 900px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
        gap: 16px;
    }
}

@media (max-width: 640px) {
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
        padding: 12px 14px;
        gap: 10px;
    }
    .filter-bar form {
        width: 100%;
    }
    .filter-bar input[type="text"],
    .filter-bar select {
        min-width: unset;
        width: 100%;
        flex: 1;
    }
    .products-section { padding: 14px 10px; }
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .card-img     { height: 180px; }
    .card-body    { padding: 12px; gap: 4px; }
    .card-body h3 { font-size: 13px; min-height: 36px; }
    .price        { font-size: 15px; }
    .add-cart-btn { font-size: 13px; padding: 9px; }
}

@media (max-width: 360px) {
    .products-grid { grid-template-columns: 1fr; }
    .card-img { height: 220px; }
}

</style>
</head>
<body>

<?php include("navbar.php"); ?>

<!-- LOGIN POPUP -->
<div id="loginPrompt">
    <div class="prompt-box">
        <div class="prompt-icon">🔐</div>
        <h3>Login Required</h3>
        <p>For add product in cart<br>login required.</p>
        <div class="prompt-btns">
            <a href="../login.php" class="prompt-login">Login Now</a>
            <button onclick="closePrompt()" class="prompt-cancel">Cancel</button>
        </div>
    </div>
</div>

<!-- CATEGORIES -->
<div class="categories">
    <a href="dashboard.php" class="<?= ($cat_id == "") ? 'active' : '' ?>">All</a>
    <?php
    $cats = $conn->query("SELECT * FROM categories");
    while ($cat = $cats->fetch_assoc()):
    ?>
        <a href="?category=<?= $cat['id'] ?>&sort=<?= htmlspecialchars($sort) ?>&search=<?= htmlspecialchars($search) ?>"
           class="<?= ($cat_id == $cat['id']) ? 'active' : '' ?>">
            <?= htmlspecialchars($cat['name']) ?>
        </a>
    <?php endwhile; ?>
</div>

<!-- SEARCH + SORT -->
<div class="filter-bar">

    <form method="GET">
        <input type="hidden" name="category" value="<?= htmlspecialchars($cat_id) ?>">
        <input type="hidden" name="sort"     value="<?= htmlspecialchars($sort)   ?>">
        <input type="text" name="search"
               placeholder="🔍  Search products..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <form method="GET">
        <input type="hidden" name="category" value="<?= htmlspecialchars($cat_id) ?>">
        <input type="hidden" name="search"   value="<?= htmlspecialchars($search) ?>">
        <select name="sort">
            <option value="">Sort by Price</option>
            <option value="low"  <?= $sort == "low"  ? 'selected' : '' ?>>💰  Low → High</option>
            <option value="high" <?= $sort == "high" ? 'selected' : '' ?>>💎  High → Low</option>
        </select>
        <button type="submit">Apply</button>
    </form>

</div>

<!-- PRODUCTS -->
<div class="products-section">
<div class="products-grid">

<?php
$count = 0;
while ($row = $products->fetch_assoc()):
    $count++;
    $outOfStock = ($row['stock'] <= 0);
    $stockClass = $outOfStock ? "low" : "ok";
    $stockLabel = $outOfStock ? "Out of Stock" : "In Stock (" . $row['stock'] . ")";
?>

<div class="card">

    <img class="card-img"
         src="../uploads/<?= htmlspecialchars($row['image']) ?>"
         alt="<?= htmlspecialchars($row['name']) ?>"
         onerror="this.style.background='#ffe6f2'">

    <div class="card-body">

        <h3><?= htmlspecialchars($row['name']) ?></h3>

        <p class="price">Rs <?= number_format($row['price'], 0) ?></p>

        <span class="stock <?= $stockClass ?>"><?= $stockLabel ?></span>

        <!-- pushes button to bottom -->
        <div class="card-spacer"></div>

        <?php if ($outOfStock): ?>

            <button class="add-cart-btn disabled-btn" disabled>
                ❌ Out of Stock
            </button>

        <?php else: ?>

            <form method="POST" action="cart.php"
                  class="cart-form"
                  onsubmit="return checkLoginBeforeCart(event)">

                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">

                <div class="qty-wrap">
                    <span class="qty-label">Qty:</span>
                    <input type="number" name="quantity"
                           value="1" min="1" max="<?= $row['stock'] ?>"
                           class="qty">
                </div>

                <button type="submit" class="add-cart-btn">
                    🛒 Add to Cart
                </button>

            </form>

        <?php endif; ?>

    </div>
</div>

<?php endwhile; ?>

<?php if ($count === 0): ?>
    <div class="no-products">
        <span class="no-icon">😔</span>
        filter change
    </div>
<?php endif; ?>

</div>
</div>

<?php include("footer.php"); ?>

<script>
var isLoggedIn = <?= $is_logged_in ?>;

function checkLoginBeforeCart(event) {
    if (!isLoggedIn) {
        event.preventDefault();
        document.getElementById('loginPrompt').classList.add('show');
        return false;
    }
    return true;
}

function closePrompt() {
    document.getElementById('loginPrompt').classList.remove('show');
}

document.getElementById('loginPrompt').addEventListener('click', function(e) {
    if (e.target === this) closePrompt();
});
</script>

</body>
</html>