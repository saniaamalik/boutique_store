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

$products = $conn->query($query);
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
    color: #333;
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
}

/* ===== PRODUCTS GRID ===== */
.products-section {
    flex: 1;
    padding: 28px 24px;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 22px;
    max-width: 1300px;
    margin: 0 auto;
}

.card {
    background: #ffe6f2;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(128,0,0,0.09);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: 0.25s;
}

.card:hover { transform: translateY(-5px); }

.card-img {
    width: 100%;
    height: 240px;
    object-fit: cover;
}

.card-body {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex: 1;
    align-items: center;
    text-align: center;
}

.card-body h3 {
    font-size: 15px;
    color: #8b0000;
    min-height: 42px;
}

.price { color: #800000; font-weight: 700; font-size: 17px; }

.stock { font-size: 12px; padding: 3px 12px; border-radius: 12px; margin-bottom: 10px; }
.stock.ok  { color: #1a7a1a; background: #e6f9e6; }
.stock.low { color: #b30000; background: #ffe6e6; }

.qty {
    width: 60px;
    padding: 6px;
    margin-bottom: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
    text-align: center;
}

.add-cart-btn {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 8px;
    background: #800000;
    color: white;
    cursor: pointer;
    font-weight: 600;
}

.add-cart-btn:disabled { background: #bbb; cursor: not-allowed; }

@media (max-width: 640px) {
    .products-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .card-img { height: 160px; }
}
</style>
</head>
<body>

<?php include("navbar.php"); ?>

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

<div class="filter-bar">
    <form method="GET">
        <input type="text" name="search" placeholder="🔍 Search..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <form method="GET">
        <select name="sort">
            <option value="">Sort by Price</option>
            <option value="low"  <?= $sort == "low"  ? 'selected' : '' ?>>Low → High</option>
            <option value="high" <?= $sort == "high" ? 'selected' : '' ?>>High → Low</option>
        </select>
        <button type="submit">Apply</button>
    </form>
</div>

<div class="products-section">
<div class="products-grid">

<?php
while ($row = $products->fetch_assoc()):
    $outOfStock = ($row['stock'] <= 0);
?>
<div class="card">
    <img class="card-img" src="../uploads/<?= htmlspecialchars($row['image']) ?>">
    <div class="card-body">
        <h3><?= htmlspecialchars($row['name']) ?></h3>
        <p class="price">Rs <?= number_format($row['price'], 0) ?></p>
        <span class="stock <?= $outOfStock ? 'low' : 'ok' ?>">
            <?= $outOfStock ? "Out of Stock" : "In Stock (" . $row['stock'] . ")" ?>
        </span>

        <?php if (!$outOfStock): ?>
            <form method="POST" action="cart.php" style="width:100%;">
                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                <input type="number" name="quantity" value="1" min="1" max="<?= $row['stock'] ?>" class="qty">
                <button type="submit" class="add-cart-btn">🛒 Add to Cart</button>
            </form>
        <?php else: ?>
            <button class="add-cart-btn" disabled>❌ Out of Stock</button>
        <?php endif; ?>
    </div>
</div>
<?php endwhile; ?>

</div>
</div>

<?php include("footer.php"); ?>

</body>
</html>